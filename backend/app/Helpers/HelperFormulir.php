<?php

namespace App\Helpers;

use App\Models\DMaster\JenjangStudiModel;
use App\Models\SPSB\FormulirPendaftaranAModel;
use App\Models\SPSB\FormulirPendaftaranBModel;
use App\Models\SPSB\FormulirPendaftaranCModel;
use App\Models\SPSB\FormulirPendaftaranDModel;
use App\Models\SPSB\FormulirPendaftaranEModel;
use App\Models\SPSB\FormulirPendaftaranFModel;
use App\Models\SPSB\PersyaratanPPDBModel;
use App\Models\SPSB\UndanganPMBModel;
use Ramsey\Uuid\Uuid;

class HelperFormulir
{
  public static function resolveId($id)
  {
    $formulir = FormulirPendaftaranAModel::find($id);
    if ($formulir) {
      return $formulir->id;
    }
    $formulir = FormulirPendaftaranAModel::where('user_id', $id)
      ->orderBy('ta', 'desc')
      ->orderBy('created_at', 'desc')
      ->first();
    return $formulir ? $formulir->id : $id;
  }

  public static function findA($id)
  {
    return FormulirPendaftaranAModel::find(self::resolveId($id));
  }

  public static function currentForUser($userId)
  {
    $undangan = UndanganPMBModel::where('user_id', $userId)
      ->orderBy('ta', 'desc')
      ->orderBy('created_at', 'desc')
      ->first();
    if ($undangan && $undangan->formulir_id) {
      $formulir = FormulirPendaftaranAModel::find($undangan->formulir_id);
      if ($formulir) {
        return $formulir;
      }
    }

    return FormulirPendaftaranAModel::where('user_id', $userId)
      ->orderBy('ta', 'desc')
      ->orderBy('created_at', 'desc')
      ->first();
  }

  public static function createPaket($userId, array $attrsA = [], array $attrsC = [], array $attrsD = [])
  {
    $id = Uuid::uuid4()->toString();
    FormulirPendaftaranAModel::create(array_merge([
      'id' => $id,
      'user_id' => $userId,
    ], $attrsA));
    FormulirPendaftaranBModel::create(['formulir_id' => $id]);
    FormulirPendaftaranCModel::create(array_merge(['formulir_id' => $id], $attrsC));
    FormulirPendaftaranDModel::create(array_merge(['formulir_id' => $id], $attrsD));
    FormulirPendaftaranEModel::create(['formulir_id' => $id]);
    FormulirPendaftaranFModel::create(['formulir_id' => $id]);
    PersyaratanPPDBModel::create(['formulir_id' => $id]);

    return $id;
  }

  public static function namaJenjang($kode)
  {
    $jenjang = JenjangStudiModel::find($kode);
    return $jenjang ? $jenjang->nama_jenjang.' DE GREEN CAMP' : null;
  }

  /**
   * Jalur undangan yang pindah jenjang: asal sekolah = nama jenjang formulir sebelumnya.
   * Null bila bukan undangan atau tidak ada jenjang di bawahnya.
   */
  public static function asalSekolahDariUndangan($formulir)
  {
    if (!$formulir || empty($formulir->id) || empty($formulir->user_id)) {
      return null;
    }

    $undangan = UndanganPMBModel::where('formulir_id', $formulir->id)->first();
    if (!$undangan) {
      return null;
    }

    $source = FormulirPendaftaranAModel::where('user_id', $formulir->user_id)
      ->where('id', '!=', $formulir->id)
      ->where('kode_jenjang', '<', $formulir->kode_jenjang)
      ->orderBy('kode_jenjang', 'desc')
      ->first();
    if (!$source) {
      return null;
    }

    return self::namaJenjang($source->kode_jenjang);
  }

  public static function cloneToJenjang(FormulirPendaftaranAModel $source, $ta, $kode_jenjang)
  {
    $asalSebelumnya = null;
    if ((int) $source->kode_jenjang !== (int) $kode_jenjang) {
      $asalSebelumnya = self::namaJenjang($source->kode_jenjang);
    }

    $existing = FormulirPendaftaranAModel::where('user_id', $source->user_id)
      ->where('ta', $ta)
      ->where('kode_jenjang', $kode_jenjang)
      ->first();
    if ($existing) {
      if ($asalSebelumnya) {
        $existing->asal_sekolah = $asalSebelumnya;
        $existing->save();
      }
      return $existing;
    }

    $newId = Uuid::uuid4()->toString();
    $copyA = $source->replicate();
    $copyA->id = $newId;
    $copyA->user_id = $source->user_id;
    $copyA->ta = $ta;
    $copyA->kode_jenjang = $kode_jenjang;
    if ($asalSebelumnya) {
      $copyA->asal_sekolah = $asalSebelumnya;
    }
    $copyA->save();

    self::cloneChild(FormulirPendaftaranBModel::class, $source->id, $newId);
    self::cloneChild(FormulirPendaftaranCModel::class, $source->id, $newId);
    self::cloneChild(FormulirPendaftaranDModel::class, $source->id, $newId);
    self::cloneChild(FormulirPendaftaranEModel::class, $source->id, $newId);
    self::cloneChild(FormulirPendaftaranFModel::class, $source->id, $newId);
    PersyaratanPPDBModel::create(['formulir_id' => $newId]);

    return $copyA;
  }

  public static function deletePaket($formulirId)
  {
    if (empty($formulirId)) {
      return;
    }

    FormulirPendaftaranBModel::where('formulir_id', $formulirId)->delete();
    FormulirPendaftaranCModel::where('formulir_id', $formulirId)->delete();
    FormulirPendaftaranDModel::where('formulir_id', $formulirId)->delete();
    FormulirPendaftaranEModel::where('formulir_id', $formulirId)->delete();
    FormulirPendaftaranFModel::where('formulir_id', $formulirId)->delete();
    PersyaratanPPDBModel::where('formulir_id', $formulirId)->delete();
    FormulirPendaftaranAModel::where('id', $formulirId)->delete();
  }

  private static function cloneChild($class, $fromId, $toId)
  {
    $row = $class::find($fromId);
    if (is_null($row)) {
      $class::create(['formulir_id' => $toId]);
      return;
    }
    $copy = $row->replicate();
    $copy->formulir_id = $toId;
    $copy->save();
  }
}
