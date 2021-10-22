<?php

namespace App\Http\Controllers\SPSB;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\SPSB\FormulirPendaftaranAModel;
use App\Models\SPSB\NilaiUjianPSBModel;
use App\Models\Keuangan\TransaksiModel;
use App\Models\Keuangan\TransaksiDetailModel;

use Ramsey\Uuid\Uuid;

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

        $data = FormulirPendaftaranAModel::select(\DB::raw('
                        users.id,
                        formulir_pendaftaran.no_formulir,
                        users.name,
                        users.nomor_hp,
                        COALESCE(nilai_ujian_psb.nilai,\'N.A\') AS nilai,
                        nilai_ujian_psb.ket_lulus,
                        CASE
                            WHEN nilai_ujian_psb.ket_lulus IS NULL THEN \'N.A\'
                            WHEN nilai_ujian_psb.ket_lulus=0 THEN \'TIDAK LULUS\'
                            WHEN nilai_ujian_psb.ket_lulus=1 THEN \'LULUS\'
                        END AS status,
                        kelas.nkelas,
                        users.active,
                        users.foto,
                        users.created_at,
                        users.updated_at
                    '))
                    ->join('users','formulir_pendaftaran.user_id','users.id')                    
                    ->join('kelas','formulir_pendaftaran.idkelas','kelas.idkelas')                    
                    ->leftJoin('nilai_ujian_psb','formulir_pendaftaran.user_id','nilai_ujian_psb.user_id')                    
                    ->where('users.ta',$ta)
                    ->where('kode_jenjang',$kode_jenjang)            
                    ->whereNotNull('formulir_pendaftaran.idkelas')   
                    ->where('users.active',1)    
                    ->where('nilai_ujian_psb.ket_lulus',$filter_status)
                    ->orderBy('users.name','ASC') 
                    ->get();
        
        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',
                                'psb'=>$data,
                                'message'=>'Fetch data calon siswa baru berhasil diperoleh'
                            ],200);  
    }            
    /**
     * Detail nilai dan jadwal ujian
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $this->hasAnyPermission(['SPSB-PSB-NILAI-UJIAN_SHOW']);

        $formulir=FormulirPendaftaranAModel::select(\DB::raw('   
                                                            formulir_pendaftaran.user_id,                                                       
                                                            kode_jenjang,
                                                            CONCAT(prodi.nama_prodi,\'(\',prodi.nama_jenjang,\')\') AS nama_prodi
                                                        '))
                                            ->join('users','users.id','formulir_pendaftaran.user_id')
                                            ->join('prodi','prodi.id','formulir_pendaftaran.kode_jenjang')                                            
                                            ->find($id);
        if (is_null($formulir))
        {
            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',                
                                    'message'=>["Formulir Pendaftaran dengan ID ($id) gagal diperoleh"]
                                ],422); 
        }
        else
        {
            $transaksi_detail=TransaksiDetailModel::select(\DB::raw('transaksi.no_transaksi,transaksi.status'))
                                                    ->join('transaksi','transaksi.id','transaksi_detail.transaksi_id')
                                                    ->where('transaksi.user_id',$formulir->user_id)
                                                    ->where('kombi_id',101)                                                    
                                                    ->first(); 

            $transaksi_status=0;
            $no_transaksi='N.A';
            if (!is_null($transaksi_detail))
            {
                $no_transaksi=$transaksi_detail->no_transaksi;
                $transaksi_status=$transaksi_detail->status;
            }             
            $daftar_jenjang[]=['kode_jenjang'=>$formulir->kode_jenjang,'nama_prodi'=>$formulir->nama_prodi];
            $data_nilai_ujian=NilaiUjianPSBModel::find($id);                     
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'fetchdata',                                                        
                                        'no_transaksi'=>"$no_transaksi",                                                                           
                                        'transaksi_status'=>$transaksi_status,
                                        'daftar_jenjang'=>$daftar_jenjang,
                                        'kjur'=>$formulir->kode_jenjang,                                        
                                        'data_nilai_ujian'=>$data_nilai_ujian,                                        
                                        'message'=>"Data nilai dengan ID ($id) berhasil diperoleh"
                                    ],200);        
        }

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
            'nama_prodi'=>'required',
            'filter_status'=>'required'
        ]);
        
        $data_report=[
            'TA'=>$request->input('TA'),
            'kode_jenjang'=>$request->input('kode_jenjang'),            
            'nama_prodi'=>$request->input('nama_prodi'), 
            'filter_status'=>$request->input('filter_status'),            
        ];

        $report= new \App\Models\Report\ReportSPSBModel ($data_report);          
        return $report->kelulusan();
    }
}