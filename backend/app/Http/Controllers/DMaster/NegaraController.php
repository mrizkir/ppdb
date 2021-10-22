<?php

namespace App\Http\Controllers\DMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DMaster\NegaraModel;

class NegaraController extends Controller {  
    /**
     * daftar Negara
     */
    public function index(Request $request)
    {
        $negara=NegaraModel::orderBy('id','ASC')
                                    ->get();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'negara'=>$negara,                                                                                                                                   
                                    'message'=>'Fetch data negara berhasil.'
                                ],200)->setEncodingOptions(JSON_NUMERIC_CHECK);  ;     
    }  
}