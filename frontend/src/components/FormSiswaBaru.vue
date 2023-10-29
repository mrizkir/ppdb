<template>
   <v-row align="center" justify="center" class="mb-4" no-gutters>
    <v-col xs="12" sm="12" md="7">
      <v-form ref="frmdata" v-model="form_valid" lazy-validation>
        <v-card class="mb-4">
          <v-card-title>
            RENCANA STUDI
          </v-card-title>
          <v-card-text>                        
            <v-select
              label="JENJANG STUDI"
              v-model="formdata.kode_jenjang"
              :items="daftar_jenjang"
              item-text="nama_jenjang"
              item-value="kode_jenjang"
              :rules="rule_jenjang"
              filled
            />        
            <v-text-field
              v-model="formdata.asal_sekolah"
              label="ASAL SEKOLAH"
              filled
            />
            <v-text-field
              v-model="formdata.nisn"
              label="NISN"
              :rules="rule_nisn"
              filled
              v-if="formdata.kode_jenjang == 4 || formdata.kode_jenjang == 3"
            />
          </v-card-text>
        </v-card>
        <v-card class="mb-4">
          <v-card-title>
            DATA UMUM ANANDA
          </v-card-title>
          <v-card-text>
            <v-text-field
              label="NAMA LENGKAP"
              v-model="formdata.nama_siswa"
              :rules="rule_nama_siswa"
              filled
            />
            <v-text-field
              label="NAMA PANGGILAN"
              v-model="formdata.nama_panggilan"
              :rules="rule_nama_panggilan"
              filled
            />
            <v-radio-group v-model="formdata.jk" row>
              JENIS KELAMIN : 
              <v-radio label="LAKI-LAKI" value="L"></v-radio>
              <v-radio label="PEREMPUAN" value="P"></v-radio>
            </v-radio-group>
            <v-text-field
              label="NIK"
              v-model="formdata.nik"
              :rules="rule_nik"
              filled
            />
            <v-row>
              <v-col cols="6">
                <v-select
                  label="ANAK KE"
                  :items="[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20]"
                  v-model="formdata.anak_ke"
                  filled
                />   
              </v-col>
              <v-col cols="6">
                <v-select
                  label="JUMLAH SAUDARA"
                  :items="[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20]"
                  v-model="formdata.jumlah_saudara"
                  filled
                />   
              </v-col>
            </v-row>
            <v-text-field
              label="TEMPAT LAHIR"
              v-model="formdata.tempat_lahir" 
              :rules="rule_tempat_lahir"          
              filled
            />
            <v-menu
              ref="menuTanggalLahir"
              v-model="menuTanggalLahir"
              :close-on-content-click="false"
              :return-value.sync="formdata.tanggal_lahir"
              transition="scale-transition"
              offset-y
              max-width="290px"
              min-width="290px"
            >
              <template v-slot:activator="{ on }">
                <v-text-field
                  v-model="formdata.tanggal_lahir"
                  label="TANGGAL LAHIR"       
                  readonly
                  filled
                  v-on="on"
                  :rules="rule_tanggal_lahir"
                ></v-text-field>
              </template>
              <v-date-picker
                v-model="formdata.tanggal_lahir"   
                no-title                                
                scrollable
                >
                <v-spacer></v-spacer>
                <v-btn text color="primary" @click="menuTanggalLahir = false">Cancel</v-btn>
                <v-btn text color="primary" @click="$refs.menuTanggalLahir.save(formdata.tanggal_lahir)">OK</v-btn>
              </v-date-picker>
            </v-menu>
            <v-select
              label="AGAMA"
              :items="daftar_agama"
              v-model="formdata.idagama"
              item-text="nama_agama"
              item-value="idagama" 
              :rules="rule_agama"
              filled
            />        
          </v-card-text>
        </v-card>
        <v-card class="mb-4">
          <v-card-title>
            DATA FISIK & PSIKIS
          </v-card-title>
          <v-card-text>
            <v-select
              label="GOLONGAN DARAH"
              :items="daftar_golongan_darah"
              v-model="formdata.golongan_darah"
              filled
            />    
            <v-text-field
              label="TINGGI (CM)"
              v-model="formdata.tinggi" 
              :rules="rule_tinggi"          
              filled
            />
            <v-text-field
              label="BERAT BADAN (KG)"
              v-model="formdata.berat_badan" 
              :rules="rule_berat_badan"          
              filled
            />  
            <v-select
              label="UKURAN SERAGAM"
              :items="['S','M','L','XL','9','10','11','12','13']"
              v-model="formdata.ukuran_seragam"
              filled
            />           
            <v-text-field
              label="PENYAKIT"
              v-model="formdata.penyakit"             
              filled
            />
            <v-text-field
              label="MAKANAN YANG DIHINDARI ?"
              v-model="formdata.avoid_food" 
              filled
            />
            <v-select 
              label="KEBUTUHAN KHUSUS"
              :items="daftar_kebutuhan_khusus"
              v-model="formdata.id_kebutuhan_khusus"
              item-text="nama_kebutuhan"
              item-value="id_kebutuhan"
              filled
            />               
          </v-card-text>
        </v-card>
        <v-card class="mb-4">
          <v-card-title>
            ALAMAT
          </v-card-title>
          <v-card-text>
            <v-select
              label="PROVINSI"
              :items="daftar_provinsi"
              v-model="provinsi_id"
              item-text="nama"
              item-value="id"
              return-object
              :loading="btnLoadingProv"
              filled
            />
            <v-select
              label="KABUPATEN/KOTA"
              :items="daftar_kabupaten"
              v-model="kabupaten_id"
              item-text="nama"
              item-value="id"
              return-object
              :loading="btnLoadingKab"
              filled
            />
            <v-select
              label="KECAMATAN"
              :items="daftar_kecamatan"
              v-model="kecamatan_id"
              item-text="nama"
              item-value="id" 
              return-object                           
              filled
            />
            <v-select
              label="DESA/KELURAHAN"
              :items="daftar_desa"
              v-model="desa_id"
              item-text="nama"
              item-value="id"
              return-object
              :rules="rule_desa"
              filled
            />
            <v-text-field
              v-model="formdata.address1_rt"
              label="RT"
              :rules="rule_address1_rt"
              filled
            />
            <v-text-field
              v-model="formdata.address1_rw"
              label="RW"
              :rules="rule_address1_rw"
              filled
            />
            <v-text-field
              v-model="formdata.alamat_tempat_tinggal"
              label="ALAMAT RUMAH"
              :rules="rule_alamat_rumah"
              filled
            />
            <v-text-field
              v-model="formdata.kode_pos"
              label="KODE POS"
              :rules="rule_kode_pos"
              filled
            />
            <v-select
              label="KEWARGANEGARAAN"
              :items="daftar_negara"
              v-model="formdata.kewarganegaraan"
              item-text="country_name"
              item-value="id" 
              :rules="rule_negara"            
              filled
            />        
          </v-card-text>
        </v-card>
        <v-card class="mb-4">
          <v-card-title>
            TRANSPORTASI
          </v-card-title>
          <v-card-text>
            <v-select
              label="MODA TRANSPORTASI"
              :items="daftar_moda_transportasi"
              v-model="formdata.id_moda"
              item-text="nama_moda"
              item-value="id_moda"
              :rules="rule_moda_transportasi"
              filled
            />
            <v-text-field
              v-model="formdata.jarak_ke_sekolah"
              label="JARAK KE SEKOLAH (METER)"
              :rules="rule_jarak_ke_sekolah"
              filled
            />
            <v-text-field
              v-model="formdata.waktu_tempuh"
              label="WAKTU TEMPUH (MENIT)"
              :rules="rule_waktu_tempuh"
              filled
            />
          </v-card-text>
        </v-card>                
        <v-card class="mb-4">                    
          <v-card-actions>                        
            <v-spacer></v-spacer>  
            <v-btn 
              color="grey darken-1" 
              text 
              @click.stop="kembali">
                KEMBALI
            </v-btn>
            <v-btn 
              color="blue darken-1" 
              text 
              @click.stop="save" 
              :loading="btnLoading"
              :disabled="!form_valid || btnLoading">SIMPAN</v-btn>
          </v-card-actions>
        </v-card>
      </v-form>
    </v-col>
    <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>  
  </v-row>
</template>
<script>
  export default {
    name: "FormSiswaBaru",
    created()
    {
      this.initialize();
    },
    props: {    
      user_id:{            
        type: String,
        required: true
      }
    },
    data: () => ({        
      btnLoading: false,
      btnLoadingProv: false,
      btnLoadingKab: false,
      btnLoadingKec: false,
      btnLoadingFakultas: false,

      //form
      kode_billing:"N.A",
      form_valid: true,

      menuTanggalLahir: false,
      
      daftar_agama: [],

      daftar_moda_transportasi: [],

      daftar_golongan_darah: [
        {
          value:"-",
          text:"TIDAK TAHU"
        },
        {
          value:"A",
          text:"A"
        },
        {
          value:"B",
          text:"B"
        },
        {
          value:"AB",
          text:"AB"
        },
        {
          value:"O",
          text:"O"
        },
      ],

      daftar_kebutuhan_khusus: [],

      daftar_negara: [],

      daftar_provinsi: [],
      provinsi_id:0,

      daftar_kabupaten: [],
      kabupaten_id:0,

      daftar_kecamatan: [],
      kecamatan_id:0,

      daftar_desa: [],
      desa_id:0,

      daftar_jenjang: [],
      
      formdata:{
        nama_siswa: "",
        nisn: null,
        nama_panggilan: "", 
        jk:"L",
        nik: "", 
        tempat_lahir: "",
        tanggal_lahir: "",
        idagama:1,
        id_kebutuhan_khusus:1,
        
        alamat_tempat_tinggal: "",
        address1_rt: "",
        address1_rw: "",
        kode_pos: "",
        kewarganegaraan: "",

        asal_sekolah: "",
        anak_ke:1,
        jumlah_saudara:0,
        golongan_darah:"-",
        penyakit: "",
        avoid_food: "",
        tinggi: "",
        berat_badan: "",
        ukuran_seragam:"S",
        id_moda: "",
        jarak_ke_sekolah: "",
        waktu_tempuh: "",
        
        kode_jenjang: "",
        ta: "",

        desc: "",
      },
      rule_nama_siswa: [
        value => !!value||"Nama Peserta Didik mohon untuk diisi !!!",
        value => /^[A-Za-z\s\\,\\.]*$/.test(value) || "Nama Peserta Didik hanya boleh string dan spasi",
      ], 
      rule_nama_panggilan: [
        value => !!value||"Nama Panggilan Panggilan Peserta mohon untuk diisi !!!",
        value => /^[A-Za-z\s\\,\\.]*$/.test(value) || "Nama Panggilan Peserta Didik hanya boleh string dan spasi",
      ],
      rule_nik: [
        value => !!value||"Mohon NIK Peserta Didik untuk di isi sesuai dengan Kartu Keluarga !!!",  
        value => /^[0-9]+$/.test(value) || "NIk Peserta Didik hanya boleh angka",
      ], 
      rule_tempat_lahir: [
        value => !!value||"Tempat Lahir mohon untuk diisi !!!"
      ], 
      rule_tanggal_lahir: [
        value => !!value||"Tanggal Lahir mohon untuk dipilih !!!"
      ], 
      rule_agama: [
        value => !!value||"Mohon agama Peserta Didik mohon untuk diisi !!!"
      ], 
      rule_tinggi: [
        value => !!value||"Tinggi badan Peserta Didik untuk di isi sesuai dengan Kartu Keluarga !!!",  
        value => /^[0-9]+$/.test(value) || "Tinggi badan Peserta Didik hanya boleh angka",
      ], 
      rule_berat_badan: [
        value => !!value||"Berat badan Peserta Didik untuk di isi sesuai dengan Kartu Keluarga !!!",  
        value => /^[0-9]+$/.test(value) || "Berat badan Peserta Didik hanya boleh angka",
      ], 
      rule_desa: [
        value => !!value||"Mohon Desa untuk dipilih !!!"
      ], 
      rule_address1_rt: [
        value => !!value||"Mohon RT untuk di isi !!!"
      ], 
      rule_address1_rw: [
        value => !!value||"Mohon RW untuk di isi !!!"
      ], 
      rule_negara: [
        value => !!value||"Mohon Kewarganegaraan  untuk dipilih !!!"
      ], 
      rule_alamat_rumah: [
        value => !!value||"Alamat Rumah mohon untuk diisi !!!"
      ], 
      rule_kode_pos: [
        value => !!value||"Kode POS mohon untuk diisi !!!",
        value => /^[0-9]+$/.test(value) || "Kode pos hanya boleh angka", 
        value => {
          if (value && typeof value !== "undefined" && value.length > 0){
            return value.length == 5 || "Panjang karakter kode pos harus sama dengan 5";
          }
          else
          {
            return true;
          }
        }               
      ], 
      rule_moda_transportasi: [
        value => !!value||"Mohon moda transportasi untuk dipilih !!!"
      ], 
      rule_jarak_ke_sekolah: [
        value => !!value||"Jarak ke sekolah mohon untuk diisi !!!",
        value => /^[0-9]+$/.test(value) || "Jarak ke sekolah hanya boleh angka",
      ], 
      rule_waktu_tempuh: [
        value => !!value||"Waktu tempuh ke sekolah mohon untuk diisi !!!",
        value => /^[0-9]+$/.test(value) || "Waktu sekolah ke sekolah hanya boleh angka",
      ], 
      rule_jenjang: [
        value => !!value||"Jenjang Studi mohon untuk dipilih !!!"
      ], 
      rule_nisn: [
        value => !!value||"NISN mohon untuk diisi !!!",
        value => /^[0-9]+$/.test(value) || "NISN hanya boleh angka",
      ], 
    }),
    methods: {
      initialize: async function()
      {
        this.$ajax.get("/datamaster/negara").then(({ data })=>{                
          this.daftar_negara = data.negara;
        });
        this.$ajax.get("/datamaster/provinsi").then(({ data })=>{                
          this.daftar_provinsi = data.provinsi;
        });
        
        await this.$ajax.get("/datamaster/agama").then(({ data })=>{                  
          this.daftar_agama = data.agama;
        });

        await this.$ajax.get("/datamaster/modatransportasi").then(({ data })=>{                  
          this.daftar_moda_transportasi = data.moda_transportasi;
        });
        
        await this.$ajax.get("/datamaster/kebutuhankhusus").then(({ data })=>{                  
          this.daftar_kebutuhan_khusus = data.kebutuhan_khusus;
        });

        await this.$ajax.get("/datamaster/jenjangstudi").then(({ data })=>{                  
          this.daftar_jenjang = data.jenjang_studi;
        });
            
        await this.$ajax.get("/spsb/formulirpendaftaran/" + this.user_id,  
          {
            headers:{
              Authorization: this.$store.getters["auth/Token"]
            }
          },
          
        ).then(({ data })=>{   
          this.formdata.nama_siswa = data.formulir.nama_siswa;
          this.formdata.nisn = data.formulir.nisn;
          this.formdata.nama_panggilan = data.formulir.nama_panggilan;
          this.formdata.jk = data.formulir.jk;
          this.formdata.nik = data.formulir.nik;
          this.formdata.tempat_lahir = data.formulir.tempat_lahir;
          this.formdata.tanggal_lahir = data.formulir.tanggal_lahir;
          this.formdata.idagama = data.formulir.idagama;
          this.formdata.id_kebutuhan_khusus = data.formulir.id_kebutuhan_khusus;
          
          this.provinsi_id={
            id:data.formulir.address1_provinsi_id,
            nama:data.formulir.address1_provinsi
          };
          this.kabupaten_id={
            id:data.formulir.address1_kabupaten_id,
            nama:data.formulir.address1_kabupaten
          };
          this.kecamatan_id={
            id:data.formulir.address1_kecamatan_id,
            nama:data.formulir.address1_kecamatan
          };
          this.desa_id={
            id:data.formulir.address1_desa_id,
            nama:data.formulir.address1_kelurahan
          };
          
          this.formdata.address1_rt = data.formulir.address1_rt;
          this.formdata.address1_rw = data.formulir.address1_rw;
          this.formdata.alamat_tempat_tinggal = data.formulir.alamat_tempat_tinggal;
          this.formdata.kode_pos = data.formulir.kode_pos;
          this.formdata.kewarganegaraan = data.formulir.kewarganegaraan;

          this.formdata.asal_sekolah = data.formulir.asal_sekolah;
          this.formdata.anak_ke = data.formulir.anak_ke;
          this.formdata.jumlah_saudara = data.formulir.jumlah_saudara;
          this.formdata.golongan_darah = data.formulir.golongan_darah;
          this.formdata.penyakit = data.formulir.penyakit;
          this.formdata.avoid_food = data.formulir.avoid_food;
          this.formdata.tinggi = data.formulir.tinggi;
          this.formdata.berat_badan = data.formulir.berat_badan;
          this.formdata.ukuran_seragam = data.formulir.ukuran_seragam;
          this.formdata.id_moda = data.formulir.id_moda;
          this.formdata.jarak_ke_sekolah = data.formulir.jarak_ke_sekolah;
          this.formdata.waktu_tempuh = data.formulir.waktu_tempuh;
          
          this.formdata.kode_jenjang = data.formulir.kode_jenjang;
          
          this.$refs.frmdata.resetValidation();
        });
      },
      save: async function() {
        if (this.$refs.frmdata.validate()) {
          this.btnLoading = true;
          await this.$ajax.post("/spsb/formulirpendaftaran/"+this.user_id, {
            _method: "put",
            nama_siswa: this.formdata.nama_siswa,
            nisn: this.formdata.nisn,
            nama_panggilan: this.formdata.nama_panggilan,
            jk: this.formdata.jk,
            nik: this.formdata.nik,
            tempat_lahir: this.formdata.tempat_lahir,
            tanggal_lahir: this.formdata.tanggal_lahir,
            idagama: this.formdata.idagama,
            id_kebutuhan_khusus: this.formdata.id_kebutuhan_khusus,

            address1_provinsi_id: this.provinsi_id.id,
            address1_provinsi: this.provinsi_id.nama,
            address1_kabupaten_id: this.kabupaten_id.id,
            address1_kabupaten: this.kabupaten_id.nama,
            address1_kecamatan_id: this.kecamatan_id.id,
            address1_kecamatan: this.kecamatan_id.nama,
            address1_desa_id: this.desa_id.id,
            address1_kelurahan: this.desa_id.nama,
            address1_rt: this.formdata.address1_rt, 
            address1_rw: this.formdata.address1_rw, 
            alamat_tempat_tinggal: this.formdata.alamat_tempat_tinggal, 
            kode_pos: this.formdata.kode_pos, 
            kewarganegaraan: this.formdata.kewarganegaraan, 

            asal_sekolah: this.formdata.asal_sekolah, 
            anak_ke: this.formdata.anak_ke,
            jumlah_saudara: this.formdata.jumlah_saudara,
            golongan_darah: this.formdata.golongan_darah,
            penyakit: this.formdata.penyakit,
            avoid_food: this.formdata.avoid_food,
            tinggi: this.formdata.tinggi,
            berat_badan: this.formdata.berat_badan,
            ukuran_seragam: this.formdata.ukuran_seragam,
            id_moda: this.formdata.id_moda,
            jarak_ke_sekolah: this.formdata.jarak_ke_sekolah,
            waktu_tempuh: this.formdata.waktu_tempuh,
            
            kode_jenjang: this.formdata.kode_jenjang,
          },
          {
            headers:{
              Authorization: this.$store.getters["auth/Token"]
            }
          }
          ).then(()=>{   
            this.btnLoading = false;
            this.$router.go();
          }).catch(() => {   
            this.btnLoading = false;
          }); 
          this.form_valid=true; 
          this.$refs.frmdata.resetValidation();
        }                             
      },
      kembali()
      {
        if (this.$store.getters["uiadmin/getDefaultDashboard"] =="siswabaru")
        {
          this.$router.push("/dashboard/"+this.$store.getters["auth/AccessToken"]);
        }
        else
        {
          this.$router.go();
        }
      }
    },
    watch:{
      provinsi_id(val) {
        if (val.id != null && val.id != "")
        {
          this.btnLoadingProv=true;
          this.$ajax.get("/datamaster/provinsi/"+val.id+"/kabupaten").then(({ data })=>{                
            this.daftar_kabupaten = data.kabupaten;
            this.btnLoadingProv=false;
          });
          this.daftar_kecamatan=[];
        }
      },
      kabupaten_id(val) {
        if (val.id != null && val.id != "")
        {
          this.btnLoadingKab=true;
          this.$ajax.get("/datamaster/kabupaten/"+val.id+"/kecamatan").then(({ data })=>{
            this.daftar_kecamatan = data.kecamatan;
            this.btnLoadingKab=false;
          });
        }
      },
      kecamatan_id(val) {
        if (val.id != null && val.id != "") {
          this.btnLoadingKec=true;
          this.$ajax.get("/datamaster/kecamatan/" + val.id + "/desa")
            .then(({ data })=>{
              this.daftar_desa = data.desa;
              this.btnLoadingKec=false;
            });
        }
      },
    },
  };
</script>
