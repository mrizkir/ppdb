<html>
    <head>

    </head>
    <body>
        <h3 style="text-align:center">CALON PESERTA DIDIK</h3>
        <h3 style="text-align:center">JENJANG {{$pesertadidik_a->nama_jenjang}}</h3>
        <h3 style="text-align:center">TAHUN PELAJARAN {{$pesertadidik_a->ta}} / {{$pesertadidik_a->ta+1}}</h3>
        <h5>1. Data Umum Ananda</h5>
        <table style="font-size:11px">
            <tbody>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>Nama Lengkap</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_a->nama_siswa}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>Nama Panggilan</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_a->nama_panggilan}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>Jenis Kelamin</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_a->jk}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>NIK</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_a->nik}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>TEMPAT LAHIR</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_a->tempat_lahir}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>TANGGAL LAHIR</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_a->tanggal_lahir}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>AGAMA</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_a->nama_agama}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>KEBUTUHAN KHUSUS</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_a->nama_kebutuhan}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>ALAMAT TEMPAT TINGGAL</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_a->alamat_tempat_tinggal}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>RT / RW</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">RT. {{$pesertadidik_a->address1_rt}}, RW. {{$pesertadidik_a->address1_rw}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>KELURAHAN / DESA</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_a->address1_kelurahan}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>KECAMATAN</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_a->address1_kecamatan}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>KABUPATEN</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_a->address1_kabupaten}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>PROVINSI</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_a->address1_provinsi}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>KODE POS</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_a->kode_pos}}</td>
                </tr>                            
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>KEWARGANEGARAAN</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_a->country_name}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>ASAL SEKOLAH</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_a->asal_sekolah}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>ANAK KE - </strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_a->anak_ke}} dari {{$pesertadidik_a->jumlah_saudara}} bersaudara</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>GOLONGAN DARAH</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_a->golongan_darah}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>TINGGI / BERAT BADAN</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_a->tinggi}} cm / {{$pesertadidik_a->berat_badan}} kg </td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>UKURAN SERAGAM</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_a->ukuran_seragam}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>MODA TRANSPORTASI</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_a->nama_moda}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>JARAK TEMPAT TINGGAL KE SEKOLAH</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_a->jarak_ke_sekolah}} meter</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>WAKTU TEMPUH</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_a->waktu_tempuh}} menit</td>
                </tr>
            </tbody>
        </table>
        <h5>2. Situasi Keluarga</h5>
        <table style="font-size:11px">
            <tbody>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>Calon Peserta Didik Tinggal Bersama</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_b->tinggal_bersama}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>Nama Panggilan</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_b->status_pernikahan}}</td>
                </tr>
            </tbody>
        </table>
        <pagebreak></pagebreak>
        <h5>3. Data Ayah Kandung</h5>
        <table style="font-size:11px">
            <tbody>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>Nama Ayah Kandung</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_c->nama_ayah}} ({{$pesertadidik_c->hubungan}})</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>TEMPAT LAHIR</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_c->tempat_lahir}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>TANGGAL LAHIR</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_c->tanggal_lahir}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>AGAMA</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_c->nama_agama}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>ALAMAT RUMAH</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_c->alamat_tempat_tinggal}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>KELURAHAN / DESA</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_c->address1_kelurahan}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>KECAMATAN</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_c->address1_kecamatan}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>KABUPATEN</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_c->address1_kabupaten}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>PROVINSI</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_c->address1_provinsi}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>KEWARGANEGARAAN</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_c->country_name}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>NOMOR HP (TERHUBUNG WA)</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_c->nomor_hp}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>EMAIL</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_c->email}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>PENDIDIKAN</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_c->pendidikan}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>PEKERJAAN DAN INSTANSI</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_c->pekerjaan_instansi}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>PENGHASILAN BULANAN</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_c->penghasilan_bulanan}}</td>
                </tr>
            </tbody>
        </table>
        <h5>4. Data Ibu Kandung</h5>
        <table style="font-size:11px">
            <tbody>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>Nama Ibu Kandung</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_d->nama_ibu}} ({{$pesertadidik_d->hubungan}})</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>TEMPAT LAHIR</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_d->tempat_lahir}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>TANGGAL LAHIR</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_d->tanggal_lahir}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>AGAMA</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_d->nama_agama}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>ALAMAT RUMAH</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_d->alamat_tempat_tinggal}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>KELURAHAN / DESA</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_d->address1_kelurahan}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>KECAMATAN</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_d->address1_kecamatan}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>KABUPATEN</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_d->address1_kabupaten}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>PROVINSI</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_d->address1_provinsi}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>KEWARGANEGARAAN</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_d->country_name}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>NOMOR HP (TERHUBUNG WA)</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_d->nomor_hp}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>EMAIL</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_d->email}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>PENDIDIKAN</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_d->pendidikan}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>PEKERJAAN DAN INSTANSI</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_d->pekerjaan_instansi}}</td>
                </tr>
                <tr>
                    <td style="padding-bottom: 10px;">
                        <strong>PENGHASILAN BULANAN</strong>
                    </td>
                    <td style="padding-bottom: 10px;">:</td>
                    <td style="padding-bottom: 10px;">{{$pesertadidik_d->penghasilan_bulanan}}</td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
    