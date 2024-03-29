<?php

namespace App\Models\DMaster;

use Illuminate\Database\Eloquent\Model;

class KuotaPendaftaranModel extends Model {    
   /**
   * nama tabel model ini.
   *
   * @var string
   */
  protected $table = 'kuota_pendaftaran';
  /**
   * primary key tabel ini.
   *
   * @var string
   */
  protected $primaryKey = 'id';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'id', 
    'tahun',
    'kode_jenjang',
    'kuota_l',
    'kuota_p',
  ];
  /**
   * enable auto_increment.
   *
   * @var string
   */
  public $incrementing = false;
  /**
   * activated timestamps.
   *
   * @var string
   */
  public $timestamps = true;
}