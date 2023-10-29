<?php

namespace App\Http\Controllers\SPSB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DMaster\JenjangStudiModel;

class SPSBController extends Controller 
{  
    /**
     * index
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $this->hasPermissionTo('SPSB-PSB_BROWSE');

        $this->validate($request, [           
            'TA'=>'required',
        ]);

        $ta=$request->input('TA');
        
        $daftar_registrasi=[];
        $total_registrasi=0;

        $daftar_isi_formulir=[];
        $total_isi_formulir=0;

        $daftar_lulus=[];
        $total_lulus=0;

        $daftar_tidak_lulus=[];
        $total_tidak_lulus=0;

        $subquery = \DB::table('formulir_pendaftaran')
                        ->select(\DB::raw('kode_jenjang,COUNT(user_id) AS jumlah'))
                        ->groupBy('kode_jenjang')
                        ->where('ta',$ta);
        
        if ($this->hasRole('superadmin'))
        {
            $daftar_registrasi=JenjangStudiModel::select(\DB::raw('id AS kode_jenjang,nama_prodi,nama_prodi_alias,nama_jenjang,COALESCE(jumlah,0) AS jumlah'))
                                        ->leftJoinSub($subquery,'formulir_pendaftaran',function($join){
                                            $join->on('formulir_pendaftaran.kode_jenjang','=','prodi.id');
                                        })
                                        ->get();
                                        
            $subquery_isi_formulir=$subquery->whereNotNull('idkelas');
            $daftar_isi_formulir=JenjangStudiModel::select(\DB::raw('id AS kode_jenjang,nama_prodi,nama_prodi_alias,nama_jenjang,COALESCE(jumlah,0) AS jumlah'))
                                        ->leftJoinSub($subquery_isi_formulir,'formulir_pendaftaran',function($join){
                                            $join->on('formulir_pendaftaran.kode_jenjang','=','prodi.id');
                                        })
                                        ->get();
            
            $subquery_kelulusan=\DB::table('nilai_ujian_psb')
                            ->select(\DB::raw('kjur,COUNT(nilai_ujian_psb.user_id) AS jumlah'))
                            ->join('formulir_pendaftaran','formulir_pendaftaran.user_id','nilai_ujian_psb.user_id')
                            ->groupBy('kjur')
                            ->where('ta',$ta);
                            
            $daftar_lulus=JenjangStudiModel::select(\DB::raw('
                            id AS kode_jenjang,
                            nama_prodi,
                            nama_prodi_alias,
                            nama_jenjang,
                            COALESCE(jumlah,0) AS jumlah'
                        ))
                        ->joinSub($subquery_kelulusan->where('ket_lulus',1),'nilai_ujian_psb',function($join){
                            $join->on('nilai_ujian_psb.kjur','=','prodi.id');
                        })                        
                        ->get();

            $daftar_tidak_lulus=JenjangStudiModel::select(\DB::raw('
                            id AS kode_jenjang,
                            nama_prodi,
                            nama_prodi_alias,
                            nama_jenjang,
                            COALESCE(jumlah,0) AS jumlah'
                        ))
                        ->joinSub($subquery_kelulusan->where('ket_lulus',0),'nilai_ujian_psb',function($join){
                            $join->on('nilai_ujian_psb.kjur','=','prodi.id');
                        })                        
                        ->get();
                        
            $total_registrasi=$daftar_registrasi->sum('jumlah');
            $total_isi_formulir=$daftar_isi_formulir->sum('jumlah');
            $total_lulus=$daftar_lulus->sum('jumlah');
            $total_tidak_lulus=$daftar_tidak_lulus->sum('jumlah');
        }
        else if ($this->hasRole('psb'))
        {
            $daftar_registrasi=\DB::table('usersprodi')
                        ->select(\DB::raw('
                            kode_jenjang,
                            nama_prodi,
                            nama_prodi_alias,
                            nama_jenjang,
                            COALESCE(jumlah,0) AS jumlah'
                        ))
                        ->leftJoinSub($subquery,'formulir_pendaftaran',function($join){
                            $join->on('formulir_pendaftaran.kode_jenjang','=','usersprodi.kode_jenjang');
                        })
                        ->where('user_id',$this->getUserid())
                        ->get();

            $subquery_isi_formulir=$subquery->whereNotNull('idkelas');
            $daftar_isi_formulir=\DB::table('usersprodi')
                                ->select(\DB::raw('
                                    kode_jenjang,
                                    nama_prodi,
                                    nama_prodi_alias,
                                    nama_jenjang,
                                    COALESCE(jumlah,0) AS jumlah'
                                ))
                                ->leftJoinSub($subquery_isi_formulir,'formulir_pendaftaran',function($join){
                                    $join->on('formulir_pendaftaran.kode_jenjang','=','usersprodi.id');
                                })
                                ->where('user_id',$this->getUserid())
                                ->get();

            $subquery_kelulusan=\DB::table('nilai_ujian_psb')
                            ->select(\DB::raw('kjur,COUNT(nilai_ujian_psb.user_id) AS jumlah'))
                            ->join('formulir_pendaftaran','formulir_pendaftaran.user_id','nilai_ujian_psb.user_id')
                            ->groupBy('kjur')
                            ->where('ta',$ta);

            $daftar_lulus=\DB::table('usersprodi')
                            ->select(\DB::raw('
                                kode_jenjang,
                                nama_prodi,
                                nama_prodi_alias,
                                nama_jenjang,
                                COALESCE(jumlah,0) AS jumlah'
                            ))
                            ->joinSub($subquery_kelulusan->where('ket_lulus',1),'nilai_ujian_psb',function($join){
                                $join->on('nilai_ujian_psb.kjur','=','usersprodi.id');
                            })
                            ->where('user_id',$this->getUserid())
                            ->get();

            $daftar_tidak_lulus=\DB::table('usersprodi')
                            ->select(\DB::raw('
                                kode_jenjang,
                                nama_prodi,
                                nama_prodi_alias,
                                nama_jenjang,
                                COALESCE(jumlah,0) AS jumlah'
                            ))
                            ->joinSub($subquery_kelulusan->where('ket_lulus',0),'nilai_ujian_psb',function($join){
                                $join->on('nilai_ujian_psb.kjur','=','usersprodi.id');
                            })
                            ->where('user_id',$this->getUserid())
                            ->get();

            $total_registrasi=$daftar_registrasi->sum('jumlah');
            $total_isi_formulir=$daftar_isi_formulir->sum('jumlah');
            $total_lulus=$daftar_lulus->sum('jumlah');
            $total_tidak_lulus=$daftar_tidak_lulus->sum('jumlah');
        }

        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata', 
                                                                                                                          
                                'daftar_registrasi'=>$daftar_registrasi,
                                'total_registrasi'=>$total_registrasi,       

                                'daftar_isi_formulir'=>$daftar_isi_formulir,
                                'total_isi_formulir'=>$total_isi_formulir,

                                'daftar_lulus'=>$daftar_lulus,
                                'total_lulus'=>$total_lulus,
                                
                                'daftar_tidak_lulus'=>$daftar_tidak_lulus,
                                'total_tidak_lulus'=>$total_tidak_lulus,

                                'message'=>'Fetch data dashboard psb berhasil diperoleh'
                            ], 200);    
        
    }
}