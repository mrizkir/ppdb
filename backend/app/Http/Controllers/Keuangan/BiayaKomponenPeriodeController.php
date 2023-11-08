<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Helpers\Helper;
use App\Models\DMaster\KelasModel;
use App\Models\Keuangan\KomponenBiayaModel;
use App\Models\Keuangan\BiayaKomponenPeriodeModel;

class BiayaKomponenPeriodeController extends Controller {  
  /**
   * daftar komponen biaya per periode
   */
  public function index(Request $request)
  {
    $this->hasPermissionTo('KEUANGAN-BIAYA-KOMPONEN-PERIODE_BROWSE');
    
    $this->validate($request, [           
      'ta'=>'required',
      'kode_jenjang'=>'required'
    ]);
    
    $ta=$request->input('ta');
    $kode_jenjang=$request->input('kode_jenjang');
    
    $kombi=BiayaKomponenPeriodeModel::from('pe3_kombi_periode AS a')->select(\DB::raw('
      a.id,
      a.kombi_id,
      a.nama_kombi,
      a.periode,
      a.idkelas AS nkelas,
      b.nama_jenjang,
      a.biaya
    '))
    ->join('jenjang_studi AS b','a.kode_jenjang','b.kode_jenjang')
    ->where('a.tahun',$ta)
    ->where('a.kode_jenjang',$kode_jenjang)
    ->orderBy('a.kode_jenjang','asc')
    ->orderBy('a.kombi_id','asc');
  
    $kombi=$kombi->get();

    return Response()->json([
      'status'=>1,
      'pid'=>'fetchdata',  
      'kombi'=>$kombi,
      'message'=>'Fetch data biaya komponen periode berhasil.'
    ], 200);
  } 
  /**
   * digunakan untuk meload daftar kombi pertama kali atau selanjutnya ke table pe3_kombi_periode
   */
  public function loadkombiperiode (Request $request)
  {
    $this->hasPermissionTo('KEUANGAN-BIAYA-KOMPONEN-PERIODE_STORE');
    
    $this->validate($request, [           
      'ta'=>'required',
      'kode_jenjang'=>'required'
    ]);
    $ta=$request->input('ta');
    $kode_jenjang=$request->input('kode_jenjang');
    
    $daftar_kelas = [1];
    foreach ($daftar_kelas as $kelas)
    {
      $sql = "INSERT INTO pe3_kombi_periode (id,kombi_id,nama_kombi,periode,idkelas,kode_jenjang,tahun,biaya,created_at,updated_at)
          SELECT UUID(),id,nama AS nama_kombi,periode,'$kelas' AS idkelas,$kode_jenjang AS kode_jenjang,$ta AS tahun,0 AS biaya,NOW() AS created_at,NOW() AS updated_at FROM pe3_kombi WHERE id NOT IN (SELECT kombi_id FROM pe3_kombi_periode WHERE tahun='$ta' AND kode_jenjang=$kode_jenjang AND idkelas='$kelas')";           
          
      \DB::statement($sql);
    }        

    $kombi=BiayaKomponenPeriodeModel::from('pe3_kombi_periode AS a')->select(\DB::raw('
      a.id,
      a.kombi_id,
      a.nama_kombi,
      a.periode,
      a.idkelas AS nkelas,
      b.nama_jenjang,
      a.biaya
    '))
    ->join('jenjang_studi AS b','a.kode_jenjang','b.kode_jenjang')
    ->where('a.tahun',$ta)
    ->where('a.kode_jenjang',$kode_jenjang)
    ->orderBy('a.kode_jenjang','asc')
    ->orderBy('a.kombi_id','asc')
    ->get();
    
    \App\Models\System\ActivityLog::log($request,[
      'object' => $kombi,
      'object_id'=>'N.A', 
      'user_id' => $this->getUserid(), 
      'message' => 'Menyalin data kombi ke data kombi periode berhasil.'
    ]);

    return Response()->json([
      'status'=>1,
      'pid'=>'store',  
      'kombi'=>$kombi,
      'message'=>'Menyalin data kombi ke data kombi periode berhasil.'
    ], 200);
  } 
  /**
   * digunakan untuk merubah biaya komponen
   */
  public function updatebiaya (Request $request)
  {
    $this->hasPermissionTo('KEUANGAN-BIAYA-KOMPONEN-PERIODE_STORE');
    
    $this->validate($request, [           
      'id'=>'required|exists:pe3_kombi_periode,id',
      'biaya'=>'required'
    ]);
    $id=$request->input('id');
    $biaya=$request->input('biaya');
    
    $kombi_biaya=BiayaKomponenPeriodeModel::find($id);
    $old_biaya=$kombi_biaya->biaya;
    $kombi_biaya->biaya=$biaya;
    $kombi_biaya->save();
    
    \App\Models\System\ActivityLog::log($request,[
      'object' => $kombi_biaya,
      'object_id'=>$kombi_biaya->id, 
      'user_id' => $this->getUserid(), 
      'message' => 'Mengubah besaran biaya Rp. '.Helper::formatUang($old_biaya).' menjadi '.Helper::formatUang($biaya).' komponen ('.$kombi_biaya->nama_kombi.') berhasil dilakukan'
    ]);
    return Response()->json([
      'status'=>1,
      'pid'=>'update',     
      'kombi_biaya'=>$kombi_biaya,                            
      'message'=>'Mengubah biaya komponen '.$kombi_biaya->nama_kombi.' berhasil.'
    ], 200);
  } 
}