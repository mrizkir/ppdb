<template>
  <v-row align="center" justify="center" class="mb-4" no-gutters>
    <v-col xs="12" sm="12" md="7">
      <v-form ref="frmdata" v-model="form_valid" lazy-validation>
        <v-card class="mb-4">
          <v-card-title>
            DATA WALI                                                                       
          </v-card-title>
          <v-card-text>            
            <v-text-field
              label="NAMA WALI"
              v-model="formdata.nama_wali"
              :rules="rule_nama_wali"
              filled
            />
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
              v-model="formdata.alamat_tempat_tinggal"
              label="ALAMAT RUMAH"
              :rules="rule_alamat_rumah"
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
            <v-text-field
              v-model="formdata.nomor_hp"
              label="NOMOR HP (TERHUBUNG KE WA)"
              :rules="rule_nomorhp"
              filled
            />
            <v-text-field
              v-model="formdata.email"
              label="EMAIL"
              :rules="rule_email"
              filled
            />
          </v-card-text>
        </v-card>
        <v-card class="mb-4">
          <v-card-title>
            PENDIDIKAN DAN PEKERJAAN
          </v-card-title>
          <v-card-text> 
            <v-text-field
              v-model="formdata.pendidikan"
              label="PENDIDIKAN"
              :rules="rule_pendidikan"
              filled
            />
            <v-text-field
              v-model="formdata.pekerjaan_instansi"
              label="PEKERJAAN DAN INSTANSI"
              :rules="rule_pekerjaan_instansi"
              filled
            />
            <v-text-field
              v-model="formdata.penghasilan_bulanan"
              label="PENGHASILAN BULANAN (RP)"
              :rules="rule_penghasilan"
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
    <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
  </v-row>
</template>
<script>
export default {
  name: 'FormBiodataWali',
  created()
  {
    this.initialize();
  },
  props: {
    user_id: {
      type: String,
      required: true
    }
  },
  data:()=>({
    btnLoading: false,
    btnLoadingProv: false,
    btnLoadingKab: false,
    btnLoadingKec: false,
    
    //form
    form_valid: true,

    menuTanggalLahir: false,
    
    daftar_agama: [],   

    daftar_negara: [],

    daftar_provinsi: [],
    provinsi_id: 0,

    daftar_kabupaten: [],
    kabupaten_id: 0,

    daftar_kecamatan: [],
    kecamatan_id: 0,

    daftar_desa: [],
    desa_id: 0,

    formdata: {
      nama_wali: "",      
      tempat_lahir: "",
      tanggal_lahir: "",
      idagama: 1,
      
      alamat_tempat_tinggal: "", 
      kewarganegaraan: "",
      nomor_hp: "",
      email: "",
      pendidikan: "",
      pekerjaan_instansi: "",
      penghasilan_bulanan: "",
      
      desc: "",
    }, 
    rule_nama_wali: [
      value => !!value || "Nama Wali mohon untuk diisi !!!",
      value => /^[A-Za-z\s\\,\\.]*$/.test(value) || 'Nama Wali hanya boleh string dan spasi',
    ], 
    rule_tempat_lahir: [
      value => !!value || "Tempat Lahir mohon untuk diisi !!!"
    ], 
    rule_tanggal_lahir: [
      value => !!value || "Tanggal Lahir mohon untuk dipilih !!!"
    ], 
    rule_agama: [
      value => !!value || "Mohon agama Wali mohon untuk diisi !!!"
    ], 
    rule_desa: [
      value => !!value || "Mohon Desa untuk dipilih !!!"
    ], 
    rule_alamat_rumah: [
      value => !!value || "Alamat Rumah mohon untuk diisi !!!"
    ], 
    rule_negara: [
      value => !!value || "Mohon Kewarganegaraan  untuk dipilih !!!"
    ], 
    rule_nomorhp: [
      value => !!value || "Nomor HP mohon untuk diisi !!!",
      value => /^\+[1-9]{1}[0-9]{1,14}$/.test(value) || 'Nomor HP hanya boleh angka dan gunakan kode negara didepan seperti +6281214553388',
    ], 
    rule_email: [
      value => !!value || "Email mohon untuk diisi !!!",
      v => /.+@.+\..+/.test(v) || 'Format E-mail mohon di isi dengan benar',
    ],  
    rule_pendidikan: [
      value => !!value || "Jenjang pendidikan mohon untuk diisi !!!", 
    ], 
    rule_pekerjaan_instansi: [
      value => !!value || "Pekerjaan beserta Instansi mohon untuk di isi !!!", 
    ], 
    rule_penghasilan: [
      value => !!value || "Penghasilan mohon untuk untuk di isi !!!",
      value => /^[0-9]+$/.test(value) || 'Penghasilan hanya boleh angka',
    ], 
  }),
  methods: {
    initialize: async function()
    {
      this.$ajax.get('/datamaster/negara').then(({ data }) => {
        this.daftar_negara = data.negara;
      });
      this.$ajax.get('/datamaster/provinsi').then(({ data }) => {
        this.daftar_provinsi = data.provinsi;
      });
      
      await this.$ajax.get('/datamaster/agama').then(({ data }) => { 
        this.daftar_agama = data.agama;
      });
          
      await this.$ajax.get('/spsb/formulirpendaftaran/biodatawali/' + this.user_id,  
        {
          headers: {
            Authorization: this.$store.getters["auth/Token"]
          }
        },
        
      ).then(({ data }) => {
        this.formdata.nama_wali = data.formulir.nama_wali;        
        this.formdata.tempat_lahir = data.formulir.tempat_lahir;
        this.formdata.tanggal_lahir = data.formulir.tanggal_lahir;
        this.formdata.idagama = data.formulir.idagama;
        
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
        
        this.formdata.alamat_tempat_tinggal = data.formulir.alamat_tempat_tinggal;
        this.formdata.kewarganegaraan = data.formulir.kewarganegaraan;
  
        this.formdata.nomor_hp = "+" + data.formulir.nomor_hp;
        this.formdata.email = data.formulir.email;
        this.formdata.pendidikan = data.formulir.pendidikan;
        this.formdata.pekerjaan_instansi = data.formulir.pekerjaan_instansi;
        this.formdata.penghasilan_bulanan = data.formulir.penghasilan_bulanan;
        
        
        this.$refs.frmdata.resetValidation();
      });
    },
    save: async function()
    {
      if (this.$refs.frmdata.validate())
      {
        this.btnLoading = true;
        await this.$ajax.post('/spsb/formulirpendaftaran/biodatawali/' + this.user_id, {
          _method: "put",
          nama_wali: this.formdata.nama_wali,          
          tempat_lahir: this.formdata.tempat_lahir,
          tanggal_lahir: this.formdata.tanggal_lahir,
          idagama: this.formdata.idagama,

          address1_provinsi_id: this.provinsi_id.id,
          address1_provinsi: this.provinsi_id.nama,
          address1_kabupaten_id: this.kabupaten_id.id,
          address1_kabupaten: this.kabupaten_id.nama,
          address1_kecamatan_id: this.kecamatan_id.id,
          address1_kecamatan: this.kecamatan_id.nama,
          address1_desa_id: this.desa_id.id,
          address1_kelurahan: this.desa_id.nama, 
          alamat_tempat_tinggal: this.formdata.alamat_tempat_tinggal,
          kewarganegaraan: this.formdata.kewarganegaraan, 

          nomor_hp: this.formdata.nomor_hp, 
          email: this.formdata.email,
          pendidikan: this.formdata.pendidikan,
          pekerjaan_instansi: this.formdata.pekerjaan_instansi,
          penghasilan_bulanan: this.formdata.penghasilan_bulanan,
        },
        {
          headers: {
            Authorization: this.$store.getters["auth/Token"]
          }
        }
        ).then(()=>{
          this.btnLoading = false;
          this.$router.go();
        }).catch(() => {
          this.btnLoading = false;
        }); 
        this.form_valid = true; 
        this.$refs.frmdata.resetValidation();
      }
    },
    kembali()
    {
      if (this.$store.getters['uiadmin/getDefaultDashboard'] == 'siswabaru')
      {
        this.$router.push('/dashboard/' + this.$store.getters['auth/AccessToken']);
      }
      else
      {
        this.$router.go();
      }
    }
  },
  watch: {
    provinsi_id(val)
    {
      if (val.id != null && val.id != '')
      {
        this.btnLoadingProv=true;
        this.$ajax.get('/datamaster/provinsi/'+val.id+'/kabupaten').then(({ data }) => {
          this.daftar_kabupaten = data.kabupaten;
          this.btnLoadingProv=false;
        });
        this.daftar_kecamatan=[];
      }
    },
    kabupaten_id(val)
    {
      if (val.id != null && val.id != '')
      {
        this.btnLoadingKab=true;
        this.$ajax.get('/datamaster/kabupaten/'+val.id+'/kecamatan').then(({ data }) => {
          this.daftar_kecamatan = data.kecamatan;
          this.btnLoadingKab=false;
        });
      }
    },
    kecamatan_id(val)
    {
      if (val.id != null && val.id != '')
      {
        this.btnLoadingKec=true;
        this.$ajax.get('/datamaster/kecamatan/'+val.id+'/desa').then(({ data }) => {
          this.daftar_desa = data.desa;
          this.btnLoadingKec=false;
        });
      }
    },
  }
}
</script>