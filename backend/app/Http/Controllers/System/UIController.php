<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\System\ConfigurationModel;
use App\Models\DMaster\TAModel;
use App\Models\DMaster\JenjangStudiModel;

class UIController extends Controller {    
    /**
     * digunakan untuk mendapatkan Identitas Perguruan Tinggi
     */
    public function frontend ()
    {
        $config = ConfigurationModel::getCache();
        $captcha_site_key = $config['CAPTCHA_SITE_KEY'];
        $tahun_pendaftaran = $config['DEFAULT_TAHUN_PENDAFTARAN'];
        $semester_pendaftaran = $config['DEFAULT_SEMESTER_PENDAFTARAN'];
        $buka_ppdb=$config['DEFAULT_BUKA_PPDB'];
        $identitas['nama_sekolah']=$config['NAMA_SEKOLAH'];
        $identitas['nama_sekolah_alias']=$config['NAMA_SEKOLAH_ALIAS'];
        $identitas['bentuk_sekolah']=$config['BENTUK_SEKOLAH'];
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',
                                    'captcha_site_key'=>$captcha_site_key,
                                    'tahun_pendaftaran'=>$tahun_pendaftaran,
                                    'semester_pendaftaran'=>$semester_pendaftaran,
                                    'buka_ppdb'=>$buka_ppdb,
                                    'identitas'=>$identitas,
                                    'message'=>'Fetch data ui untuk front berhasil diperoleh'
                                ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
    /**
     * digunakan untuk mendapatkan Identitas Perguruan Tinggi
     */
    public function admin ()
    {
        $config = ConfigurationModel::getCache();
        $theme=[
            'V-SYSTEM-BAR-CSS-CLASS'=>$config['V-SYSTEM-BAR-CSS-CLASS'],
            'V-APP-BAR-NAV-ICON-CSS-CLASS'=>$config['V-APP-BAR-NAV-ICON-CSS-CLASS'],
            'V-NAVIGATION-DRAWER-CSS-CLASS'=>$config['V-NAVIGATION-DRAWER-CSS-CLASS'],
            'V-LIST-ITEM-BOARD-CSS-CLASS'=>$config['V-LIST-ITEM-BOARD-CSS-CLASS'],
            'V-LIST-ITEM-BOARD-COLOR'=>$config['V-LIST-ITEM-BOARD-COLOR'],
            'V-LIST-ITEM-ACTIVE-CSS-CLASS'=>$config['V-LIST-ITEM-ACTIVE-CSS-CLASS'],            
        ];
        $daftar_semester=[
                            0=>[
                                'id'=>1,
                                'text'=>'GANJIL'
                            ],
                            1=>[
                                'id'=>2,
                                'text'=>'GENAP'
                            ],                     
                        ];
        $daftar_ta=[];
        $daftar_jenjang=[];
        if ($this->hasRole('superadmin'))
        {
            $daftar_ta=TAModel::select(\DB::raw('tahun AS value,tahun_ajaran AS text'))
                            ->orderBy('tahun','asc')
                            ->get();

            $daftar_jenjang=JenjangStudiModel::select(\DB::raw('kode_jenjang AS id,nama_jenjang AS text'))
                                        ->get();
            $kode_jenjang=$config['DEFAULT_KODE_JENJANG'];    
            
            $tahun_pendaftaran = $config['DEFAULT_TAHUN_PENDAFTARAN'];
            $tahun_ajaran = $config['DEFAULT_TA'];
        }
        elseif($this->hasRole('psb'))
        {
            $daftar_ta=TAModel::select(\DB::raw('tahun AS value,tahun_ajaran AS text'))
                            ->orderBy('tahun','asc')
                            ->get();

            $daftar_jenjang=JenjangStudiModel::select(\DB::raw('kode_jenjang AS id,nama_jenjang AS text'))
                            ->get();
            $kode_jenjang=$config['DEFAULT_KODE_JENJANG'];     
                    
            $tahun_pendaftaran = $config['DEFAULT_TAHUN_PENDAFTARAN'];
            $tahun_ajaran = $config['DEFAULT_TA'];
        }
        elseif ($this->hasRole('siswabaru'))
        {
            $formulir=\App\Models\SPSB\FormulirPendaftaranAModel::find($this->getUserid());
            $daftar_ta=TAModel::where('tahun','=',$formulir->ta)
                                ->select(\DB::raw('tahun AS value,tahun_ajaran AS text'))
                                ->get();  
            
            $daftar_jenjang=JenjangStudiModel::select(\DB::raw('kode_jenjang AS id,nama_jenjang AS text'))
                                ->where('kode_jenjang',$formulir->kode_jenjang)
                                ->get();
            $kode_jenjang=$formulir->kode_jenjang;

            $tahun_pendaftaran = $formulir->ta;
            $tahun_ajaran = $formulir->ta;
        }                                  
        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'fetchdata',  
                                    'daftar_ta'=>$daftar_ta,    
                                    'tahun_pendaftaran'=>$tahun_pendaftaran,
                                    'tahun_ajaran'=>$tahun_ajaran,
                                    'daftar_semester'=>$daftar_semester,    
                                    'semester_akademik' => $config['DEFAULT_SEMESTER'],                                    
                                    'daftar_jenjang'=>$daftar_jenjang,
                                    'kode_jenjang'=>$kode_jenjang,                                                                                                            
                                    'theme'=>$theme,
                                    'message'=>'Fetch data ui untuk admin berhasil diperoleh'
                                ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);  
    }
}               