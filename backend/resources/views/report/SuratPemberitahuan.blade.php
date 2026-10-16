<html>
<head>
  <style>
    @page {
      margin-top: 39mm;
      margin-header: 2mm;
      margin-bottom: 12mm;
      margin-left: 15mm;
      margin-right: 15mm;
      odd-header-name: html_kop;
      even-header-name: html_kop;
    }
    body { font-family: serif; font-size: 10pt; color: #222; }
    .kop { margin: 0; padding: 0; line-height: 0; font-size: 0; }
    .tanggal { text-align: right; margin-bottom: 8px; }
    .tanggal .garis, .kepala-tanggal .garis { display: inline-block; border-bottom: 1px solid #222; text-align: center; }
    .kepala-surat { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
    .kepala-surat td { vertical-align: top; }
    .kepala-meta { width: 62%; }
    .kepala-tanggal { width: 38%; text-align: right; }
    .meta { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
    .meta td { vertical-align: top; padding: 1px 0; }
    .pembuka { margin: 8px 0 6px; }
    .info { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    .info th { background: #3d9b40; color: #fff; font-weight: bold; text-align: center; padding: 4px 6px; border: 1px solid #2f2f2f; }
    .info td { background: #e7f6e4; border: 1px solid #2f2f2f; padding: 4px 6px; vertical-align: middle; }
    .info td.nomor { text-align: center; width: 36px; }
    .akses { width: 100%; border-collapse: collapse; margin: 6px 0 8px; }
    .akses th, .akses td { border: 1px solid #2f2f2f; padding: 8px; vertical-align: middle; }
    .akses th { text-align: center; font-weight: bold; }
    .link { word-wrap: break-word; font-size: 10pt; }
    .otp { text-align: center; font-weight: bold; font-size: 12pt; letter-spacing: 1px; }
    .penutup { margin-top: 8px; }
    .ttd { text-align: center; margin-top: 10px; }
    .nama-ttd { font-weight: bold; margin-top: 36px; }
    .ttd-gambar { display: block; margin: 4px auto 0; }
    .nama-ttd-dekat { margin-top: 2px; }
    .lampiran-judul { text-align: center; font-weight: bold; margin: 14px 0 12px; line-height: 1.35; }
    .narasi { margin: 8px 0; text-align: justify; }
    .undangan { width: 100%; border-collapse: collapse; margin: 10px 0 12px; }
    .undangan td { border: 1px solid #2f2f2f; padding: 8px; vertical-align: middle; }
    .undangan td.label { font-weight: bold; text-align: center; background: #f3f3f3; }
    .undangan td.nilai-otp { text-align: center; font-weight: bold; font-size: 12pt; letter-spacing: 1px; }
    .undangan td.qr { text-align: center; }
    .biaya { width: 100%; border-collapse: collapse; }
    .biaya th { background: #3d9b40; color: #fff; font-weight: bold; text-align: center; padding: 4px 6px; border: 1px solid #2f2f2f; }
    .biaya td { border: 1px solid #2f2f2f; padding: 3px 6px; vertical-align: middle; }
    .biaya td.no { text-align: center; width: 36px; }
    .biaya td.uang { text-align: right; white-space: nowrap; width: 130px; }
    .biaya tr.bagian td { background: #e7f6e4; font-weight: bold; }
    .biaya tr.total td { font-weight: bold; }
    .jadwal-utuh { width: 100%; border-collapse: collapse; margin-top: 14px; page-break-inside: avoid; }
    .jadwal-utuh > tr > td, .jadwal-utuh td { border: none; padding: 0; }
    .jadwal { width: 100%; border-collapse: collapse; margin-top: 6px; }
    .jadwal th { background: #3d9b40; color: #fff; font-weight: bold; text-align: center; padding: 4px 6px; border: 1px solid #2f2f2f; }
    .jadwal td { border: 1px solid #2f2f2f; padding: 4px 6px; }
    .jadwal tr.selang td { background: #e7f6e4; }
    .jabatan { text-align: center; font-size: 10pt; }
  </style>
</head>
<body>
  <htmlpageheader name="kop">
    <div class="kop">
      <img src="{{ base_path('storage/app/public/images/kop_panitia_pmb.png') }}" style="width: 190mm; height: 35mm;" alt="Kop Panitia PMB">
    </div>
  </htmlpageheader>
  <sethtmlpageheader name="kop" value="on" show-this-page="1" />

  <table class="kepala-surat">
    <tr>
      <td class="kepala-meta">
        <table class="meta">
          <tr>
            <td style="width: 90px;">Nomor</td>
            <td style="width: 12px;">:</td>
            <td>{{ $nomor_surat }}</td>
          </tr>
          <tr>
            <td>Perihal</td>
            <td>:</td>
            <td>
              Pemberitahuan Rincian Biaya, Jadwal, dan Undangan<br>
              PMB Sekolah Islam De Green Camp Periode {{ $ta }}-{{ $ta_berikut }}
            </td>
          </tr>
          <tr>
            <td>Sifat</td>
            <td>:</td>
            <td>Rahasia</td>
          </tr>
          <tr>
            <td>Lampiran</td>
            <td>:</td>
            <td>2 Berkas</td>
          </tr>
        </table>
      </td>
      <td class="kepala-tanggal">
        <div class="garis">
          Tanjungpinang, {{ $tanggal_masehi }}<br>
          {{ $tanggal_hijriah }}
        </div>
      </td>
    </tr>
  </table>

  <div>
    Kepada Yth.<br>
    Ayah dan Bunda dari Ananda <strong>{{ $nama }}</strong><br>
    Calon Murid {{ $jenjang }} Islam De Green Camp<br>
    <strong><em>Rahimakumullah</em></strong>
  </div>

  <div class="pembuka">
    <em>Bismillah,</em><br>
    Berkenaan dengan kegiatan Penerimaan Murid Baru (PMB) di Sekolah Islam De Green Camp untuk periode {{ $ta }}-{{ $ta_berikut }}, beberapa informasi penting perlu kami sampaikan sebagai berikut.
  </div>

  <table class="info">
    <thead>
      <tr>
        <th style="width: 36px;">No</th>
        <th>Perihal</th>
        <th style="width: 230px;">Keterangan</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="nomor">1</td>
        <td style="text-align: center;">Persyaratan usia pada bulan Juli {{ $ta }}.</td>
        <td>
          TK minimal sudah berusia 4,4 tahun.<br>
          SD minimal sudah berusia 6 tahun.<br>
          SMP maksimal berusia 14 tahun.<br>
          SMA maksimal berusia 17 tahun.
        </td>
      </tr>
      <tr>
        <td class="nomor">2</td>
        <td style="text-align: center;">Potongan biaya bagi alumni TK Islam De Green Camp yang melanjutkan ke Kelas I (Satu) SD Islam De Green Camp.</td>
        <td>Sebesar 5% pada uang pangkal.</td>
      </tr>
      <tr>
        <td class="nomor">3</td>
        <td style="text-align: center;">Potongan biaya bagi alumni SD Islam De Green Camp yang akan melanjutkan ke kelas VII (Tujuh) SMP Islam De Green Camp.</td>
        <td>Sebesar 35% pada uang pangkal.</td>
      </tr>
      <tr>
        <td class="nomor">4</td>
        <td style="text-align: center;">Potongan biaya bagi alumni SMP Islam De Green Camp yang akan melanjutkan ke kelas X (Sepuluh) SMA Islam De Green Camp.</td>
        <td>Sebesar dua juta rupiah.</td>
      </tr>
    </tbody>
  </table>

  <div class="penutup">
    Demikian pemberitahuan ini kami sampaikan, semoga bermanfaat bagi Ayah Bunda yang sedang merencanakan proses pendidikan bagi Ananda. Atas perhatian dan kerjasama dari Ayah dan Bunda kami ucapkan. <em>Jazakumullah khair.</em>
  </div>

  <div class="ttd">
    Mengetahui,<br>
    <img class="ttd-gambar" src="{{ base_path('storage/app/public/images/ttd_panitia_pmb.png') }}" width="200" alt="Tanda tangan panitia">
    <div class="nama-ttd nama-ttd-dekat">Totok Riyanto, M. Pd., Gr</div>
    <div class="jabatan">Ketua Panitia PMB SI DGC {{ substr($ta, -2) }}{{ substr($ta_berikut, -2) }}</div>
  </div>

  <pagebreak odd-header-name="html_kop" odd-header-value="on" even-header-name="html_kop" even-header-value="on" />

  <table class="meta">
    <tr>
      <td style="width: 90px;">Lampiran</td>
      <td style="width: 12px;"></td>
      <td></td>
    </tr>
    <tr>
      <td>Nomor surat</td>
      <td>:</td>
      <td>{{ $nomor_surat }}</td>
    </tr>
    <tr>
      <td>Perihal</td>
      <td>:</td>
      <td>
        Pemberitahuan Perincian Biaya dan Jadwal PMB<br>
        Periode {{ $ta }}-{{ $ta_berikut }} TK, SD, SMP dan SMA Islam De Green Camp
      </td>
    </tr>
  </table>

  <div class="lampiran-judul">
    TABEL PERINCIAN BIAYA PMB<br>
    SEKOLAH ISLAM DE GREEN CAMP<br>
    PERIODE {{ $ta }}-{{ $ta_berikut }}
  </div>

  <table class="biaya">
    <thead>
      <tr>
        <th style="width: 36px;">No.</th>
        <th>Komponen Biaya</th>
        <th style="width: 130px;">Besaran Biaya<br>(dalam rupiah)</th>
        <th style="width: 170px;">Keterangan</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="no">1</td>
        <td>Formulir Pendaftaran TK-SD</td>
        <td class="uang">375.000,00-</td>
        <td rowspan="2">Dibayarkan saat Pra Pendaftaran</td>
      </tr>
      <tr>
        <td class="no">2</td>
        <td>Formulir Pendaftaran SMP-SMA</td>
        <td class="uang">400.000,00-</td>
      </tr>
      <tr class="bagian">
        <td colspan="4">Biaya Pendidikan Bulanan (BPB) Bulan Juli {{ $ta - 1 }}</td>
      </tr>
      <tr>
        <td class="no">1</td>
        <td>BPB TK</td>
        <td class="uang">500.000,00-</td>
        <td rowspan="5">Dibayarkan pada bulan Juli {{ $ta }} (Setelah dinyatakan lulus seleksi dan telah melakukan tahapan pendaftaran ulang)</td>
      </tr>
      <tr>
        <td class="no">2</td>
        <td>BPB SD</td>
        <td class="uang">650.000,00-</td>
      </tr>
      <tr>
        <td class="no">3</td>
        <td>BPB SMP</td>
        <td class="uang">750.000,00-</td>
      </tr>
      <tr>
        <td class="no">4</td>
        <td>BPB SMA</td>
        <td class="uang">750.000,00-</td>
      </tr>
      <tr>
        <td class="no">5</td>
        <td>BPB Peserta Didik Penyandang Disabilitas (PDPD) SD</td>
        <td class="uang">1.000.000,00-</td>
      </tr>
      <tr class="bagian">
        <td colspan="4">PMB TK</td>
      </tr>
      <tr>
        <td class="no">1</td>
        <td>Uang Pangkal</td>
        <td class="uang">3.000.000,00-</td>
        <td rowspan="3">Dibayarkan dalam satu tahapan saat pendaftaran ulang</td>
      </tr>
      <tr>
        <td class="no">2</td>
        <td>Biaya PBM (setiap awal tahun pelajaran)</td>
        <td class="uang">1.500.000,00-</td>
      </tr>
      <tr>
        <td class="no">3</td>
        <td>Seragam</td>
        <td class="uang">1.000.000,00-</td>
      </tr>
      <tr class="total">
        <td colspan="2" style="text-align: center;">Total Keseluruhan</td>
        <td class="uang">5.500.000,00-</td>
        <td></td>
      </tr>
      <tr class="bagian">
        <td colspan="4">PMB SD Kelas I (Satu)</td>
      </tr>
      <tr>
        <td class="no">1</td>
        <td>Uang Pangkal</td>
        <td class="uang">12.000.000,00-</td>
        <td rowspan="3">Dibayarkan dalam satu tahapan saat pendaftaran ulang</td>
      </tr>
      <tr>
        <td class="no">2</td>
        <td>Biaya PBM (Setiap awal tahun pelajaran)</td>
        <td class="uang">2.000.000,00-</td>
      </tr>
      <tr>
        <td class="no">3</td>
        <td>Seragam</td>
        <td class="uang">1.300.000,00-</td>
      </tr>
      <tr class="total">
        <td colspan="2" style="text-align: center;">Total Keseluruhan</td>
        <td class="uang">15.300.000,00-</td>
        <td></td>
      </tr>
      <tr class="bagian">
        <td colspan="4">PMB SMP Kelas VII (Tujuh)</td>
      </tr>
      <tr>
        <td class="no">1</td>
        <td>Uang Pangkal</td>
        <td class="uang">11.500.000,00-</td>
        <td rowspan="3">Dibayarkan dalam satu tahapan saat pendaftaran ulang</td>
      </tr>
      <tr>
        <td class="no">2</td>
        <td>Biaya PBM (Setiap awal tahun pelajaran)</td>
        <td class="uang">2.500.000,00-</td>
      </tr>
      <tr>
        <td class="no">3</td>
        <td>Seragam</td>
        <td class="uang">1.450.000,00-</td>
      </tr>
      <tr class="total">
        <td colspan="2" style="text-align: center;">Total Keseluruhan</td>
        <td class="uang">15.550.000,00-</td>
        <td></td>
      </tr>
      <tr class="bagian">
        <td colspan="4">PMB SMA Kelas X (Sepuluh)</td>
      </tr>
      <tr>
        <td class="no">1</td>
        <td>Uang Pangkal</td>
        <td class="uang">11.500.000,00-</td>
        <td rowspan="4">Dibayarkan dalam satu tahapan saat pendaftaran ulang</td>
      </tr>
      <tr>
        <td class="no">2</td>
        <td>Biaya PBM (Setiap awal tahun pelajaran)</td>
        <td class="uang">2.500.000,00-</td>
      </tr>
      <tr>
        <td class="no">3</td>
        <td>Seragam</td>
        <td class="uang">1.850.000,00-</td>
      </tr>
      <tr class="total">
        <td colspan="2" style="text-align: center;">Total Keseluruhan</td>
        <td class="uang">15.850.000,00-</td>
      </tr>
      <tr>
        <td class="no">4</td>
        <td>Biaya PBM SMA Kelas XII</td>
        <td class="uang">4.000.000,00-</td>
        <td>Dibayarkan pada saat kenaikan kelas XII</td>
      </tr>
    </tbody>
  </table>

  <table class="jadwal-utuh">
    <tr>
      <td>
        <div>Jadwal pendaftaran PMB sebagai berikut:</div>
        <table class="jadwal">
          <thead>
            <tr>
              <th>Agenda</th>
              <th style="width: 180px;">Tanggal Penting</th>
            </tr>
          </thead>
          <tbody>
            <tr class="selang">
              <td>Pembukaan Pendaftaran</td>
              <td>19–23 Oktober {{ $ta - 1 }}</td>
            </tr>
            <tr>
              <td>Zoominar Program Sekolah</td>
              <td>24 Oktober {{ $ta - 1 }}</td>
            </tr>
            <tr class="selang">
              <td>Verifikasi berkas Calon Murid dan Interview Orangtua</td>
              <td>21 – 28 Oktober {{ $ta - 1 }}</td>
            </tr>
            <tr>
              <td>Pengumuman Kelulusan</td>
              <td>02 November {{ $ta - 1 }}</td>
            </tr>
            <tr class="selang">
              <td>Pendaftaran Ulang</td>
              <td>03-06 November {{ $ta - 1 }}</td>
            </tr>
          </tbody>
        </table>
      </td>
    </tr>
  </table>

  <div class="ttd">
    Mengetahui,<br>
    <img class="ttd-gambar" src="{{ base_path('storage/app/public/images/ttd_panitia_pmb.png') }}" width="200" alt="Tanda tangan panitia">
    <div class="nama-ttd nama-ttd-dekat">Totok Riyanto, M.Pd., Gr</div>
    <div class="jabatan">Ketua Panitia PMB SI DGC {{ substr($ta, -2) }}{{ substr($ta_berikut, -2) }}</div>
  </div>

  <pagebreak odd-header-name="html_kop" odd-header-value="on" even-header-name="html_kop" even-header-value="on" />

  <table class="meta">
    <tr>
      <td style="width: 90px;"><u>Lampiran 2</u></td>
      <td style="width: 12px;"></td>
      <td></td>
    </tr>
    <tr>
      <td>Nomor surat</td>
      <td>:</td>
      <td>{{ $nomor_surat }}</td>
    </tr>
    <tr>
      <td>Perihal</td>
      <td>:</td>
      <td>
        Pemberitahuan Rincian Biaya, Jadwal, dan Undangan<br>
        PMB Sekolah Islam De Green Camp Periode {{ $ta }}-{{ $ta_berikut }}
      </td>
    </tr>
  </table>

  <div class="lampiran-judul">
    TAUTAN UNDANGAN PENDAFTARAN PMB<br>
    SEKOLAH ISLAM DE GREEN CAMP<br>
    PERIODE {{ $ta }}-{{ $ta_berikut }}
  </div>

  <div class="narasi">
    Bagi Ayah-Bunda yang berencana untuk mendaftarkan ananda di Sekolah Islam De Green Camp pada tahun ajaran {{ $ta }}/{{ $ta_berikut }}, maka Ayah-Bunda dapat melakukan pendaftaran secara langsung dengan mengakses tautan undangan atau memindai kode QR pada table di bawah ini. Link undangan pendaftaran berikut hanya berlaku sampai dengan <strong>{{ $berlaku_sampai }}</strong>.
  </div>

  <table class="undangan">
    <tr>
      <td class="label" style="width: 46%;">Link Undangan Pendaftaran</td>
      <td class="label" style="width: 27%;">Kode OTP</td>
      <td class="label" style="width: 27%;">Kode QR</td>
    </tr>
    <tr>
      <td class="link">{{ $link }}</td>
      <td class="nilai-otp">{{ $otp }}</td>
      <td class="qr">
        <img src="data:image/png;base64,{{ $qr_base64 }}" width="90" height="90" alt="QR pendaftaran">
      </td>
    </tr>
  </table>

  <div class="narasi">
    Bagi setiap alumni yang telah melakukan pendaftaran, berikutnya akan dilakukan <strong>proses seleksi</strong> sesuai ketentuan yang berlaku. Apabila Ayah-Bunda menemukan kendala atau memerlukan bantuan selama proses pendaftaran, silakan menghubungi panitia PMB pada nomor WA berikut (+62812-6632-571).
  </div>

  <div class="narasi">
    <em>Jazakumullahu khairan</em> atas perhatian dan kepercayaan Ayah-Bunda.
  </div>

  <div class="ttd">
    Mengetahui,<br>
    <img class="ttd-gambar" src="{{ base_path('storage/app/public/images/ttd_panitia_pmb.png') }}" width="200" alt="Tanda tangan panitia">
    <div class="nama-ttd nama-ttd-dekat">Totok Riyanto, M. Pd., Gr</div>
    <div class="jabatan">Ketua Panitia PMB SI DGC {{ substr($ta, -2) }}{{ substr($ta_berikut, -2) }}</div>
  </div>
</body>
</html>
