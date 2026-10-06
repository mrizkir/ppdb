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

use App\Helpers\Helper;
use App\Helpers\HelperFormulir;
use Ramsey\Uuid\Uuid;
use Exception;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output\Png;

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
      undangan_pmb.nomor_surat,
      undangan_pmb.used,
      undangan_pmb.created_at,
      undangan_pmb.updated_at,
      users.name,
      users.nomor_hp,
      COALESCE(formulir_pendaftaran_a.nominal_transfer, users.code) AS code,
      users.email,
      users.active,
      formulir_pendaftaran_a.jk,
      formulir_pendaftaran_a.kode_jenjang,
      konfirmasi_pembayaran.transaksi_id AS konfirmasi_id,
      konfirmasi_pembayaran.verified
    '))
    ->join('users', 'undangan_pmb.user_id', 'users.id')
    ->join('formulir_pendaftaran_a', 'formulir_pendaftaran_a.id', 'undangan_pmb.formulir_id')
    ->leftJoin('konfirmasi_pembayaran', function ($join) {
      $join->on('konfirmasi_pembayaran.user_id', '=', 'users.id')
        ->on('konfirmasi_pembayaran.formulir_id', '=', 'undangan_pmb.formulir_id');
    })
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
    $nominal = null;
    if ($undangan && $undangan->formulir_id) {
      $nominal = FormulirPendaftaranAModel::where('id', $undangan->formulir_id)->value('nominal_transfer');
    }

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'user' => [
        'id' => $user->id,
        'name' => $user->name,
        'nomor_hp' => $user->nomor_hp,
        'code' => $nominal,
      ],
      'undangan' => $undangan,
      'message' => is_null($undangan)
        ? 'Calon belum memiliki undangan PMB.'
        : 'Data undangan PMB berhasil diperoleh.',
    ], 200);
  }

  /**
   * Buat atau perbarui undangan. OTP yang sudah ada dipakai ulang
   * selama tahun pendaftaran tidak berubah; masa berlaku mengikuti
   * berlaku_mulai s.d. berlaku_sampai.
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

        $now = \Carbon\Carbon::now()->toDateTimeString();
        $createdBy = $this->guard()->user() ? $this->guard()->user()->id : null;

        $undangan = UndanganPMBModel::where('formulir_id', $formulir->id)->first();
        if (is_null($undangan)) {
          $undangan = UndanganPMBModel::create([
            'id' => Uuid::uuid4()->toString(),
            'user_id' => $user->id,
            'formulir_id' => $formulir->id,
            'otp' => $this->generateOtp($ta),
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
          $keepOtp = (int) $undangan->ta === (int) $ta && !empty($undangan->otp);
          $gantiPaket = (int) $undangan->ta !== (int) $ta || (int) $undangan->kode_jenjang !== (int) $kode_jenjang;
          $undangan->otp = $keepOtp ? $undangan->otp : $this->generateOtp($ta, $undangan->id);
          $undangan->berlaku_mulai = $request->input('berlaku_mulai');
          $undangan->berlaku_sampai = $request->input('berlaku_sampai');
          $undangan->ta = $ta;
          $undangan->kode_jenjang = $kode_jenjang;
          if ($gantiPaket) {
            $undangan->nominal_transfer = null;
            $formulir->nominal_transfer = null;
            $formulir->save();
          }
          if (!$keepOtp) {
            $undangan->used = 0;
          }
          $undangan->created_by = $createdBy;
          $undangan->updated_at = $now;
          $undangan->save();
        }

        $kombi = $this->requireBiayaPendaftaran($undangan);
        $nominal = HelperFormulir::ensureNominal($formulir, (int) $kombi->biaya);
        if ((int) $undangan->nominal_transfer !== $nominal) {
          $undangan->nominal_transfer = $nominal;
          $undangan->save();
        }

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
          'code' => $undangan->nominal_transfer,
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
   * Hapus undangan beserta paket formulir A-F yang formulir_id, ta, dan kode_jenjang-nya sama.
   */
  public function destroy(Request $request, $id)
  {
    $this->hasAnyPermission(['SPSB-PSB-FORMULIR-PENDAFTARAN_BROWSE', 'SPSB-PSB_STORE', 'SPSB-PSB_DESTROY']);

    $undangan = UndanganPMBModel::find($id);
    if (is_null($undangan)) {
      return Response()->json([
        'status' => 0,
        'pid' => 'destroy',
        'message' => "Undangan PMB dengan ID ($id) gagal dihapus.",
      ], 422);
    }

    try {
      $nama = optional(User::find($undangan->user_id))->name;
      \DB::transaction(function () use ($undangan) {
        $formulir = FormulirPendaftaranAModel::where('id', $undangan->formulir_id)
          ->where('ta', $undangan->ta)
          ->where('kode_jenjang', $undangan->kode_jenjang)
          ->first();

        $undangan->delete();

        if ($formulir) {
          HelperFormulir::deletePaket($formulir->id);
        }
      });

      \App\Models\System\ActivityLog::log($request, [
        'object' => $this->guard()->user(),
        'object_id' => $this->getUserid(),
        'user_id' => $this->getUserid(),
        'message' => 'Menghapus undangan PMB'.($nama ? ' untuk '.$nama : ''),
      ]);

      return Response()->json([
        'status' => 1,
        'pid' => 'destroy',
        'message' => 'Undangan PMB'.($nama ? ' untuk '.$nama : '').' berhasil dihapus.',
      ], 200);
    } catch (Exception $e) {
      return Response()->json([
        'status' => 0,
        'pid' => 'destroy',
        'message' => $e->getMessage(),
      ], 422);
    }
  }

  /**
   * Simpan nomor surat lalu buat PDF surat pemberitahuan.
   */
  public function cetak(Request $request)
  {
    $this->hasAnyPermission(['SPSB-PSB-FORMULIR-PENDAFTARAN_BROWSE', 'SPSB-PSB_STORE']);

    $this->validate($request, [
      'id' => 'required|string|exists:undangan_pmb,id',
      'nomor_surat' => 'required|string|max:50',
      'link' => 'required|url|max:255',
    ]);

    $undangan = UndanganPMBModel::find($request->input('id'));
    $user = User::find($undangan->user_id);
    if (is_null($user)) {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => 'Calon peserta didik undangan tidak ditemukan.',
      ], 422);
    }

    $nomorSurat = trim($request->input('nomor_surat'));
    $undangan->nomor_surat = $nomorSurat;
    $undangan->save();

    $link = $request->input('link');
    $qr = new QrCode($link);
    $qrPng = (new Png())->output($qr, 180);

    $ta = (int) $undangan->ta;
    $sekarang = \Carbon\Carbon::now('Asia/Jakarta');
    $hijri = $this->tanggalHijriah($sekarang);
    $jenjang = $this->labelJenjangSingkat($undangan->kode_jenjang);

    try {
      $pdf = \Mccarlosen\LaravelMpdf\Facades\LaravelMpdf::loadView(
        'report.SuratPemberitahuan',
        [
          'nomor_surat' => $nomorSurat,
          'nama' => $user->name,
          'jenjang' => $jenjang,
          'ta' => $ta,
          'ta_berikut' => $ta + 1,
          'tanggal_masehi' => $sekarang->format('d').' '.$this->namaBulanMasehi((int) $sekarang->format('n')).' '.$sekarang->format('Y').' M',
          'tanggal_hijriah' => $hijri['tanggal'].' '.$hijri['bulan'].' '.$hijri['tahun'].' H',
          'link' => $link,
          'qr_base64' => base64_encode($qrPng),
        ],
        [],
        [
          'title' => 'Surat Pemberitahuan PMB',
          'format' => 'A4',
          'margin_left' => 15,
          'margin_right' => 15,
          'margin_top' => 12,
          'margin_bottom' => 12,
        ]
      );

      $folder = Helper::public_path('exported/pdf');
      if (!is_dir($folder)) {
        mkdir($folder, 0755, true);
      }
      $filename = 'surat-pmb-'.$undangan->id.'.pdf';
      $pdf->save($folder.'/'.$filename);
    } catch (Exception $e) {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',
        'message' => 'Surat pemberitahuan gagal dibuat.',
      ], 422);
    }

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'nomor_surat' => $nomorSurat,
      'pdf_file' => 'exported/pdf/'.$filename,
      'message' => 'Surat pemberitahuan berhasil dibuat.',
    ], 200);
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
    $konfirmasi = $this->getKonfirmasi($undangan);
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
   * OTP yang sama boleh diisi berulang selama masa berlaku.
   * Jika bukti bayar belum ada, selalu tampilkan pembayaran yang sama.
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

    if ($undangan->isBelumMulai()) {
      return Response()->json([
        'status' => 0,
        'pid' => 'update',
        'message' => 'Link undangan belum berlaku.',
      ], 422);
    }
    if (!$undangan->isBerlaku()) {
      return Response()->json([
        'status' => 0,
        'pid' => 'update',
        'message' => 'Link undangan sudah kadaluarsa.',
      ], 422);
    }

    $this->markUsed($undangan);
    $konfirmasi = $this->getKonfirmasi($undangan);

    if (is_null($konfirmasi)) {
      return $this->paymentFromUndangan($user, $undangan);
    }

    if ((int) $konfirmasi->verified !== 1) {
      return Response()->json([
        'status' => 1,
        'pid' => 'update',
        'need_payment' => false,
        'waiting' => true,
        'message' => 'Bukti pembayaran sudah diterima. Silahkan menunggu verifikasi panitia sekolah.',
      ], 200);
    }

    return $this->loginFromUndangan($request, $user, $undangan);
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
    if (!$undangan->isBerlaku()) {
      $message = $undangan->isBelumMulai()
        ? 'Link undangan belum berlaku.'
        : 'Link undangan sudah kadaluarsa.';
      return Response()->json([
        'status' => 0,
        'pid' => 'update',
        'message' => $message,
      ], 422);
    }

    $konfirmasi = $this->getKonfirmasi($undangan);

    if (is_null($konfirmasi)) {
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

  private function markUsed(UndanganPMBModel $undangan)
  {
    if ($undangan->used) {
      return;
    }
    $undangan->used = 1;
    $undangan->save();
  }

  private function getKonfirmasi(UndanganPMBModel $undangan)
  {
    if (empty($undangan->formulir_id)) {
      return null;
    }

    return KonfirmasiPembayaranModel::where('user_id', $undangan->user_id)
      ->where('formulir_id', $undangan->formulir_id)
      ->whereNotNull('transaksi_id')
      ->first();
  }

  private function labelJenjangSingkat($kode)
  {
    $map = [
      1 => 'TK',
      2 => 'SD',
      3 => 'SMP',
      4 => 'SMA',
    ];

    return isset($map[(int) $kode]) ? $map[(int) $kode] : '';
  }

  private function namaBulanMasehi($bulan)
  {
    $nama = [
      1 => 'Januari',
      2 => 'Februari',
      3 => 'Maret',
      4 => 'April',
      5 => 'Mei',
      6 => 'Juni',
      7 => 'Juli',
      8 => 'Agustus',
      9 => 'September',
      10 => 'Oktober',
      11 => 'November',
      12 => 'Desember',
    ];

    return isset($nama[$bulan]) ? $nama[$bulan] : '';
  }

  private function tanggalHijriah(\Carbon\Carbon $tanggal)
  {
    $bulan = [
      1 => 'Muharram',
      2 => 'Safar',
      3 => 'Rabiul Awal',
      4 => 'Rabiul Akhir',
      5 => 'Jumadil Awal',
      6 => 'Jumadil Akhir',
      7 => 'Rajab',
      8 => 'Syaban',
      9 => 'Ramadan',
      10 => 'Syawal',
      11 => 'Zulkaidah',
      12 => 'Zulhijah',
    ];
    $jd = $this->julianDay((int) $tanggal->format('n'), (int) $tanggal->format('j'), (int) $tanggal->format('Y'));
    $l = $jd - 1948440 + 10632;
    $n = intdiv($l - 1, 10631);
    $l = $l - 10631 * $n + 354;
    $j = intdiv(10985 - $l, 5316) * intdiv(50 * $l, 17719) + intdiv($l, 5670) * intdiv(43 * $l, 15238);
    $l = $l - intdiv(30 - $j, 15) * intdiv(17719 * $j, 50) - intdiv($j, 16) * intdiv(15238 * $j, 43) + 29;
    $bulanHijriah = intdiv(24 * $l, 709);
    $hari = $l - intdiv(709 * $bulanHijriah, 24);
    $tahun = 30 * $n + $j - 30;

    return [
      'tanggal' => $hari,
      'bulan' => isset($bulan[$bulanHijriah]) ? $bulan[$bulanHijriah] : '',
      'tahun' => $tahun,
    ];
  }

  private function julianDay($month, $day, $year)
  {
    $a = intdiv(14 - $month, 12);
    $y = $year + 4800 - $a;
    $m = $month + 12 * $a - 3;

    return $day + intdiv(153 * $m + 2, 5) + 365 * $y + intdiv($y, 4) - intdiv($y, 100) + intdiv($y, 400) - 32045;
  }

  private function namaJenjangUndangan($undangan)
  {
    $jenjang = JenjangStudiModel::find($undangan->kode_jenjang);
    return $jenjang ? $jenjang->nama_jenjang : null;
  }

  private function statusUndangan($undangan, $konfirmasi)
  {
    if ($undangan->isBelumMulai()) {
      return 'belum_mulai';
    }
    if (!$undangan->isBerlaku()) {
      return 'kadaluarsa';
    }
    if ($konfirmasi && $konfirmasi->transaksi_id && (int) $konfirmasi->verified === 1) {
      return 'sudah_diverifikasi';
    }
    if ($konfirmasi && $konfirmasi->transaksi_id) {
      return 'menunggu_verifikasi';
    }
    return 'berlaku';
  }

  private function generateOtp($ta, $exceptId = null)
  {
    $ta = (int) $ta;
    if ($ta <= 0) {
      $ta = (int) date('Y');
    }
    $prefix = $ta % 100;
    $attempts = 0;
    do {
      $otp = ($prefix * 10000) + mt_rand(0, 9999);
      $query = UndanganPMBModel::where('otp', $otp);
      if ($exceptId) {
        $query->where('id', '!=', $exceptId);
      }
      $exists = $query->exists();
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

  /**
   * Tampilkan data pembayaran yang sama setiap OTP diisi ulang.
   * Nominal dibaca dari formulir tahun dan jenjang undangan ini.
   */
  private function paymentFromUndangan(User $user, UndanganPMBModel $undangan)
  {
    try {
      $kombi = $this->requireBiayaPendaftaran($undangan);
    } catch (Exception $e) {
      return Response()->json([
        'status' => 0,
        'pid' => 'update',
        'message' => $e->getMessage(),
      ], 422);
    }

    $formulir = FormulirPendaftaranAModel::find($undangan->formulir_id);
    if (is_null($formulir)) {
      return Response()->json([
        'status' => 0,
        'pid' => 'update',
        'message' => 'Formulir pendaftaran undangan tidak ditemukan.',
      ], 422);
    }

    $biaya = (int) $kombi->biaya;
    $nominal = HelperFormulir::ensureNominal($formulir, $biaya);
    if ((int) $undangan->nominal_transfer !== $nominal) {
      $undangan->nominal_transfer = $nominal;
      $undangan->save();
    }

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
        'code' => $nominal - $biaya,
        'biaya' => $biaya,
        'total_transfer' => $nominal,
        'ta' => $undangan->ta,
        'kode_jenjang' => $undangan->kode_jenjang,
        'formulir_id' => $undangan->formulir_id,
        'nama_jenjang' => $this->namaJenjangUndangan($undangan),
      ],
      'message' => 'OTP benar. Silahkan mengisi konfirmasi pembayaran.',
    ], 200);
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
