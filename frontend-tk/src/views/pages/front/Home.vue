<template>
    <FrontLayout>
        <v-carousel
            cycle                                                
            show-arrows-on-hover>
            <v-carousel-item
                v-for="(slide, i) in slides"
                :key="i"
                :src="$api.url+'/'+slide.src"
                :contain="true">                            
            </v-carousel-item>
        </v-carousel>
        <v-container fluid>
            <v-row>
                <v-col xs="12" md="12" sm="12">
                    <v-card>
                        <v-card-title>
                            BAGAN ALUR PROSES PENDAFTARAN
                        </v-card-title>
                        <v-img :src="$api.url+'/storage/images/informasi/alur_pendaftaran_tk.jpg'"></v-img>                        
                    </v-card>
                </v-col>
                <v-col xs="12" md="12" sm="12">
                    <v-card>
                        <v-card-title>
                            ALUR PROSES PENDAFTARAN DAN SELEKSI TK ISLAM DE GREEN CAMP T.P 2021-2022
                        </v-card-title>
                        <v-card-text>
                            <v-timeline>
                                <v-timeline-item small color="green">
                                    <template v-slot:opposite>
                                        <span
                                        :class="`headline font-weight-bold green--text`"
                                        >
                                            TAHAP KE - 1
                                        </span>
                                    </template>
                                    <div class="py-4">
                                        <h2 :class="`headline font-weight-light mb-4`">
                                        PPDB Sekolah De Green Camp
                                        </h2>
                                        <div>
                                            Seleksi  PPDB  terdiri  dari 9 (Sembilan) tahapan. Sebelum melanjutkan pastikan terlebih dahulu :
                                            <ol type="a">
                                                <li>Ananda sudah berusia minimal <strong>4 tahun</strong> pada 1 Juli 2021 <strong>untuk Jenjang Studi TK</strong></li>                                                
                                            </ol>
                                        </div>
                                    </div>
                                </v-timeline-item>
                                <v-timeline-item small color="green">
                                    <template v-slot:opposite>
                                        <span
                                        :class="`headline font-weight-bold green--text`"
                                        >
                                            TAHAP KE - 2
                                        </span>
                                    </template>
                                    <div class="py-4">
                                        <h2 :class="`headline font-weight-light mb-4`">
                                        PRA-PENDAFTARAN
                                        </h2>
                                        <div>
                                            Klik menu PRA-PENDAFTARAN maka muncul halaman pra-pendaftaran, yang terdiri dari beberapa bagian yang wajib untuk diisi, yaitu : 
                                            <ol type="a">
                                                <li>Nama Calon Peserta Didik</li>
                                                <li>Nomor Kontak WA Wali</li>
                                                <li>Surat Elektronik</li>                                                
                                                <li>Username</li>
                                                <li>Password</li>
                                            </ol>
                                            Setelah tekan tombol "Daftar" maka akan muncul konfirmasi pendaftaran yang menyatakan proses pengisian form telah berhasil; 
                                            dan disana juga memuat informasi biaya pendaftaran. Selanjutnya lakukan pembayaran dengan cara ditransfer ke :<br />
                                             <v-alert type="tkinfo">
                                                BANK MUAMALAT <br>
                                                NOMOR REKENING : 4-520-006-166 <br>
                                                A.N : TK ISLAM DE GREEN CAMP<br>                                                
                                            </v-alert> 
                                        </div>
                                    </div>
                                </v-timeline-item>
                                <v-timeline-item small color="green">
                                    <template v-slot:opposite>
                                        <span
                                        :class="`headline font-weight-bold green--text`"
                                        >
                                            TAHAP KE - 3
                                        </span>
                                    </template>
                                    <div class="py-4">
                                        <h2 :class="`headline font-weight-light mb-4`">
                                        KONFIRMASI PEMBAYARAN
                                        </h2>
                                        <div>
                                            Klik menu KONFIRMASI PEMBAYARAN maka muncul halaman konfirmasi pembayaran, isikan username kemudian klik "tombol konfirmasi", bila berhasil isilah bagian-bagian berikut: 
                                            <ol type="a">
                                                <li>Pilih model pembayaran (via teller, transfer, internet banking, mobile banking)</li>
                                                <li>jumlah dana sejumlah dengan yang ditransfer</li>
                                                <li>Nama rekening pengirim</li>
                                                <li>Nama pengirim</li>
                                                <li>Bank Pengirim</li>
                                                <li>Tanggal Bayar/Transer</li>
                                                <li>Catatan</li>
                                                <li>Unggah Foto/Scan/Screenshoot Bukti Bayar</li>
                                            </ol>                                            
                                        </div>
                                    </div>
                                </v-timeline-item>
                                <v-timeline-item small color="green">
                                    <template v-slot:opposite>
                                        <span
                                        :class="`headline font-weight-bold green--text`"
                                        >
                                            TAHAP KE - 4
                                        </span>
                                    </template>
                                    <div class="py-4">
                                        <h2 :class="`headline font-weight-light mb-4`">
                                        PANITIA VERIFIKASI PEMBAYARAN
                                        </h2>
                                        <div>
                                            Panitia akan melakukan verifikasi bukti bayar/transfer dan mengirimkan pesan teks kepada pendaftar melalui kontak WA.  Apabila belum mendapatkan  pesan verifikasi dari panitia (1x24 jam), pendaftar dapat menghubungi  <strong>nomor kontak WA +62 812-6633-2571 (narahubung Indah Handika)</strong>.
                                        </div>
                                    </div>
                                </v-timeline-item>
                                <v-timeline-item small color="green">
                                    <template v-slot:opposite>
                                        <span
                                        :class="`headline font-weight-bold green--text`"
                                        >
                                            TAHAP KE - 5
                                        </span>
                                    </template>
                                    <div class="py-4">
                                        <h2 :class="`headline font-weight-light mb-4`">
                                        LOGIN FORMULIR
                                        </h2>
                                        <div>
                                            Setelah mendapatkan pesan teks  verifikasi  dari Panitia PPDB, maka pendaftar dapat melakukan LOGIN  untuk  pengisian formulir pendaftaran.  Pastikanlah  data  yang  diisikan ke formulir dapat dipertanggungjawabkan.  Berkas  cetak formulir akan ditandatangani di atas materai 6000  oleh pendaftar pada saat menghadiri  jadwal interview dan observasi.  Hasil scan Akta Kelahiran, KK dan KTP kedua orang tua/wali juga akan dicocokkan dengan dokumen asli yang dibawa oleh pendaftar  pada saat menghadiri  jadwal interview dan observasi.  
                                        </div>
                                    </div>
                                </v-timeline-item>
                                <v-timeline-item small color="green">
                                    <template v-slot:opposite>
                                        <span
                                        :class="`headline font-weight-bold green--text`"
                                        >
                                            TAHAP KE - 6
                                        </span>
                                    </template>
                                    <div class="py-4">
                                        <h2 :class="`headline font-weight-light mb-4`">
                                        JADWAL ZOOM, INTERVIEW, DAN OBSERVASI
                                        </h2>
                                        <div>
                                            Setelah pengisian formulir selesai, Panitia akan menghubungi nomor kontak WA pendaftar. Pada tahap ini, Panitia akan memberikan jadwal zoom serta  interview bagi orang tua/wali calon Peserta Didik Baru (PDB) dan observasi bagi calon PDB. 
                                            Jadwal interview dan observasi berlangsung bersamaan bagi setiap keluarga dengan durasi 60 menit .  Jadwal yang telah difiksasi (disepakati melalui pesan WA) tidak dapat diubah, kecuali jika ada udzur syar’i.
                                        </div>
                                        <v-alert type="warning">
                                            <div class="title">
                                                Catatan Penting !!!
                                            </div>
                                            Jika dalam pelaksanaannya orang tua calon peserta didik baru tidak merespon informasi yang disampaikan panitia maka dianggap mengundurkan diri
                                        </v-alert>
                                    </div>
                                </v-timeline-item>
                                <v-timeline-item small color="green">
                                    <template v-slot:opposite>
                                        <span
                                        :class="`headline font-weight-bold green--text`"
                                        >
                                            TAHAP KE - 7
                                        </span>
                                    </template>
                                    <div class="py-4">
                                        <h2 :class="`headline font-weight-light mb-4`">
                                        KESEPAKATAN KERJA SAMA (MoU) Pendidikan
                                        </h2>
                                        <div>
                                            Pada waktu yang bersamaan, setelah jadwal interview dan observasi selesai dikomunikasikan, Panitia akan melanjutkan dengan memberikan informasi jadwal Zoom Meeting. 
                                            Kegiatan Zoom Meeting dapat dilaksanakan sebelum jadwal interview-observasi. 
                                            Pelaksanaan kegiatan Zoom Meeting adalah pada hari Sabtu  tertentu pada  pukul 08.00—10.00 WIB. 
                                            Tautan kegiatan ini akan dikirimkan oleh Panitia melalui nomor kontak WA pendaftar.  
                                            Zoom Meeting bersifat klasikal  dan membahas tentang konsep serta program sekolah pada masing-masing jenjang pendidikan di Sekolah Islam De Green Camp. 
                                            Kegiatan ini wajib diikuti oleh setiap orangtua/wali calon PDB
                                        </div>
                                        <v-alert type="warning">
                                            <div class="title">
                                                Catatan Penting !!!
                                            </div>
                                            Jika dalam pelaksanaannya orang tua calon peserta didik baru tidak merespon informasi yang disampaikan panitia maka dianggap mengundurkan diri.
                                        </v-alert>
                                    </div>
                                </v-timeline-item>
                                <v-timeline-item small color="green">
                                    <template v-slot:opposite>
                                        <span
                                        :class="`headline font-weight-bold green--text`"
                                        >
                                            TAHAP KE - 8
                                        </span>
                                    </template>
                                    <div class="py-4">
                                        <h2 :class="`headline font-weight-light mb-4`">
                                        DISCLAIMER DAN PERSYARATAN
                                        </h2>
                                        <div>
                                            Pendaftar yang tidak menghadiri jadwal interview maupun observasi tanpa melakukan konfirmasi H-1 kepada Panitia akan dianggap mengundurkan diri. 
                                            Saat menghadiri kegiatan ini, perndaftar harus membawa berkas asli akta kelahiran, KTP kedua orang tua/wali,d an KK yang sesuai dengan berkas unggah saat melakukan pendaftaran daring. 
                                            Apabila berkas asli tidak dibawa, maka proses interview dan observasi dibatalkan atau tidak dapat dilaksanakan. 
                                            Orang tua/wali juga akan diminta untuk menandatangani berkas bermaterai (materai  telah disiapkan oleh panitia). 
                                        </div>                                        
                                    </div>
                                </v-timeline-item>
                                <v-timeline-item small color="green">
                                    <template v-slot:opposite>
                                        <span
                                        :class="`headline font-weight-bold green--text`"
                                        >
                                            TAHAP KE - 9
                                        </span>
                                    </template>
                                    <div class="py-4">
                                        <h2 :class="`headline font-weight-light mb-4`">
                                        PENGUMUMAN HASIL SELEKSI
                                        </h2>
                                        <div>
                                            Pengumuman hasil seleksi PPDB TK Islam De Green Camp akan disampaikan melalui kontak WhatsApp orang tua/wali satu pekan setelah jadwal interview dan observasi. Keputusan Panitia PPDB terkait hasil seleksi tidak dapat diganggu gugat.
                                        </div>                                        
                                    </div>
                                </v-timeline-item>
                            </v-timeline>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </FrontLayout>
</template>
<script>
import FrontLayout from '@/views/layouts/FrontLayout';
export default {
    name: 'Home',
    data:()=>({
        slides: [
            {
                src:'storage/images/sliders/slider1.jpg',
            }
        ],
    }), 
    components: {
		FrontLayout
	}  
}
</script>