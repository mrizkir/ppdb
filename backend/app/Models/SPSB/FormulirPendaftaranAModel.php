<?php

namespace App\Models\SPSB;

use Illuminate\Database\Eloquent\Model;

class FormulirPendaftaranAModel extends Model {    
   /**
   * nama tabel model ini.
   *
   * @var string
   */
  protected $table = 'formulir_pendaftaran_a';
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
    'nama_siswa',
    'nisn',
    'nama_panggilan',
    'jk',
    'nik',
    'tempat_lahir',
    'tanggal_lahir',
    'idagama',
    'id_kebutuhan_khusus',

    'address1_desa_id',
    'address1_kelurahan',
    'address1_kecamatan_id',
    'address1_kecamatan',
    'address1_kabupaten_id',
    'address1_kabupaten',
    'address1_provinsi_id',
    'address1_provinsi',
    'alamat_tempat_tinggal',
    'address1_rt',
    'address1_rw',
    'kode_pos',
    'kewarganegaraan',

    'asal_sekolah',
    'anak_ke',
    'jumlah_saudara',
    'golongan_darah',
    'penyakit',
    'avoid_food',
    'tinggi',
    'berat_badan',
    'ukuran_seragam',
    'id_moda',
    'jarak_ke_sekolah',
    'waktu_tempuh',

    'kode_jenjang',
    'ta',

    'desc'
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