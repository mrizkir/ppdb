<?php

namespace App\Http\Controllers\DMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DMaster\JenjangStudiModel;

class JenjangStudiController extends Controller {  
  /**
   * daftar jenjang studi
   */
  public function index(Request $request)
  {
    $jenjang_studi=JenjangStudiModel::orderBy('kode_jenjang','ASC')
    ->get();

    return Response()->json([
      'status'=>1,
      'pid'=>'fetchdata',  
      'jenjang_studi'=>$jenjang_studi,                                                                                                                                   
      'message'=>'Fetch data jenjang studi berhasil.'
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);  ;     
  }  

  public function update(Request $request, $id)
  {
    $jenjang_studi = JenjangStudiModel::find($id);
    if (is_null($jenjang_studi))
    {
      return Response()->json([
        'status'=>1,
        'pid'=>'update',                
        'message'=>["Jenjang Studi dengan ID ($id) gagal diupdate"]
      ], 422);
    }
    else
    {
      $this->validate($request, [        
        'status_pendaftaran'=>'required|in:0,1'            
      ]);
      
      $jenjang_studi->status_pendaftaran = $request->input('status_pendaftaran');
      $jenjang_studi->save();

      return Response()->json([
        'status'=>1,
        'pid'=>'store',
        'jenjang_studi'=>$jenjang_studi,                                  
        'message'=>'Data Jenjang Studi berhasil diubah.'
      ], 200);
    }
  }
}