<?php

namespace App\Helpers;

use App\Models\DMaster\KuotaPendaftaranModel;
use App\Models\SPSB\FormulirPendaftaranAModel;

class HelperPendaftaran extends Helper {      
  /**
   * digunakan untuk mengecek kuota masih ada atau tidak ada
   * @param tahun 
   * @param kode_jenjang 
   * @param jk 
   * @param increment 
  */
  public static function checkKuotaPendaftaran($tahun, $kode_jenjang, $jk, $increment = 1)
  {
    $kuota_jk = KuotaPendaftaranModel::select(\DB::raw('
      kuota_l,
      kuota_p
    '))
    ->where('tahun', $tahun)
    ->where('kode_jenjang', $kode_jenjang)    
    ->first();

    $_checked = false;
    if(!is_null($kuota_jk))
    {
      $jumlah_pendaftar = FormulirPendaftaranAModel::where('kode_jenjang', $kode_jenjang)
      ->where('ta', $tahun)
      ->where('jk', $jk)
      ->count('user_id')
      + $increment;
      
      $kuota = $jk == 'L' ? $kuota_jk->kuota_l : $kuota_jk->kuota_p; 
      $_checked = $jumlah_pendaftar <= $kuota;
    }    

    return $_checked;
  }
}