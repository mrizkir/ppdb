<?php

namespace App\Http\Controllers\SPSB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SPSB\KelulusanPMBModel;

class ReportKelulusanController extends Controller {             
  /**
   * digunakan untuk mendapatkan calon siswa baru yang telah mengisi formulir pendaftaran
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {   
    $this->hasAnyPermission(['SPSB-PSB-LAPORAN-KELULUSAN_BROWSE']);

    $this->validate($request, [           
      'TA'=>'required',
      'kode_jenjang'=>'required',
      'filter_status'=>'required'
    ]);
    
    $ta=$request->input('TA');
    $kode_jenjang=$request->input('kode_jenjang');
    $filter_status=$request->input('filter_status');

    $data = KelulusanPMBModel::queryDaftar($ta, $kode_jenjang, $filter_status)->get();
    
    return Response()->json([
      'status'=>1,
      'pid'=>'fetchdata',
      'psb'=>$data,
      'message'=>'Fetch data calon siswa baru berhasil diperoleh'
    ], 200);  
  }
  /**
   * cetak ke excel
   *
   * @return \Illuminate\Http\Response
   */
  public function printtoexcel(Request $request)
  {   
    $this->hasAnyPermission(['SPSB-PSB-LAPORAN-KELULUSAN_BROWSE']);

    $this->validate($request, [           
      'TA'=>'required',
      'kode_jenjang'=>'required',
      'filter_status'=>'required'
    ]);

    $nama_jenjang = $request->input('nama_prodi', $request->input('nama_jenjang'));
    
    $data_report=[
      'TA'=>$request->input('TA'),
      'kode_jenjang'=>$request->input('kode_jenjang'),            
      'nama_prodi'=>$nama_jenjang, 
      'filter_status'=>$request->input('filter_status'),            
    ];

    $report= new \App\Models\Report\ReportSPSBModel ($data_report);
    return $report->kelulusan();
  }
}