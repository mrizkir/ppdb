<?php

namespace App\Models\SPSB;

use Illuminate\Database\Eloquent\Model;

class KelulusanPMBModel extends Model
{
  protected $table = 'kelulusan_pmb';
  protected $primaryKey = 'id';
  protected $keyType = 'string';
  public $incrementing = false;
  public $timestamps = true;

  protected $fillable = [
    'id',
    'formulir_id',
    'user_id',
    'ta',
    'kode_jenjang',
    'ket_lulus',
    'decided_by',
  ];

  protected $casts = [
    'ta' => 'integer',
    'kode_jenjang' => 'integer',
    'ket_lulus' => 'integer',
  ];

  /**
   * Daftar calon pada satu tahun dan jenjang, beserta keputusan kelulusan.
   *
   * filter_status: all | 1 | 0 | belum
   */
  public static function queryDaftar($ta, $kodeJenjang, $filterStatus = 'all')
  {
    $query = FormulirPendaftaranAModel::select(\DB::raw("
      users.id,
      formulir_pendaftaran_a.id AS formulir_id,
      users.username AS no_formulir,
      users.name,
      COALESCE(NULLIF(formulir_pendaftaran_a.nama_siswa, ''), users.name) AS nama_siswa,
      users.nomor_hp,
      users.foto,
      users.active,
      users.created_at,
      users.updated_at,
      formulir_pendaftaran_a.tempat_lahir,
      formulir_pendaftaran_a.tanggal_lahir,
      formulir_pendaftaran_a.jk,
      CONCAT_WS(' ',
        formulir_pendaftaran_a.alamat_tempat_tinggal,
        formulir_pendaftaran_a.address1_kelurahan,
        formulir_pendaftaran_a.address1_kecamatan,
        formulir_pendaftaran_a.address1_kabupaten,
        formulir_pendaftaran_a.address1_provinsi
      ) AS alamat,
      users.nomor_hp AS telp_hp,
      '' AS telp_rumah,
      '-' AS nilai,
      jenjang_studi.nama_jenjang AS nkelas,
      kelulusan_pmb.ket_lulus,
      CASE
        WHEN kelulusan_pmb.ket_lulus IS NULL THEN 'BELUM DINYATAKAN'
        WHEN kelulusan_pmb.ket_lulus = 0 THEN 'TIDAK LULUS'
        WHEN kelulusan_pmb.ket_lulus = 1 THEN 'LULUS'
      END AS status,
      kelulusan_pmb.updated_at AS dinyatakan_pada
    "))
      ->join('users', 'formulir_pendaftaran_a.user_id', 'users.id')
      ->join('jenjang_studi', 'jenjang_studi.kode_jenjang', 'formulir_pendaftaran_a.kode_jenjang')
      ->leftJoin('kelulusan_pmb', 'kelulusan_pmb.formulir_id', 'formulir_pendaftaran_a.id')
      ->where('formulir_pendaftaran_a.ta', $ta)
      ->where('formulir_pendaftaran_a.kode_jenjang', $kodeJenjang)
      ->orderBy('users.name', 'ASC');

    if ($filterStatus === 1 || $filterStatus === '1' || $filterStatus === 0 || $filterStatus === '0') {
      $query->where('kelulusan_pmb.ket_lulus', (int) $filterStatus);
    } elseif ($filterStatus === 'belum') {
      $query->whereNull('kelulusan_pmb.id');
    }

    return $query;
  }
}
