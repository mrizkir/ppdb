<template>
  <SystemConfigLayout>
    <ModuleHeader>
      <template v-slot:icon>
        mdi-face-profile
      </template>
      <template v-slot:name>
        PPDB
      </template>
      <template v-slot:breadcrumbs>
        <v-breadcrumbs :items="breadcrumbs" class="pa-0">
          <template v-slot:divider>
            <v-icon>mdi-chevron-right</v-icon>
          </template>
        </v-breadcrumbs>
      </template>
      <template v-slot:desc>
        <v-alert color="cyan" border="left" colored-border type="info">
          berisi konfigurasi ppdb sekolah
        </v-alert>
      </template>
    </ModuleHeader>
    <v-container fluid>
      <v-row class="mb-4" no-gutters>
        <v-col cols="12">
          <v-form ref="frmdata" v-model="form_valid" lazy-validation>
            <v-card>
              <v-card-text>
                <v-select
                  v-model="formdata.tahun_pendaftaran"
                  :items="daftar_ta"
                  label="TAHUN PENDAFTARAN"
                  item-text="tahun_ajaran"
                  item-value="tahun"
                  filled
                />
                <v-switch v-model="formdata.buka_ppdb" flat label="BUKA PPDB" />
                <v-switch v-model="formdata.buka_ppdb_gtk" flat label="BUKA PPDB GTK"/>
              </v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
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
      </v-row>
    </v-container>
  </SystemConfigLayout>
</template>
<script>
  import {mapGetters} from "vuex";
  import SystemConfigLayout from "@/views/layouts/SystemConfigLayout";
  import ModuleHeader from "@/components/ModuleHeader";
  export default {
    name: "SettingPPDB",
    created() {
      this.breadcrumbs = [
        {
          text: "HOME",
          disabled: false,
          href: "/dashboard/" + this.ACCESS_TOKEN,
        },
        {
          text: "KONFIGURASI SISTEM",
          disabled: false,
          href: "/system-setting",
        },
        {
          text: "SEKOLAH",
          disabled: false,
          href: "#",
        },
        {
          text: "PPDB",
          disabled: true,
          href: "#",
        },
      ];
      this.initialize();
    },
    data: () => ({
      breadcrumbs: [],
      datatableLoading: false,
      btnLoading: false,
      //form
      form_valid: true,
      daftar_ta: [],
      formdata: {
        tahun_pendaftaran: "",
        buka_ppdb: false,
        buka_ppdb_gtk: false,
      },
      //form rules        
      rule_tahun_pendaftaran: [
        value => !!value || "Mohon untuk di pilih tahun pendaftaran!!!",
      ],
    }),
    methods: {
      initialize: async function() {
        this.$ajax.get("/datamaster/tahunajaran").then(({ data }) => {
          this.daftar_ta = data.ta;
        }); 

        this.datatableLoading = true;
        await this.$ajax.get("/system/setting/variables",
          {
            headers: {
              Authorization: this.TOKEN,
            },
          })
          .then(({ data }) => {
            let setting = data.setting;
            this.formdata.tahun_pendaftaran = parseInt(setting.DEFAULT_TAHUN_PENDAFTARAN);
            this.formdata.buka_ppdb = setting.DEFAULT_BUKA_PPDB == "0" ? false : true;
            this.formdata.buka_ppdb_gtk = setting.DEFAULT_BUKA_PPDB_GTK == "0" ? false : true;
            this.datatableLoading = false;
          });
      },
      save() {
        if (this.$refs.frmdata.validate()) {
          this.btnLoading = true;
          this.$ajax
            .post(
              "/system/setting/variables",
              {
                _method: "PUT",
                pid: "PPDB",
                setting: JSON.stringify({
                  203: this.formdata.tahun_pendaftaran,
                  206: this.formdata.buka_ppdb,
                  207: this.formdata.buka_ppdb_gtk,
                }),
              },
              {
                headers: {
                  Authorization: this.TOKEN,
                },
              }
            )
            .then(() => {
              this.btnLoading = false;
            })
            .catch(() => {
              this.btnLoading = false;
            });
        }
      },
    },
    computed: {
      ...mapGetters("auth", {
        ACCESS_TOKEN: "AccessToken",
        TOKEN: "Token",
      }),
    },
    components: {
      SystemConfigLayout,
      ModuleHeader,
    },
  };
</script>
