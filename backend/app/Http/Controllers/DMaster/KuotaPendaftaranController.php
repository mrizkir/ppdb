<?php

namespace App\Http\Controllers\DMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\DMaster\KuotaPendaftaranModel;
use App\Models\DMaster\TAModel;
use App\Models\DMaster\JenjangStudiModel;

use Ramsey\Uuid\Uuid;

class KuotaPendaftaranController extends Controller {
  /**
   * daftar tahun ajaran
   */
  public function index(Request $request)
  {
    $kuota = KuotaPendaftaranModel::from('kuota_pendaftaran AS a')
    ->select(\DB::raw('
      a.*,
      ta.tahun_ajaran,
      c.nama_jenjang
    '))
    ->join('ta', 'ta.tahun', 'a.tahun')
    ->join('jenjang_studi AS c', 'c.kode_jenjang', 'a.kode_jenjang')
    ->orderBy('a.tahun', 'desc')
    ->orderBy('a.kode_jenjang', 'asc')
    ->get();

    return Response()->json([
      'status'=>1,
      'pid'=>'fetchdata',
      'kuota'=>$kuota,
      'message'=>'Fetch data kuota pendaftaran berhasil.'
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
  }  
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $this->hasPermissionTo('DMASTER-TA_STORE');

    $rule=[
      'ta'=>'required|numeric',      
      'kode_jenjang'=>'required|numeric',      
      'kuota_l'=>'required|numeric',      
      'kuota_p'=>'required|numeric',      
    ];

    $this->validate($request, $rule);

    $kuota = KuotaPendaftaranModel::create([
      'id' => Uuid::uuid4()->toString(),
      'tahun' => $request->input('ta'),      
      'kode_jenjang' => $request->input('kode_jenjang'),
      'kuota_l' => $request->input('kuota_l'),
      'kuota_p' => $request->input('kuota_p'),
    ]);

    \App\Models\System\ActivityLog::log($request,[
      'object' => $kuota,
      'object_id'=>$kuota->id,
      'user_id' => $this->guard()->user()->id,
      'message' => 'Menambah kuota pendaftaran ajaran baru berhasil'
    ]);

    return Response()->json([
      'status'=>1,
      'pid'=>'store',
      'kuota'=>$kuota,
      'message'=>'Data kuota pendaftaran berhasil disimpan.'
    ], 200);

  }
  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $this->hasPermissionTo('DMASTER-TA_UPDATE');

    $kuota = KuotaPendaftaranModel::find($id);
    if (is_null($kuota))
    {
      return Response()->json([
        'status'=>1,
        'pid'=>'update',
        'message'=>["Tahun Ajaran ($id) gagal diupdate"]
      ],422);
    }
    else
    {
      $rule=[
        'ta'=>'required|numeric',      
        'kode_jenjang'=>'required|numeric',      
        'kuota_l'=>'required|numeric',      
        'kuota_p'=>'required|numeric',      
      ];
  
      $this->validate($request, $rule);

      $kuota->tahun = $request->input('ta');
      $kuota->kode_jenjang = $request->input('kode_jenjang');
      $kuota->kuota_l = $request->input('kuota_l');
      $kuota->kuota_p = $request->input('kuota_p');

      $kuota->save();

      \App\Models\System\ActivityLog::log($request,[
        'object' => $kuota,
        'object_id'=>$kuota->id,
        'user_id' => $this->guard()->user()->id,
        'message' => 'Mengubah data kuota ('.$kuota->tahun.') berhasil'
      ]);

      return Response()->json([
        'status'=>1,
        'pid'=>'update',
        'kuota'=>$kuota,
        'message'=>'Data kuota pendaftaran siswa tahun '.$kuota->tahun.' berhasil diubah.'
      ], 200);
    }
  }
  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request,$id)
  {
    $this->hasPermissionTo('DMASTER-TA_DESTROY');

    $kuota = KuotaPendaftaranModel::find($id);

    if (is_null($kuota))
    {
      return Response()->json([
        'status'=>1,
        'pid'=>'destroy',
        'message'=>["Kode kuota pendaftaran ($id) gagal dihapus"]
      ], 422);
    }
    else
    {
      \App\Models\System\ActivityLog::log($request,[
        'object' => $kuota,
        'object_id' => $kuota->id,
        'user_id' => $this->guard()->user()->id,
        'message' => 'Menghapus Kuota Pendaftaran ('.$id.') berhasil'
      ]);

      $kuota->delete();

      return Response()->json([
        'status'=>1,
        'pid'=>'destroy',
        'message'=>"Tahun Ajaran dengan kode ($id) berhasil dihapus"
      ], 200);
    }

  }
}
