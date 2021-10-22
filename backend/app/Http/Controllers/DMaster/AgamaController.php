<?php

namespace App\Http\Controllers\DMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DMaster\AgamaModel;

class AgamaController extends Controller {  
    /**
     * daftar Agama
     */
    public function index(Request $request)
    {
        $agama=AgamaModel::orderBy('idagama','ASC')
                                    ->get();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'agama'=>$agama,                                                                                                                                   
                                    'message'=>'Fetch data agama berhasil.'
                                ],200)->setEncodingOptions(JSON_NUMERIC_CHECK);  ;     
    }  
}