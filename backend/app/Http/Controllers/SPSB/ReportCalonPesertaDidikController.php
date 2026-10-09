<?php

namespace App\Http\Controllers\SPSB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\DMaster\KebutuhanKhususModel;

use App\Models\SPSB\FormulirPendaftaranAModel;
use App\Models\SPSB\FormulirPendaftaranBModel;
use App\Models\SPSB\FormulirPendaftaranCModel;
use App\Models\SPSB\FormulirPendaftaranDModel;
use App\Models\SPSB\PersyaratanPPDBModel;

class ReportCalonPesertaDidikController extends Controller
{
  public function printpdf (Request $request)
  {
    $this->validate($request, [
      'user_id'=>'required',
    ]);
    $user_id=$request->input('user_id');
    $formulir_id=\App\Helpers\HelperFormulir::resolveId($user_id);
    
    $pesertadidik_a=FormulirPendaftaranAModel::leftJoin('agama','formulir_pendaftaran_a.idagama','agama.idagama')
      ->leftJoin('kebutuhan_khusus','formulir_pendaftaran_a.id_kebutuhan_khusus','kebutuhan_khusus.id_kebutuhan')
      ->leftJoin('negara','formulir_pendaftaran_a.kewarganegaraan','negara.id')
      ->leftJoin('moda_transportasi','formulir_pendaftaran_a.id_moda','moda_transportasi.id_moda')
      ->leftJoin('jenjang_studi','formulir_pendaftaran_a.kode_jenjang','jenjang_studi.kode_jenjang')
      ->find($formulir_id);
                        
    $pesertadidik_b=FormulirPendaftaranBModel::find($formulir_id);
    $pesertadidik_c=FormulirPendaftaranCModel::leftJoin('agama','formulir_pendaftaran_c.idagama','agama.idagama')
    ->leftJoin('negara','formulir_pendaftaran_c.kewarganegaraan','negara.id')
    ->find($formulir_id);
    $pesertadidik_d=FormulirPendaftaranDModel::leftJoin('agama','formulir_pendaftaran_d.idagama','agama.idagama')
    ->leftJoin('negara','formulir_pendaftaran_d.kewarganegaraan','negara.id')
    ->find($formulir_id);
    $persyaratan=PersyaratanPPDBModel::find($formulir_id);
    $kategori_disabilitas=\DB::table('formulir_kategori_disabilitas as kategori')
      ->join('kebutuhan_khusus', 'kebutuhan_khusus.id_kebutuhan', 'kategori.id_kebutuhan')
      ->where('kategori.formulir_id', $formulir_id)
      ->orderBy('kebutuhan_khusus.id_kebutuhan')
      ->pluck('kebutuhan_khusus.nama_kebutuhan')
      ->implode(', ');
    $gambar=$this->gambarPersyaratan($persyaratan);

    $pdf = \Mccarlosen\LaravelMpdf\Facades\LaravelMpdf::loadView('report.ReportCalonPesertaDidik', 
      [
        'pesertadidik_a'=>$pesertadidik_a,
        'pesertadidik_b'=>$pesertadidik_b,
        'pesertadidik_c'=>$pesertadidik_c,
        'pesertadidik_d'=>$pesertadidik_d,
        'persyaratan'=>$persyaratan,
        'kategori_disabilitas'=>$kategori_disabilitas,
        'gambar'=>$gambar,
      ],
      [],
      array_merge(
        [
          'title' => 'Formulir Pendaftaran Calon Peserta Didik',
        ],
        $this->fontArialNarrow()
      )
    );
    $file_pdf=\App\Helpers\Helper::public_path('exported/pdf/')."/$user_id.pdf";
    $pdf->save($file_pdf);

    $pdf_file = "exported/pdf/$user_id.pdf";

    return Response()->json([
      'status'=>1,
      'pid'=>'fetchdata',
      'pesertadidik_a'=>$pesertadidik_a,
      'pesertadidik_b'=>$pesertadidik_b,
      'pesertadidik_c'=>$pesertadidik_c,
      'pesertadidik_d'=>$pesertadidik_d,
      'persyaratan'=>$persyaratan,
      'pdf_file'=>$pdf_file                                    
    ], 200);
  }

  private function fontArialNarrow()
  {
    $dir = null;
    foreach ([
      '/System/Library/Fonts/Supplemental',
      '/usr/share/fonts/truetype/msttcorefonts',
      '/usr/share/fonts/truetype/liberation',
      app()->basePath('resources/fonts'),
    ] as $kandidat) {
      if (is_file($kandidat.'/Arial Narrow.ttf')) {
        $dir = $kandidat;
        break;
      }
    }
    if (!$dir) {
      return [];
    }
    return [
      'default_font' => 'arialnarrow',
      'custom_font_dir' => $dir,
      'custom_font_data' => [
        'arialnarrow' => [
          'R' => 'Arial Narrow.ttf',
          'B' => 'Arial Narrow Bold.ttf',
          'I' => 'Arial Narrow Italic.ttf',
          'BI' => 'Arial Narrow Bold Italic.ttf',
        ],
      ],
    ];
  }

  private function gambarPersyaratan($persyaratan)
  {
    $kolom = [
      'file_fotoselfi',
      'file_ktp_ayah',
      'file_ktp_ibu',
      'file_kk',
      'file_aktalahir',
      'file_screenshoot_medsos',
      'file_sertifikat',
      'file_nisn',
      'file_kia',
      'file_pemeriksaan_ahli',
    ];
    $gambar = [];
    foreach ($kolom as $nama) {
      $gambar[$nama] = $this->siapkanGambar($persyaratan ? $persyaratan->{$nama} : null);
    }
    return $gambar;
  }

  private function siapkanGambar($relative)
  {
    if (!$relative) {
      return null;
    }
    $path = \App\Helpers\Helper::public_path(ltrim(str_replace('storage', '', $relative), '/'));
    if (!is_file($path)) {
      return null;
    }
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
      return $path;
    }
    if ($ext !== 'pdf') {
      return null;
    }
    $dir = app()->basePath('storage/app/tmp/persyaratan');
    if (!is_dir($dir)) {
      mkdir($dir, 0755, true);
    }
    $jpg = $dir.'/'.md5($path.'|'.filemtime($path)).'.jpg';
    if (!is_file($jpg)) {
      $cmd = 'gs -dSAFER -dBATCH -dNOPAUSE -sDEVICE=jpeg -r110 -dFirstPage=1 -dLastPage=1 -sOutputFile='
        .escapeshellarg($jpg).' '.escapeshellarg($path);
      exec($cmd, $output, $code);
      if ($code !== 0 || !is_file($jpg)) {
        return null;
      }
    }
    return $jpg;
  }
}