<?php

namespace App\Models\SPSB;

use Illuminate\Database\Eloquent\Model;

class FormulirPendaftaranDModel extends Model {    
  /**
   * nama tabel model ini.
   *
   * @var string
  */
  protected $table = 'formulir_pendaftaran_d';
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
    'nama_ibu',
    'hubungan',  
    'tempat_lahir',     
    'tanggal_lahir',
    'idagama',
    
    'address1_desa_id',
    'address1_kelurahan',
    'address1_kecamatan_id',
    'address1_kecamatan',
    'address1_kabupaten_id',
    'address1_kabupaten',
    'address1_provinsi_id',
    'address1_provinsi',
    'alamat_tempat_tinggal',                
    'kewarganegaraan',
    'nomor_hp',
    
    'email',
    'pendidikan',
    'pekerjaan_instansi',
    'penghasilan_bulanan',
    'fb_account',
		'ig_account',
		'tiktok_account',
    'desc',        
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