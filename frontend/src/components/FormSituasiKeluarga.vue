<template>
   <v-row align="center" justify="center" class="mb-4" no-gutters>
    <v-col xs="12" sm="12" md="7">
      <v-form ref="frmdata" v-model="form_valid" lazy-validation>
        <v-card class="mb-4">
          <v-card-title>
            SITUASI KELUARGA
          </v-card-title>
          <v-card-text>
            <v-text-field
              label="CALON PESERTA DIDIK TINGGAL BERSAMA"
              v-model="formdata.tinggal_bersama"
              :rules="rule_tinggal_bersama"
              filled
            />
            <v-select
              label="STATUS PERNIKAHAN ORANG TUA SAAT PENDAFTARAN"
              :items="daftar_status_penikahan"
              v-model="formdata.status_pernikahan"                   
              :rules="rule_status_pernikahan"
              filled
            /> 
            <v-textarea
              label="ANGGOTA KELUARGA/ORANG LAIN YANG TINGGAL SERUMAH TULISKAN SECARA TERPERICI"
              v-model="formdata.desc"              
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
              @click.stop="kembali"
            >
              KEMBALI
            </v-btn>
            <v-btn 
              color="blue darken-1" 
              text 
              @click.stop="save" 
              :loading="btnLoading"
              :disabled="!form_valid || btnLoading"
            >
              SIMPAN
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-form>
    </v-col>
    <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>  
  </v-row>
</template>
<script>
  export default {
    name: 'FormSituasiKeluarga',
    created() {
      this.initialize();
    },
    props: {        
      user_id:{            
        type: String,
        required: true,
      },
    },
    data:()=>({
      btnLoading: false,
      //form
      form_valid: true,
      daftar_status_penikahan: [
        {
          value: 'UTUH',
          text: 'UTUH'
        },
        {
          value: 'PISAH',
          text: 'BERPISAH'
        }, 
        {
          value: 'CERAI',
          text: 'CERAI'
        },    
      ],
      formdata:{
        tinggal_bersama: "",
        status_pernikahan: 'UTUH', 
        desc: "",
      }, 
      rule_tinggal_bersama: [
        value => !!value || "Peserta Didik tinggal bersama siapa mohon untuk diisi !!!",
        value => /^[A-Za-z\s\\,\\.]*$/.test(value) || 'Tempat tinggal Peserta Didik hanya boleh string dan spasi',
      ],               
      rule_status_pernikahan: [
        value => !!value || "Mohon Status Pernikahan untuk dipilih !!!"
      ], 
    }),
    methods: {
      initialize: async function()
      {        
        await this.$ajax.get('/spsb/formulirpendaftaran/situasikeluarga/'+this.user_id,  
          {
            headers:{
              Authorization: this.$store.getters["auth/Token"]
            }
          },
          
        ).then(({ data }) => {   
          this.formdata.tinggal_bersama = data.formulir.tinggal_bersama;
          this.formdata.status_pernikahan = data.formulir.status_pernikahan;
          this.formdata.desc = data.formulir.desc;
          this.$refs.frmdata.resetValidation();
        });
      },
      save: async function() {
        if (this.$refs.frmdata.validate()) {
          this.btnLoading = true;
          await this.$ajax.post('/spsb/formulirpendaftaran/situasikeluarga/' + this.user_id, {
            _method: "put",
            tinggal_bersama: this.formdata.tinggal_bersama,
            status_pernikahan: this.formdata.status_pernikahan, 
            desc: this.formdata.desc, 
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
        if (this.$store.getters['uiadmin/getDefaultDashboard'] == 'siswabaru')
        {
          this.$router.push('/dashboard/'+this.$store.getters['auth/AccessToken']);
        }
        else
        {
          this.$router.go();
        }
      }
    }, 
  };
</script>
