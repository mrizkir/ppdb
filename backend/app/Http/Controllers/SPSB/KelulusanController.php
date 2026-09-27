<?php

namespace App\Http\Controllers\SPSB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SPSB\FormulirPendaftaranAModel;
use App\Models\SPSB\KelulusanPMBModel;
use Ramsey\Uuid\Uuid;

class KelulusanController extends Controller
{
  /**
   * Daftar calon untuk dinyatakan lulus atau tidak lulus.
   */
  public function index(Request $request)
  {
    $this->hasAnyPermission(['SPSB-PSB-FORMULIR-PENDAFTARAN_BROWSE', 'SPSB-PSB_STORE', 'SPSB-PSB_BROWSE']);

    $this->validate($request, [
      'TA' => 'required',
      'kode_jenjang' => 'required',
    ]);

    $data = KelulusanPMBModel::queryDaftar(
      $request->input('TA'),
      $request->input('kode_jenjang'),
      'all'
    )->get();

    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'psb' => $data,
      'message' => 'Fetch data kelulusan berhasil diperoleh',
    ], 200);
  }

  /**
   * Simpan keputusan panitia untuk satu formulir.
   */
  public function nyatakan(Request $request)
  {
    $this->hasAnyPermission(['SPSB-PSB-FORMULIR-PENDAFTARAN_BROWSE', 'SPSB-PSB_STORE', 'SPSB-PSB_BROWSE']);

    $this->validate($request, [
      'formulir_id' => 'required',
      'ket_lulus' => 'required|in:0,1',
    ]);

    $formulir = FormulirPendaftaranAModel::find($request->input('formulir_id'));
    if (is_null($formulir)) {
      return Response()->json([
        'status' => 0,
        'pid' => 'update',
        'message' => ['Formulir pendaftaran tidak ditemukan.'],
      ], 422);
    }

    $ketLulus = (int) $request->input('ket_lulus');
    $kelulusan = KelulusanPMBModel::where('formulir_id', $formulir->id)->first();
    if (is_null($kelulusan)) {
      $kelulusan = new KelulusanPMBModel();
      $kelulusan->id = Uuid::uuid4()->toString();
      $kelulusan->formulir_id = $formulir->id;
    }

    $kelulusan->user_id = $formulir->user_id;
    $kelulusan->ta = $formulir->ta;
    $kelulusan->kode_jenjang = $formulir->kode_jenjang;
    $kelulusan->ket_lulus = $ketLulus;
    $kelulusan->decided_by = $this->getUserid();
    $kelulusan->save();

    $label = $ketLulus === 1 ? 'LULUS' : 'TIDAK LULUS';

    \App\Models\System\ActivityLog::log($request, [
      'object' => $kelulusan,
      'object_id' => $kelulusan->id,
      'user_id' => $this->getUserid(),
      'message' => 'Menyatakan kelulusan '.$label.' untuk formulir '.$formulir->id.' berhasil dilakukan',
    ]);

    return Response()->json([
      'status' => 1,
      'pid' => 'update',
      'kelulusan' => $kelulusan,
      'message' => 'Kelulusan dinyatakan '.$label.'.',
    ], 200);
  }
}
