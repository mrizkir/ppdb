<?php

namespace App\Http\Controllers\SPSB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\DMaster\KebutuhanKhususModel;

use App\Models\SPSB\FormulirPendaftaranAModel;
use App\Models\SPSB\FormulirPendaftaranBModel;
use App\Models\SPSB\FormulirPendaftaranCModel;
use App\Models\SPSB\FormulirPendaftaranDModel;

class ReportCalonPesertaDidikController extends Controller
{
    public function printpdf (Request $request)
    {
        $this->validate($request, [
            'user_id'=>'required|exists:formulir_pendaftaran_a,user_id',                                    
        ]);
        $user_id=$request->input('user_id');
        
        $pesertadidik_a=FormulirPendaftaranAModel::leftJoin('agama','formulir_pendaftaran_a.idagama','agama.idagama')
                                                ->leftJoin('kebutuhan_khusus','formulir_pendaftaran_a.id_kebutuhan_khusus','kebutuhan_khusus.id_kebutuhan')
                                                ->leftJoin('negara','formulir_pendaftaran_a.kewarganegaraan','negara.id')
                                                ->leftJoin('moda_transportasi','formulir_pendaftaran_a.id_moda','moda_transportasi.id_moda')
                                                ->leftJoin('jenjang_studi','formulir_pendaftaran_a.kode_jenjang','jenjang_studi.kode_jenjang')
                                                ->find($user_id);
                                                
        $pesertadidik_b=FormulirPendaftaranBModel::find($user_id);
        $pesertadidik_c=FormulirPendaftaranCModel::leftJoin('agama','formulir_pendaftaran_c.idagama','agama.idagama')
                                                ->leftJoin('negara','formulir_pendaftaran_c.kewarganegaraan','negara.id')
                                                ->find($user_id);
        $pesertadidik_d=FormulirPendaftaranDModel::leftJoin('agama','formulir_pendaftaran_d.idagama','agama.idagama')
                                                ->leftJoin('negara','formulir_pendaftaran_d.kewarganegaraan','negara.id')
                                                ->find($user_id);

        $pdf = \Meneses\LaravelMpdf\Facades\LaravelMpdf::loadView('report.ReportCalonPesertaDidik', 
                                                                    [
                                                                        'pesertadidik_a'=>$pesertadidik_a,
                                                                        'pesertadidik_b'=>$pesertadidik_b,
                                                                        'pesertadidik_c'=>$pesertadidik_c,
                                                                        'pesertadidik_d'=>$pesertadidik_d,
                                                                    ],
                                                                    [],
                                                                    [
                                                                        'title' => 'Formulir Pendaftaran Calon Peserta Didik',
                                                                    ]
                                                            );
        $file_pdf=\App\Helpers\Helper::public_path('exported/pdf/')."/$user_id.pdf";        
        $pdf->save($file_pdf);

        $pdf_file="storage/exported/pdf/$user_id.pdf";

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',
                                    'pesertadidik_a'=>$pesertadidik_a,
                                    'pesertadidik_b'=>$pesertadidik_b,
                                    'pesertadidik_c'=>$pesertadidik_c,
                                    'pesertadidik_d'=>$pesertadidik_d,
                                    'pdf_file'=>$pdf_file                                    
                                ],200);
    }
}