<html>
<head>
  <style>
    body { font-family: arialnarrow, sans-serif; font-size: 10.5pt; color: #222; }
    .logo { text-align: left; margin-bottom: 8px; }
    .logo img { width: 150px; height: auto; }
    h1 { font-size: 13pt; text-align: center; margin: 0; letter-spacing: 0.4px; }
    .subjudul { text-align: center; font-weight: bold; margin: 2px 0 10px; }
    .bagian { font-weight: bold; font-size: 11pt; margin: 12px 0 4px; border-bottom: 1px solid #222; }
    table.isi { width: 100%; border-collapse: collapse; table-layout: fixed; }
    table.isi td { padding: 2px 0; vertical-align: bottom; }
    td.no { width: 8%; white-space: nowrap; }
    td.label { width: 46%; }
    td.colon { width: 3%; text-align: center; }
    td.nilai { width: 43%; border-bottom: 1px dotted #444; }
    table.syarat { width: 100%; border-collapse: separate; border-spacing: 6px 4px; margin-top: 2px; }
    table.syarat td { width: 50%; vertical-align: top; padding: 3px 8px; }
    table.syarat td.kotak { border: 1px solid #222; }
    table.syarat .nama { font-weight: bold; }
    table.syarat .status { font-size: 9pt; margin-top: 2px; }
    table.syarat table { width: 100%; border-collapse: collapse; }
    table.syarat table td { width: auto; border: none; padding: 1px 0; }
    table.syarat img { margin-top: 4px; }
  </style>
</head>
<body>
  <div class="logo">
    <img src="{{ base_path('storage/app/public/images/logo.png') }}" width="150" alt="Sekolah Islam De Green Camp">
  </div>
  <h1>FORMULIR PENDAFTARAN</h1>
  <div class="subjudul">
    {{ $pesertadidik_a->nama_jenjang }} ISLAM DE GREEN CAMP<br>
    TAHUN PELAJARAN {{ $pesertadidik_a->ta }} / {{ $pesertadidik_a->ta + 1 }}
  </div>

  <div class="bagian">1. Data Umum Ananda</div>
  <table class="isi">
    <tbody>
      @php($no = 1)
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Nama Lengkap</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_a->nama_siswa }}</td>
      </tr>
      @if ($pesertadidik_a->kode_jenjang == 4 || $pesertadidik_a->kode_jenjang == 3)
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">NISN</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_a->nisn }}</td>
      </tr>
      @endif
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Nama Panggilan</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_a->nama_panggilan }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Jenis Kelamin</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_a->jk }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">NIK</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_a->nik }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Tempat Lahir</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_a->tempat_lahir }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Tanggal Lahir</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_a->tanggal_lahir }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Agama</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_a->nama_agama }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Kebutuhan Khusus</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_a->nama_kebutuhan }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Alamat Tempat Tinggal</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_a->alamat_tempat_tinggal }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">RT / RW</td>
        <td class="colon">:</td>
        <td class="nilai">RT. {{ $pesertadidik_a->address1_rt }}, RW. {{ $pesertadidik_a->address1_rw }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Kelurahan / Desa</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_a->address1_kelurahan }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Kecamatan</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_a->address1_kecamatan }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Kabupaten</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_a->address1_kabupaten }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Provinsi</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_a->address1_provinsi }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Kode Pos</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_a->kode_pos }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Kewarganegaraan</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_a->country_name }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Asal Sekolah</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_a->asal_sekolah }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Anak Ke-</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_a->anak_ke }} dari {{ $pesertadidik_a->jumlah_saudara }} bersaudara</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Golongan Darah</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_a->golongan_darah }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Tinggi / Berat Badan</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_a->tinggi }} cm / {{ $pesertadidik_a->berat_badan }} kg</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Ukuran Seragam</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_a->ukuran_seragam }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Moda Transportasi</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_a->nama_moda }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Jarak Tempat Tinggal ke Sekolah</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_a->jarak_ke_sekolah }} meter</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Waktu Tempuh</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_a->waktu_tempuh }} menit</td>
      </tr>
    </tbody>
  </table>

  <div class="bagian">2. Situasi Keluarga</div>
  <table class="isi">
    <tbody>
      @php($no = 1)
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Calon Peserta Didik Tinggal Bersama</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_b->tinggal_bersama }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Nama Panggilan</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_b->status_pernikahan }}</td>
      </tr>
    </tbody>
  </table>

  <div class="bagian">3. Data Ayah Kandung</div>
  <table class="isi">
    <tbody>
      @php($no = 1)
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Nama Ayah Kandung</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_c->nama_ayah }} ({{ $pesertadidik_c->hubungan }})</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Tempat Lahir</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_c->tempat_lahir }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Tanggal Lahir</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_c->tanggal_lahir }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Agama</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_c->nama_agama }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Alamat Rumah</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_c->alamat_tempat_tinggal }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Kelurahan / Desa</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_c->address1_kelurahan }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Kecamatan</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_c->address1_kecamatan }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Kabupaten</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_c->address1_kabupaten }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Provinsi</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_c->address1_provinsi }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Kewarganegaraan</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_c->country_name }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Nomor HP (Terhubung WA)</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_c->nomor_hp }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Email</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_c->email }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Pendidikan</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_c->pendidikan }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Pekerjaan</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_c->pekerjaan ?: $pesertadidik_c->pekerjaan_instansi }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Instansi</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_c->instansi }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Penghasilan Bulanan</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_c->penghasilan_bulanan }}</td>
      </tr>
    </tbody>
  </table>

  <div class="bagian">4. Data Ibu Kandung</div>
  <table class="isi">
    <tbody>
      @php($no = 1)
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Nama Ibu Kandung</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_d->nama_ibu }} ({{ $pesertadidik_d->hubungan }})</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Tempat Lahir</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_d->tempat_lahir }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Tanggal Lahir</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_d->tanggal_lahir }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Agama</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_d->nama_agama }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Alamat Rumah</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_d->alamat_tempat_tinggal }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Kelurahan / Desa</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_d->address1_kelurahan }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Kecamatan</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_d->address1_kecamatan }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Kabupaten</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_d->address1_kabupaten }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Provinsi</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_d->address1_provinsi }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Kewarganegaraan</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_d->country_name }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Nomor HP (Terhubung WA)</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_d->nomor_hp }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Email</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_d->email }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Pendidikan</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_d->pendidikan }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Pekerjaan</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_d->pekerjaan ?: $pesertadidik_d->pekerjaan_instansi }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Instansi</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_d->instansi }}</td>
      </tr>
      <tr>
        <td class="no">{{ $no++ }}.</td>
        <td class="label">Penghasilan Bulanan</td>
        <td class="colon">:</td>
        <td class="nilai">{{ $pesertadidik_d->penghasilan_bulanan }}</td>
      </tr>
    </tbody>
  </table>

  <div class="bagian">5. Persyaratan</div>
  <table class="syarat">
    <tbody>
      @foreach ([
        [['Foto Selfie', 'file_fotoselfi'], ['KTP Ayah', 'file_ktp_ayah']],
        [['KTP Ibu', 'file_ktp_ibu'], ['Kartu Keluarga', 'file_kk']],
        [['Akta Kelahiran', 'file_aktalahir'], ['Tangkapan Media Sosial', 'file_screenshoot_medsos']],
        [['Sertifikat', 'file_sertifikat'], ['Kartu NISN', 'file_nisn']],
        [['Kartu KIA', 'file_kia']],
      ] as $baris)
      <tr>
        @foreach ($baris as $item)
        <td class="kotak">
          <table>
            <tr>
              <td class="nama">{{ $item[0] }}</td>
            </tr>
            <tr>
              <td>
                @if (!empty($gambar[$item[1]]))
                  <img src="{{ $gambar[$item[1]] }}" width="230" alt="{{ $item[0] }}">
                @else
                  <div class="status">{{ ($persyaratan && $persyaratan->{$item[1]}) ? 'Sudah diunggah' : 'Belum diunggah' }}</div>
                @endif
              </td>
            </tr>
          </table>
        </td>
        @endforeach
        @if (count($baris) === 1)
        <td></td>
        @endif
      </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
