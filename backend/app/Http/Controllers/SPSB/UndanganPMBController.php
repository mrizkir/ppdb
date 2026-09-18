<?php

namespace App\Http\Controllers\SPSB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SPSB\FormulirPendaftaranAModel;
use App\Models\SPSB\UndanganPMBModel;
use App\Models\DMaster\JenjangStudiModel;
use App\Models\Keuangan\KonfirmasiPembayaranModel;
use App\Models\System\ConfigurationModel;

use App\Helpers\HelperFormulir;
use Ramsey\Uuid\Uuid;
use Exception;

class UndanganPMBController extends Controller
{
  /**
   * Daftar undangan PMB untuk panitia.
   */
  public function index(Request $request)
  {
    $this->hasAnyPermission(['SPSB-PSB-FORMULIR-PENDAFTARAN_BROWSE', 'SPSB-PSB_STORE']);

    $this->validate($request, [
      'TA' => 'required',
      'kode_jenjang' => 'required',
    ]);

    $ta = $request->input('TA');
    $kode_jenjang = $request->input('kode_jenjang');

    $query = UndanganPMBModel::select(\DB::raw('
      undangan_pmb.id,
      undangan_pmb.user_id,
      undangan_pmb.formulir_id,
      undangan_pmb.otp,
      undangan_pmb.berlaku_mulai,
      undangan_pmb.berlaku_sampai,
      undangan_pmb.ta,
      undangan_pmb.kode_jenjang,
      undangan_pmb.used,
      undangan_pmb.created_at,
      undangan_pmb.updated_at,
      users.name,
      users.nomor_hp,
      users.code,
      users.email,
      users.active,
      formulir_pendaftaran_a.jk,
      formulir_pendaftaran_a.kode_jenjang,
      konfirmasi_pembayaran.transaksi_id AS konfirmasi_id,
      konfirmasi_pembayaran.verified
    '))
    ->join('users', 'undangan_pmb.user_id', 'users.id')
    ->join('formulir_pendaftaran_a', 'formulir_pendaftaran_a.id', 'undangan_pmb.formulir_id')
    ->leftJoin('konfirmasi_pembayaran', 'konfirmasi_pembayaran.user_id', 'users.id')
    ->where('undangan_pmb.ta', $ta)
    ->where('undangan_pmb.kode_jenjang', $kode_jenjang);

    if ($request->filled('used') && $request->input('used') !== '' && $request->input('used') !== 'all') {
      $query->where('undangan_pmb.used', (int) $request->input('used'));
    }

    $data = $query->orderBy('users.name', 'ASC')->get();

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'undangan' => $data,
      'message' => 'Fetch data undangan PMB berhasil diperoleh',
    ], 200);
  }

  /**
   * Undangan existing per calon (untuk dicetak ulang).
   */
  public function show(Request $request, $id)
  {
    $this->hasAnyPermission(['SPSB-PSB-FORMULIR-PENDAFTARAN_BROWSE', 'SPSB-PSB_STORE']);

    $user = User::find($id);
    if (is_null($user)) {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => 'Calon peserta didik tidak ditemukan.',
      ], 422);
    }

    $query = UndanganPMBModel::where('user_id', $id);
    if ($request->filled('ta')) {
      $query->where('ta', $request->input('ta'));
    }
    if ($request->filled('kode_jenjang')) {
      $query->where('kode_jenjang', $request->input('kode_jenjang'));
    }
    $undangan = $query->orderBy('ta', 'desc')->orderBy('created_at', 'desc')->first();

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'user' => [
        'id' => $user->id,
        'name' => $user->name,
        'nomor_hp' => $user->nomor_hp,
        'code' => $user->code,
      ],
      'undangan' => $undangan,
      'message' => is_null($undangan)
        ? 'Calon belum memiliki undangan PMB.'
        : 'Data undangan PMB berhasil diperoleh.',
    ], 200);
  }

  /**
   * Buat atau generate ulang undangan.
   */
  public function store(Request $request)
  {
    $this->hasAnyPermission(['SPSB-PSB-FORMULIR-PENDAFTARAN_BROWSE', 'SPSB-PSB_STORE']);

    $this->validate($request, [
      'user_id' => 'required|string|exists:users,id',
      'formulir_id' => 'nullable|string',
      'berlaku_mulai' => 'required|date_format:Y-m-d',
      'berlaku_sampai' => 'required|date_format:Y-m-d|after_or_equal:berlaku_mulai',
      'ta' => 'required|numeric',
      'kode_jenjang' => 'required|numeric|exists:jenjang_studi,kode_jenjang',
    ]);

    try {
      $undangan = \DB::transaction(function () use ($request) {
        $user = User::find($request->input('user_id'));
        $source = null;
        if ($request->filled('formulir_id')) {
          $source = FormulirPendaftaranAModel::where('id', $request->input('formulir_id'))
            ->where('user_id', $user->id)
            ->first();
        }
        if (is_null($source)) {
          $source = HelperFormulir::findA($user->id);
        }
        if (is_null($source)) {
          throw new Exception('Formulir pendaftaran calon tidak ditemukan.');
        }

        $ta = $request->input('ta');
        $kode_jenjang = $request->input('kode_jenjang');
        if ((int) $source->ta !== (int) $ta || (int) $source->kode_jenjang !== (int) $kode_jenjang) {
          $formulir = HelperFormulir::cloneToJenjang($source, $ta, $kode_jenjang);
        } else {
          $formulir = $source;
        }

        $otp = $this->generateOtp($ta);
        $now = \Carbon\Carbon::now()->toDateTimeString();
        $createdBy = $this->guard()->user() ? $this->guard()->user()->id : null;

        $undangan = UndanganPMBModel::where('formulir_id', $formulir->id)->first();
        if (is_null($undangan)) {
          $undangan = UndanganPMBModel::create([
            'id' => Uuid::uuid4()->toString(),
            'user_id' => $user->id,
            'formulir_id' => $formulir->id,
            'otp' => $otp,
            'berlaku_mulai' => $request->input('berlaku_mulai'),
            'berlaku_sampai' => $request->input('berlaku_sampai'),
            'ta' => $ta,
            'kode_jenjang' => $kode_jenjang,
            'used' => 0,
            'created_by' => $createdBy,
            'created_at' => $now,
            'updated_at' => $now,
          ]);
        } else {
          $undangan->otp = $otp;
          $undangan->berlaku_mulai = $request->input('berlaku_mulai');
          $undangan->berlaku_sampai = $request->input('berlaku_sampai');
          $undangan->ta = $ta;
          $undangan->kode_jenjang = $kode_jenjang;
          $undangan->used = 0;
          $undangan->created_by = $createdBy;
          $undangan->updated_at = $now;
          $undangan->save();
        }

        $this->ensureKodeTransfer($user);
        $this->requireBiayaPendaftaran($undangan);

        return $undangan;
      });

      $user = User::find($undangan->user_id);

      \App\Models\System\ActivityLog::log($request, [
        'object' => $undangan,
        'object_id' => $undangan->id,
        'user_id' => $this->getUserid(),
        'message' => 'Membuat/memperbarui undangan PMB untuk '.$user->name,
      ]);

      return Response()->json([
        'status' => 1,
        'pid' => 'store',
        'undangan' => $undangan,
        'user' => [
          'id' => $user->id,
          'name' => $user->name,
          'nomor_hp' => $user->nomor_hp,
          'code' => $user->code,
        ],
        'message' => 'Link undangan PMB berhasil dibuat.',
      ], 200);
    } catch (Exception $e) {
      return Response()->json([
        'status' => 0,
        'pid' => 'store',
        'message' => $e->getMessage(),
      ], 422);
    }
  }

  /**
   * Preview publik link undangan (tanpa mengembalikan OTP).
   */
  public function preview(Request $request, $otp)
  {
    $undangan = $this->findUndanganByOtp($otp);
    if (is_null($undangan)) {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => 'Link undangan tidak valid.',
      ], 422);
    }

    $user = User::find($undangan->user_id);
    $konfirmasi = $this->getKonfirmasi($undangan->user_id);
    $status = $this->statusUndangan($undangan, $konfirmasi);
    $jenjang = $this->namaJenjangUndangan($undangan);

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'undangan' => [
        'name' => $user->name,
        'ta' => $undangan->ta,
        'kode_jenjang' => $undangan->kode_jenjang,
        'nama_jenjang' => $jenjang,
        'berlaku_mulai' => $undangan->berlaku_mulai,
        'berlaku_sampai' => $undangan->berlaku_sampai,
        'status' => $status,
      ],
      'message' => 'Data undangan berhasil diperoleh.',
    ], 200);
  }

  /**
   * Verifikasi OTP publik.
   */
  public function verify(Request $request, $otp)
  {
    $this->validate($request, [
      'otp' => 'required|numeric',
    ]);

    $undangan = $this->findUndanganByOtp($otp);
    if (is_null($undangan) || (int) $request->input('otp') !== (int) $undangan->otp) {
      return Response()->json([
        'status' => 0,
        'pid' => 'update',
        'message' => 'Kode OTP tidak valid.',
      ], 422);
    }

    $user = User::find($undangan->user_id);
    if (is_null($user) || (int) $user->active !== 1) {
      return Response()->json([
        'status' => 0,
        'pid' => 'update',
        'message' => 'Akun calon peserta didik belum aktif.',
      ], 422);
    }

    $konfirmasi = $this->getKonfirmasi($undangan->user_id);
    $status = $this->statusUndangan($undangan, $konfirmasi);

    if ($status === 'sudah_diverifikasi') {
      return $this->loginFromUndangan($request, $user, $undangan);
    }

    if ($status === 'menunggu_verifikasi') {
      return Response()->json([
        'status' => 1,
        'pid' => 'update',
        'need_payment' => false,
        'waiting' => true,
        'message' => 'Bukti pembayaran sudah diterima. Silahkan menunggu verifikasi panitia sekolah.',
      ], 200);
    }

    if ($status === 'belum_mulai') {
      return Response()->json([
        'status' => 0,
        'pid' => 'update',
        'message' => 'Link undangan belum berlaku.',
      ], 422);
    }
    if ($status === 'kadaluarsa') {
      return Response()->json([
        'status' => 0,
        'pid' => 'update',
        'message' => 'Link undangan sudah kadaluarsa.',
      ], 422);
    }

    if (!$undangan->used) {
      $undangan->used = 1;
      $undangan->save();
    }

    if (is_null($konfirmasi)) {
      try {
        $this->ensureKodeTransfer($user);
        $kombi = $this->requireBiayaPendaftaran($undangan);
      } catch (Exception $e) {
        return Response()->json([
          'status' => 0,
          'pid' => 'update',
          'message' => $e->getMessage(),
        ], 422);
      }

      $biaya = (int) $kombi->biaya;
      $code = (int) $user->code;

      return Response()->json([
        'status' => 1,
        'pid' => 'update',
        'need_payment' => true,
        'user' => [
          'id' => $user->id,
          'name' => $user->name,
          'nomor_hp' => $user->nomor_hp,
          'email' => $user->email,
          'username' => $user->username,
          'code' => $code,
          'biaya' => $biaya,
          'total_transfer' => $biaya + $code,
          'ta' => $undangan->ta,
          'kode_jenjang' => $undangan->kode_jenjang,
          'nama_jenjang' => $this->namaJenjangUndangan($undangan),
        ],
        'message' => 'OTP benar. Silahkan mengisi konfirmasi pembayaran.',
      ], 200);
    }

    return Response()->json([
      'status' => 1,
      'pid' => 'update',
      'need_payment' => false,
      'waiting' => true,
      'message' => 'Bukti pembayaran sudah diterima. Silahkan menunggu verifikasi panitia sekolah.',
    ], 200);
  }

  /**
   * Login JWT setelah OTP dipakai dan pembayaran diverifikasi panitia.
   */
  public function masuk(Request $request, $otp)
  {
    $this->validate($request, [
      'otp' => 'required|numeric',
    ]);

    $undangan = $this->findUndanganByOtp($otp);
    if (is_null($undangan) || (int) $request->input('otp') !== (int) $undangan->otp) {
      return Response()->json([
        'status' => 0,
        'pid' => 'update',
        'message' => 'Kode OTP tidak valid.',
      ], 422);
    }

    $user = User::find($undangan->user_id);
    $konfirmasi = $this->getKonfirmasi($undangan->user_id);

    if (!$undangan->used || is_null($konfirmasi)) {
      return Response()->json([
        'status' => 0,
        'pid' => 'update',
        'message' => 'Konfirmasi pembayaran belum diterima.',
      ], 422);
    }

    if ((int) $konfirmasi->verified !== 1) {
      return Response()->json([
        'status' => 0,
        'pid' => 'update',
        'waiting' => true,
        'message' => 'Bukti pembayaran sudah diterima. Silahkan menunggu verifikasi panitia sekolah.',
      ], 422);
    }

    if (is_null($user) || (int) $user->active !== 1) {
      return Response()->json([
        'status' => 0,
        'pid' => 'update',
        'message' => 'Akun calon peserta didik belum aktif.',
      ], 422);
    }

    return $this->loginFromUndangan($request, $user, $undangan);
  }

  private function findUndanganByOtp($otp)
  {
    return UndanganPMBModel::where('otp', (int) $otp)->first();
  }

  private function getKonfirmasi($userId)
  {
    return KonfirmasiPembayaranModel::where('user_id', $userId)->first();
  }

  private function namaJenjangUndangan($undangan)
  {
    $jenjang = JenjangStudiModel::find($undangan->kode_jenjang);
    return $jenjang ? $jenjang->nama_jenjang : null;
  }

  private function statusUndangan($undangan, $konfirmasi)
  {
    if ($konfirmasi && (int) $konfirmasi->verified === 1) {
      return 'sudah_diverifikasi';
    }
    if ($konfirmasi) {
      return 'menunggu_verifikasi';
    }
    $today = \Carbon\Carbon::today()->toDateString();
    if ($today < $undangan->berlaku_mulai) {
      return 'belum_mulai';
    }
    if ($today > $undangan->berlaku_sampai) {
      return 'kadaluarsa';
    }
    return 'berlaku';
  }

  private function generateOtp($ta)
  {
    $ta = (int) $ta;
    if ($ta <= 0) {
      $ta = (int) date('Y');
    }
    $prefix = $ta % 100;
    $attempts = 0;
    do {
      $otp = ($prefix * 10000) + mt_rand(0, 9999);
      $exists = UndanganPMBModel::where('otp', $otp)->exists();
      $attempts++;
    } while ($exists && $attempts < 50);

    if ($exists) {
      throw new Exception('Gagal membuat kode OTP unik. Silahkan coba lagi.');
    }

    return $otp;
  }

  private function requireBiayaPendaftaran(UndanganPMBModel $undangan)
  {
    $kombi = $undangan->biayaPendaftaran();
    if (is_null($kombi)) {
      throw new Exception("Biaya pendaftaran jenjang pendidikan ({$undangan->kode_jenjang}) tahun {$undangan->ta} belum ditentukan oleh Admin.");
    }

    return $kombi;
  }

  private function ensureKodeTransfer(User $user)
  {
    $code = (int) $user->code;
    if ($code >= 1000 && $code <= 9999) {
      return $user;
    }

    $attempts = 0;
    do {
      $code = mt_rand(1000, 9999);
      $exists = User::where('code', $code)->where('id', '!=', $user->id)->exists();
      $attempts++;
    } while ($exists && $attempts < 50);

    $user->code = $code;
    $user->save();

    return $user;
  }

  private function loginFromUndangan(Request $request, User $user, UndanganPMBModel $undangan)
  {
    $token = $this->guard()->login($user);
    ConfigurationModel::toCache();

    \App\Models\System\ActivityLog::log($request, [
      'object' => $user,
      'object_id' => $user->id,
      'user_id' => $user->id,
      'message' => 'user '.$user->username.' berhasil login via undangan PMB (otp '.$undangan->otp.')',
    ]);

    return Response()->json([
      'status' => 1,
      'pid' => 'update',
      'need_payment' => false,
      'access_token' => $token,
      'token_type' => 'bearer',
      'expires_in' => $this->guard()->factory()->getTTL() * 60,
      'message' => 'Login undangan PMB berhasil.',
    ], 200);
  }
}
