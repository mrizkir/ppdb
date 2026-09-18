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
   protected $primaryKey = 'formulir_id';
   protected $keyType = 'string';
   /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
   protected $fillable = [        
     'formulir_id',
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

  public function formulir()
  {
    return $this->belongsTo('App\Models\SPSB\FormulirPendaftaranAModel','formulir_id','id');
  }
}