<?php

namespace App\Http\Controllers\DMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DMaster\ModaTransportasiModel;

class ModaTransportasiController extends Controller {  
    /**
     * daftar ModaTransportasi
     */
    public function index(Request $request)
    {
        $moda=ModaTransportasiModel::orderBy('id_moda','ASC')
                                    ->get();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'moda_transportasi'=>$moda,                                                                                                                                   
                                    'message'=>'Fetch data moda transportasi berhasil.'
                                ],200)->setEncodingOptions(JSON_NUMERIC_CHECK);  ;     
    }  
}