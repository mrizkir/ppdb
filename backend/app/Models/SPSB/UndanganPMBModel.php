<?php

namespace App\Models\SPSB;

use Illuminate\Database\Eloquent\Model;
use App\Models\Keuangan\BiayaKomponenPeriodeModel;

class UndanganPMBModel extends Model {
  /**
   * nama tabel model ini.
   *
   * @var string
   */
  protected $table = 'undangan_pmb';
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
    'user_id',
    'formulir_id',
    'otp',
    'berlaku_mulai',
    'berlaku_sampai',
    'ta',
    'kode_jenjang',
    'used',
    'created_by',
  ];
  protected $casts = [
    'otp' => 'integer',
    'ta' => 'integer',
    'kode_jenjang' => 'integer',
    'used' => 'boolean',
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

  /**
   * OTP berlaku berulang selama tanggal hari ini ada di rentang
   * berlaku_mulai s.d. berlaku_sampai.
   */
  public function isBerlaku($tanggal = null)
  {
    $hari = $this->tanggalAcuan($tanggal);
    $mulai = \Carbon\Carbon::parse($this->berlaku_mulai)->toDateString();
    $sampai = \Carbon\Carbon::parse($this->berlaku_sampai)->toDateString();

    return $hari >= $mulai && $hari <= $sampai;
  }

  public function isBelumMulai($tanggal = null)
  {
    $hari = $this->tanggalAcuan($tanggal);
    $mulai = \Carbon\Carbon::parse($this->berlaku_mulai)->toDateString();

    return $hari < $mulai;
  }

  private function tanggalAcuan($tanggal = null)
  {
    return $tanggal
      ? \Carbon\Carbon::parse($tanggal)->toDateString()
      : \Carbon\Carbon::today()->toDateString();
  }

  public function user()
  {
    return $this->belongsTo('App\Models\User', 'user_id', 'id');
  }

  public function formulir()
  {
    return $this->belongsTo('App\Models\SPSB\FormulirPendaftaranAModel', 'formulir_id', 'id');
  }

  public function biayaPendaftaran()
  {
    return BiayaKomponenPeriodeModel::where('kombi_id', 101)
      ->where('kode_jenjang', $this->kode_jenjang)
      ->where('tahun', $this->ta)
      ->where('biaya', '>', 0)
      ->first();
  }
}
