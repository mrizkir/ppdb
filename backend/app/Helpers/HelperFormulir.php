<?php

namespace App\Helpers;

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

  public static function cloneToJenjang(FormulirPendaftaranAModel $source, $ta, $kode_jenjang)
  {
    $existing = FormulirPendaftaranAModel::where('user_id', $source->user_id)
      ->where('ta', $ta)
      ->where('kode_jenjang', $kode_jenjang)
      ->first();
    if ($existing) {
      return $existing;
    }

    $newId = Uuid::uuid4()->toString();
    $copyA = $source->replicate();
    $copyA->id = $newId;
    $copyA->user_id = $source->user_id;
    $copyA->ta = $ta;
    $copyA->kode_jenjang = $kode_jenjang;
    $copyA->save();

    self::cloneChild(FormulirPendaftaranBModel::class, $source->id, $newId);
    self::cloneChild(FormulirPendaftaranCModel::class, $source->id, $newId);
    self::cloneChild(FormulirPendaftaranDModel::class, $source->id, $newId);
    self::cloneChild(FormulirPendaftaranEModel::class, $source->id, $newId);
    self::cloneChild(FormulirPendaftaranFModel::class, $source->id, $newId);
    PersyaratanPPDBModel::create(['formulir_id' => $newId]);

    return $copyA;
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
