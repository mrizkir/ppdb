<?php

namespace App\Http\Controllers\DMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DMaster\KebutuhanKhususModel;

class KebutuhanKhususController extends Controller {  
    /**
     * daftar Kebutuhan Khusus
     */
    public function index(Request $request)
    {
        $kebutuhan_khusus=KebutuhanKhususModel::orderBy('id_kebutuhan','ASC')
                                    ->get();

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'kebutuhan_khusus'=>$kebutuhan_khusus,                                                                                                                                   
                                    'message'=>'Fetch data kebutuhan khusus berhasil.'
                                ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);  ;     
    }  
}