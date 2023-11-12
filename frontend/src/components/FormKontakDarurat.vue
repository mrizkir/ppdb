<template>
  <v-row align="center" justify="center" class="mb-4" no-gutters>
    <v-col xs="12" sm="12" md="7">
      <v-form ref="frmdata" v-model="form_valid" lazy-validation>
        <v-card class="mb-4">
          <v-card-title>
            KONTAK DARURAT DAN KESEDIAAN
          </v-card-title>
          <v-card-text>            
            <v-text-field
              label="NAMA YANG DIHUBUNGI"
              v-model="formdata.nama_kontak"
              :rules="rule_nama_kontak"
              filled
            />
            <v-text-field
              label="HUBUNGAN DENGAN MURID"
              v-model="formdata.hubungan" 
              :rules="rule_hubungan"
              filled
            />            
            <v-text-field
              v-model="formdata.nomor_hp"
              label="NOMOR HP (TERHUBUNG KE WA)"
              :rules="rule_nomorhp"
              filled
            />
            <v-text-field
              v-model="formdata.alamat_kontak"
              label="ALAMAT KONTAK"
              :rules="rule_alamat_rumah"
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
    name: 'FormKontakDarurat',
    created() {
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
      
      //form
      form_valid: true,
      formdata: {
        nama_kontak: "",
        hubungan: "",      
        alamat_kontak: "",      
        nomor_hp: "",
      },
      rule_hubungan: [
        value => !!value || "Mohon hubungan dengan Peserta Didik untuk dipilih !!!"
      ],
      rule_nama_kontak: [
        value => !!value || "Nama Peserta Didik mohon untuk diisi !!!",
        value => /^[A-Za-z\s\\,\\.]*$/.test(value) || 'Nama Peserta Didik hanya boleh string dan spasi',
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
    }),
    methods: {
      initialize: async function() {
        await this.$ajax.get('/spsb/formulirpendaftaran/kontakdarurat/' + this.user_id,
          {
            headers: {
              Authorization: this.$store.getters["auth/Token"]
            }
          },
          
        ).then(({ data }) => {
          this.formdata.nama_kontak = data.formulir.nama_kontak;
          this.formdata.hubungan = data.formulir.hubungan;
          this.formdata.alamat_kontak = data.formulir.alamat_kontak;            
          this.formdata.nomor_hp = "+" + data.formulir.nomor_hp;            
          this.$refs.frmdata.resetValidation();
        });
      },
      save: async function()
      {
        if (this.$refs.frmdata.validate())
        {
          this.btnLoading = true;
          await this.$ajax.post('/spsb/formulirpendaftaran/kontakdarurat/' + this.user_id, {
            _method: "put",
            nama_kontak: this.formdata.nama_kontak,
            hubungan: this.formdata.hubungan,       
            alamat_kontak: this.formdata.alamat_kontak,
            nomor_hp: this.formdata.nomor_hp, 
          },
          {
            headers: {
              Authorization: this.$store.getters["auth/Token"]
            }
          }
          ).then(() => {
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
  };
</script>
