<?php

namespace App\Helpers;
use Carbon\Carbon;
use URL;
class Helper { 
  /**
   * daftar bulan
   */     
  private static $daftar_bulan=[
    1=>'Januari', 
    2=>'Februari', 
    3=>'Maret', 
    4=>'April', 
    5=>'Mei',
    6=>'Juni', 
    7=>'Juli', 
    8=>'Agustus', 
    9=>'September', 
    10=>'Oktober', 
    11=>'November', 
    12=>'Desember'   
  ];
  public static function getNamaBulan($no_bulan)
  {
    if ($no_bulan >=1 && $no_bulan <=12)
    {
      return Helper::$daftar_bulan[$no_bulan];
    }
    else
    {
      return null;
    }    
  }
  /**
   * digunakan untuk mendapatkan format tahun akademik
   */
  public static function getTA($ta)
  {
    return "$ta/".($ta+1);
  }      
  /**
   * digunakan untuk memformat tanggal
   * @param type $format
   * @param type $date
   * @return type date
   */
  public static function tanggal($format, $date=null) {
    Carbon::setLocale(app()->getLocale());
    if ($date == null)
    {
      $tanggal=Carbon::parse(Carbon::now())->format($format);
    }
    else
    {
      $tanggal = Carbon::parse($date)->format($format);
    }        
    $result = str_replace([
      'Sunday', 
      'Monday', 
      'Tuesday',
      'Wednesday', 
      'Thursday', 
      'Friday', 
      'Saturday'
    ], 
    [
      'Minggu', 
      'Senin', 
      'Selasa',
      'Rabu', 
      'Kamis', 
      'Jumat', 
      'Sabtu'
    ], 
    $tanggal);

    return str_replace([
      'January', 
      'February', 
      'March', 
      'April', 
      'May',
      'June', 
      'July',
      'August', 
      'September', 
      'October', 
      'November' , 
      'December'
    ], 
    [
      'Januari', 
      'Februari', 
      'Maret', 
      'April', 
      'Mei',
      'Juni', 
      'Juli', 
      'Agustus', 
      'September', 
      'Oktober', 
      'November', 
      'Desember'                        
    ], $result);
  }
  /**
   * digunakan untuk mendapatkan usia
  */ 
  public static function getUsia($tanggal_awal, $tanggal_akhir = null)
  {
    if(is_null($tanggal_akhir))
    {
      $tanggal_akhir = date('Y-m-d');
    }
    Carbon::setLocale(app()->getLocale());
    $dob = Carbon::parse($tanggal_awal);
    $d = Carbon::parse($tanggal_akhir);    
    $month = $dob->diffInMonths($d);
    
    return (float) number_format($month / 12, 1);
  }
  /**
  * digunakan untuk mem-format uang
  */
  public static function formatUang ($uang=0,$decimal=2) {
    $formatted = number_format((float)$uang,$decimal,',','.');
    return $formatted;
  }
  /**
  * digunakan untuk mem-format angka
  */
  public static function formatAngka ($angka=0) {
    $bil = intval($angka);
    $formatted = ($bil < $angka) ? $angka : $bil;
    return $formatted;
  }
  /**
  * digunakan untuk mem-format persentase
  */
  public static function formatPersen ($pembilang,$penyebut=0,$dec_sep=3) {
    $result=0.00;
    if ($pembilang > 0 && $penyebut > 0) {            
      $temp=round(number_format((float)($pembilang/$penyebut)*100,4),$dec_sep);
      $result = $temp;
    }
    else
    {
      $result=0;
    }
    return $result;
  }
  /**
  * digunakan untuk mem-format pecahan
  */
  public static function formatPecahan ($pembilang,$penyebut=0,$dec_sep=3) {
    $result=0;
    if ($pembilang > 0 && $penyebut > 0) {
      $result=round(number_format((float)($pembilang/$penyebut),4),$dec_sep);
    }
    return $result;
  }    
  
  public static function public_path($path = null)
  {
    return rtrim(app()->basePath('storage/app/public/' . $path), '/');
  } 
  public static function exported_path()
  {
    return app()->basePath('storage/app/exported/');
  } 
}