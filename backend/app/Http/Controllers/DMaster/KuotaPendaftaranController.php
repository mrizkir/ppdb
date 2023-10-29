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
    ->get();

    return Response()->json([
      'status'=>1,
      'pid'=>'fetchdata',
      'kuota'=>$kuota,
      'message'=>'Fetch data kuota pendaftaran berhasil.'
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
  }
  /**
   * digunakan untuk mendapatkan daftar bulan berdasarkan awal semester
   */
  public function daftarbulan(Request $request,$id)
  {
    $ta=TAModel::find($id);         
    if (is_null($ta))
    {
      return Response()->json([
                  'status'=>1,
                  'pid'=>'update',                
                  'message'=>["Tahun Ajaran ($id) gagal diperoleh"]
                ],422); 
    }
    else
    {
      $awal_semester = $ta->awal_semester;
      $daftar_bulan=[];
      for($i=$awal_semester;$i<= 12;$i++)
      {
        $daftar_bulan[]=[
                  'value'=>$i,
                  'text'=>\App\Helpers\Helper::getNamaBulan($i)
                ];
      }
      for($i=1;$i<$awal_semester;$i++)
      {
        $daftar_bulan[]=[
                  'value'=>$i,
                  'text'=>\App\Helpers\Helper::getNamaBulan($i)
                ];
      }
      return Response()->json([
                    'status'=>1,
                    'pid'=>'fetchdata',
                    'ta'=>$ta,
                    'daftar_bulan'=>$daftar_bulan,
                    'message'=>'Fetch data daftar bulan berhasil.'
                  ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
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
    ],200);

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

    $ta = TAModel::find($id);
    if (is_null($ta))
    {
      return Response()->json([
                  'status'=>1,
                  'pid'=>'update',
                  'message'=>["Tahun Ajaran ($id) gagal diupdate"]
                ],422);
    }
    else
    {
      $this->validate($request, [
                    'tahun'=>[
                          'required',
                          'numeric',
                          Rule::unique('ta')->ignore($ta->tahun,'tahun')
                        ],
                    'tahun_ajaran'=>[
                            'required',
                            'string',
                            Rule::unique('ta')->ignore($ta->tahun_ajaran,'tahun_ajaran')
                          ],

                  ]);

      $ta->tahun = $request->input('tahun');
      $ta->tahun_ajaran = $request->input('tahun_ajaran');

      $ta->save();

      \App\Models\System\ActivityLog::log($request,[
                            'object' => $ta,
                            'object_id'=>$ta->tahun,
                            'user_id' => $this->guard()->user()->tahun,
                            'message' => 'Mengubah data tahun ajaran ('.$ta->tahun_ajaran.') berhasil'
                          ]);

      return Response()->json([
                  'status'=>1,
                  'pid'=>'update',
                  'ta'=>$ta,
                  'message'=>'Data tahun ajaran '.$ta->tahun_ajaran.' berhasil diubah.'
                ],200);
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

    $ta = TAModel::find($id);

    if (is_null($ta))
    {
      return Response()->json([
                  'status'=>1,
                  'pid'=>'destroy',
                  'message'=>["Kode tahun ajaran ($id) gagal dihapus"]
                ],422);
    }
    else
    {
      \App\Models\System\ActivityLog::log($request,[
                              'object' => $ta,
                              'object_id' => $ta->tahun,
                              'user_id' => $this->guard()->user()->id,
                              'message' => 'Menghapus Tahun Ajaran ('.$id.') berhasil'
                            ]);
      $ta->delete();
      return Response()->json([
                    'status'=>1,
                    'pid'=>'destroy',
                    'message'=>"Tahun Ajaran dengan kode ($id) berhasil dihapus"
                  ],200);
    }

  }
}
