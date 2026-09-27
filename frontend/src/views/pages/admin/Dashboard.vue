<template>
  <AdminLayout>		
    <v-container v-if="dashboard== 'siswabaru'">
      <DashboardMB />
    </v-container> 
    <v-container fluid v-else>
      <v-row>
        <v-col xs="12" sm="4" md="3" v-if="$store.getters['auth/can']('DMASTER-GROUP')">
          <v-card
            elevation="0"
            class="clickable"
            min-height="180"
            color="teal darken-1"
            @click.native="$router.push('/dmaster')"
            dark>
            <div class="text-center pt-4">
              <v-btn class="mx-2" fab dark large elevation="0" color="white">
                <v-icon color="teal darken-1">mdi-monitor-multiple</v-icon>
              </v-btn>
            </div>
            <v-card-title class="white--text font-weight-bold justify-center">
              DATA MASTER
            </v-card-title>
            <v-card-text class="white--text text-center">
              Pengaturan berbagai parameter sebagai referensi dari modul-modul lain dalam sistem.
            </v-card-text>
          </v-card>
        </v-col>
        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
        <v-col xs="12" sm="4" md="3" v-if="$store.getters['auth/can']('SPMB-GROUP')">
          <v-card
            elevation="0"
            class="clickable"
            min-height="180"
            color="blue darken-1"
            @click.native="$router.push('/spsb')"
            dark>
            <div class="text-center pt-4">
              <v-btn class="mx-2" fab dark large elevation="0" color="white">
                <v-icon color="blue darken-1">mdi-file-account</v-icon>
              </v-btn>
            </div>
            <v-card-title class="white--text font-weight-bold justify-center">
              SPMB
            </v-card-title>
            <v-card-text class="white--text text-center">
              Modul ini digunakan untuk mengelola Seleksi Penerimaan Murid Baru (SPMB) tahun {{ tahun_pendaftaran }}.
            </v-card-text>
          </v-card>
        </v-col>
        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
        <v-col
          xs="12"
          sm="4"
          md="3"
          v-if="$store.getters['auth/can']('KEUANGAN-GROUP')"
        >
          <v-card
            elevation="0"
            class="clickable"
            min-height="180"
            color="indigo darken-1"
            @click.native="$router.push('/keuangan')"
            dark>
            <div class="text-center pt-4">
              <v-btn class="mx-2" fab dark large elevation="0" color="white">
                <v-icon color="indigo darken-1">mdi-cash-multiple</v-icon>
              </v-btn>
            </div>
            <v-card-title class="white--text font-weight-bold justify-center">
              KEUANGAN
            </v-card-title>
            <v-card-text class="white--text text-center">
              Modul ini digunakan untuk mengelola Keuangan Sekolah.
            </v-card-text>
          </v-card>
        </v-col>
        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
        <v-col xs="12" sm="4" md="3" v-if="$store.getters['auth/can']('SYSTEM-USERS-GROUP')">
          <v-card
            elevation="0"
            class="clickable"
            min-height="180"
            color="deep-orange darken-1"
            @click.native="$router.push('/system-users')"
            dark>
            <div class="text-center pt-4">
              <v-btn class="mx-2" fab dark large elevation="0" color="white">
                <v-icon color="deep-orange darken-1">mdi-account-key</v-icon>
              </v-btn>
            </div>
            <v-card-title class="white--text font-weight-bold justify-center">
              USER SISTEM
            </v-card-title>
            <v-card-text class="white--text text-center">
              Modul ini digunakan untuk mengelola user sistem.
            </v-card-text>
          </v-card>
        </v-col>
        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
        <v-col xs="12" sm="4" md="3" v-if="$store.getters['auth/can']('SYSTEM-SETTING-GROUP')">
          <v-card
            elevation="0"
            class="clickable"
            min-height="180"
            color="pink darken-1"
            @click.native="$router.push('/system-setting')"
            dark>
            <div class="text-center pt-4">
              <v-btn class="mx-2" fab dark large elevation="0" color="white">
                <v-icon color="pink darken-1">mdi-cog</v-icon>
              </v-btn>
            </div>
            <v-card-title class="white--text font-weight-bold justify-center">
              KONFIGURASI SISTEM
            </v-card-title>
            <v-card-text class="white--text text-center">
              Modul ini digunakan untuk mengatur berbagai macam konfigurasi sistem.
            </v-card-text>
          </v-card>
        </v-col>
        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />							
      </v-row>
    </v-container>
  </AdminLayout>
</template>
<script>
  import DashboardMB from "@/components/DashboardSiswaBaru";
  import AdminLayout from "@/views/layouts/AdminLayout";
  export default {
    name: "Dashboard",
    created() {
      this.TOKEN = this.$route.params.token;
      this.breadcrumbs = [
        {
          text: "HOME",
          disabled: false,
          href: "/dashboard/" + this.TOKEN
        },
        {
          text: "DASHBOARD",
          disabled: true,
          href: "#"
        }
      ];		
      this.initialize();
    },
    data: () => ({
      breadcrumbs: [],
      TOKEN: null,
      dashboard: null,
      tahun_pendaftaran: "",
    }),
    methods: {
      initialize: async function() {	            
        await this.$ajax.get("/auth/me",
        {
          headers: {
            Authorization: "Bearer " + this.TOKEN
          }
        }).then(({ data }) => {
          this.dashboard = data.role[0];
          this.$store.dispatch("uiadmin/changeDashboard", this.dashboard);
        });
        await this.$store.dispatch("uiadmin/init", this.$ajax);
        this.tahun_pendaftaran = this.$store.getters["uifront/getTahunPendaftaran"];
      }
    },
    computed: {},
    components: {
      AdminLayout,
      DashboardMB,
    }
  }
</script>
