<?php

namespace App\Http\Controllers\SPSB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use GuzzleHttp\Client;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SPSB\FormulirPendaftaranAModel;
use App\Models\System\ConfigurationModel;
use App\Helpers\Helper;
use App\Mail\SiswaBaruRegistered;
use App\Mail\VerifyEmailAddress;

use Ramsey\Uuid\Uuid;

class ReportSPSBJenjangController extends Controller {         
    /**
     * cetak ke excel
     *
     * @return \Illuminate\Http\Response
     */
    public function printtoexcel(Request $request)
    {   
        $this->hasPermissionTo('SPSB-PSB-LAPORAN-PRODI_BROWSE');

        $this->validate($request, [           
            'TA'=>'required',
            'kode_jenjang'=>'required',
            'nama_prodi'=>'required',
        ]);
        
        $data_report=[
            'TA'=>$request->input('TA'),
            'kode_jenjang'=>$request->input('kode_jenjang'),            
            'nama_prodi'=>$request->input('nama_prodi'),            
        ];

        $report= new \App\Models\Report\ReportSPSBModel ($data_report);          
        return $report->prodi();
    }
}