<?php

namespace App\Models\SPSB;

use Illuminate\Database\Eloquent\Model;

class FormulirPendaftaranEModel extends Model {    
   /**
   * nama tabel model ini.
   *
   * @var string
   */
  protected $table = 'formulir_pendaftaran_e';
  /**
   * primary key tabel ini.
   *
   * @var string
   */
  protected $primaryKey = 'user_id';
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [        
    'user_id',
    'nama_kontak',
    'hubungan',      
    'alamat_kontak',                    
    'nomor_hp',    
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

  public function user()
  {
    return $this->belongsTo('App\Models\User','user_id','id');
  }
}