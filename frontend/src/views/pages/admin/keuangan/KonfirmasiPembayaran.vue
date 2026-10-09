<template>
  <KeuanganLayout :showrightsidebar="dashboard!='mahasiswa'&&dashboard!='mahasiswabaru'">
    <ModuleHeader>
      <template v-slot:icon>
        mdi-cash-check
      </template>
      <template v-slot:name>
        KONFIRMASI PEMBAYARAN
      </template>
      <template v-slot:subtitle>
        TAHUN PENDAFTARAN {{ tahun_pendaftaran }} - {{ nama_jenjang }}
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
          Halaman ini berisi bukti bayar yang diunggah calon peserta didik. Verifikasi pembayaran dilakukan di sini, terpisah dari data pendaftar.
        </v-alert>
      </template>
    </ModuleHeader>
    <v-container fluid>
      <v-row class="mb-4" no-gutters>
        <v-col cols="12">
          <v-card>
            <v-card-text>
              <v-text-field
                v-model="search"
                append-icon="mdi-database-search"
                label="Search"
                single-line
                hide-details
                class="mb-3"
              />
              <v-chip-group v-model="filterVerified" mandatory>
                <v-chip value="all" filter outlined>Semua</v-chip>
                <v-chip value="0" filter outlined>Menunggu verifikasi</v-chip>
                <v-chip value="1" filter outlined>Sudah diverifikasi</v-chip>
              </v-chip-group>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
      <v-row class="mb-4" no-gutters>
        <v-col cols="12">
          <v-data-table
            :headers="headers"
            :items="datatable"
            :search="search"
            item-key="transaksi_id"
            sort-by="created_at"
            sort-desc
            class="elevation-1"
            :loading="datatableLoading"
            loading-text="Loading... Please wait"
          >
            <template v-slot:top>
              <v-toolbar flat color="white">
                <v-toolbar-title>DAFTAR KONFIRMASI PEMBAYARAN</v-toolbar-title>
                <v-dialog v-model="dialogdetailitem" max-width="750px" persistent>
                  <v-card v-if="formdata">
                    <v-card-title>
                      <span class="headline">DETAIL BUKTI BAYAR</span>
                    </v-card-title>
                    <v-card-text>
                      <v-row no-gutters>
                        <v-col xs="12" sm="6" md="6">
                          <v-card flat>
                            <v-card-title>NAMA MURID :</v-card-title>
                            <v-card-subtitle>{{ formdata.name }}</v-card-subtitle>
                          </v-card>
                        </v-col>
                        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
                        <v-col xs="12" sm="6" md="6">
                          <v-card flat>
                            <v-card-title>USERNAME :</v-card-title>
                            <v-card-subtitle>{{ formdata.username }}</v-card-subtitle>
                          </v-card>
                        </v-col>
                      </v-row>
                      <v-row no-gutters>
                        <v-col xs="12" sm="6" md="6">
                          <v-card flat>
                            <v-card-title>KODE BILLING :</v-card-title>
                            <v-card-subtitle>{{ formdata.no_transaksi }}</v-card-subtitle>
                          </v-card>
                        </v-col>
                        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
                        <v-col xs="12" sm="6" md="6">
                          <v-card flat>
                            <v-card-title>CHANNEL PEMBAYARAN :</v-card-title>
                            <v-card-subtitle>{{ formdata.nama_channel }}</v-card-subtitle>
                          </v-card>
                        </v-col>
                      </v-row>
                      <v-row no-gutters>
                        <v-col xs="12" sm="6" md="6">
                          <v-card flat>
                            <v-card-title>TANGGAL KONFIRMASI :</v-card-title>
                            <v-card-subtitle>
                              {{ formdata.tanggal_bayar ? $date(formdata.tanggal_bayar).format("DD/MM/YYYY") : "-" }}
                            </v-card-subtitle>
                          </v-card>
                        </v-col>
                        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
                        <v-col xs="12" sm="6" md="6">
                          <v-card flat>
                            <v-card-title>TOTAL BAYAR :</v-card-title>
                            <v-card-subtitle>{{ formdata.total_bayar | formatUang }}</v-card-subtitle>
                          </v-card>
                        </v-col>
                      </v-row>
                      <v-row no-gutters>
                        <v-col xs="12" sm="6" md="6">
                          <v-card flat>
                            <v-card-title>NOMOR REKENING PENGIRIM :</v-card-title>
                            <v-card-subtitle>{{ formdata.nomor_rekening_pengirim }}</v-card-subtitle>
                          </v-card>
                        </v-col>
                        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
                        <v-col xs="12" sm="6" md="6">
                          <v-card flat>
                            <v-card-title>NAMA REKENING PENGIRIM :</v-card-title>
                            <v-card-subtitle>{{ formdata.nama_rekening_pengirim }}</v-card-subtitle>
                          </v-card>
                        </v-col>
                      </v-row>
                      <v-row no-gutters>
                        <v-col xs="12" sm="6" md="6">
                          <v-card flat>
                            <v-card-title>NAMA BANK PENGIRIM :</v-card-title>
                            <v-card-subtitle>{{ formdata.nama_bank_pengirim }}</v-card-subtitle>
                          </v-card>
                        </v-col>
                        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
                        <v-col xs="12" sm="6" md="6">
                          <v-card flat>
                            <v-card-title>STATUS :</v-card-title>
                            <v-card-subtitle>{{ formdata.nama_status }}</v-card-subtitle>
                          </v-card>
                        </v-col>
                      </v-row>
                      <v-row no-gutters v-if="formdata.bukti_bayar">
                        <v-col cols="12">
                          <v-card-title>BUKTI BAYAR</v-card-title>
                          <a
                            :href="$api.storageURL + '/' + formdata.bukti_bayar"
                            target="_blank"
                            rel="noopener"
                          >
                            <v-img
                              contain
                              max-height="480"
                              :src="$api.storageURL + '/' + formdata.bukti_bayar"
                            />
                          </a>
                        </v-col>
                      </v-row>
                    </v-card-text>
                    <v-card-actions>
                      <v-spacer></v-spacer>
                      <v-btn
                        small
                        class="primary"
                        @click.stop="verifikasi(formdata)"
                        :disabled="btnLoading"
                        :loading="btnLoading"
                        v-if="Number(formdata.verified) === 0"
                      >
                        <v-icon left>mdi-email-check</v-icon>
                        VERIFIKASI BUKTI BAYAR
                      </v-btn>
                      <v-btn color="blue darken-1" text @click.stop="closedialogdetailitem">KELUAR</v-btn>
                    </v-card-actions>
                  </v-card>
                </v-dialog>
              </v-toolbar>
            </template>
            <template v-slot:item.foto="{ item }">
              <v-avatar size="30">
                <v-img :src="$api.storageURL + '/' + item.foto" />
              </v-avatar>
            </template>
            <template v-slot:item.tanggal_bayar="{ item }">
              {{ item.tanggal_bayar ? $date(item.tanggal_bayar).format("DD/MM/YYYY") : "-" }}
            </template>
            <template v-slot:item.total_bayar="{ item }">
              {{ item.total_bayar | formatUang }}
            </template>
            <template v-slot:item.verified="{ item }">
              <v-chip x-small :color="statusBayarColor(item)" dark>
                {{ statusBayarText(item) }}
              </v-chip>
            </template>
            <template v-slot:item.actions="{ item }">
              <v-icon small class="mr-2" @click.stop="viewItem(item)">
                mdi-eye
              </v-icon>
            </template>
            <template v-slot:no-data>
              Data belum tersedia
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-container>
    <template v-slot:filtersidebar>
      <Filter7 v-on:changeTahunPendaftaran="changeTahunPendaftaran" v-on:changeJenjang="changeJenjang" ref="filter7" />
    </template>
  </KeuanganLayout>
</template>
<script>
import KeuanganLayout from "@/views/layouts/KeuanganLayout";
import ModuleHeader from "@/components/ModuleHeader";
import Filter7 from "@/components/sidebar/FilterMode7";
export default {
  name: "KonfirmasiPembayaranAdmin",
  async created() {
    await this.$store.dispatch("uiadmin/init", this.$ajax);
    this.dashboard = this.$store.getters["uiadmin/getDefaultDashboard"];
    this.breadcrumbs = [
      {
        text: "HOME",
        disabled: false,
        href: "/dashboard/" + this.$store.getters["auth/AccessToken"],
      },
      {
        text: "KEUANGAN",
        disabled: false,
        href: "/keuangan",
      },
      {
        text: "KONFIRMASI PEMBAYARAN",
        disabled: true,
        href: "#",
      },
    ];
    this.breadcrumbs[1].disabled = this.dashboard == "siswabaru" || this.dashboard == "mahasiswa";
    const kode_jenjang = this.$store.getters["uiadmin/getKodeJenjang"];
    this.kode_jenjang = kode_jenjang;
    this.nama_jenjang = this.$store.getters["uiadmin/getNamaJenjang"](kode_jenjang);
    this.tahun_pendaftaran = this.$store.getters["uiadmin/getTahunPendaftaran"];
    this.initialize();
  },
  data: () => ({
    firstloading: true,
    kode_jenjang: null,
    tahun_pendaftaran: null,
    nama_jenjang: null,
    breadcrumbs: [],
    dashboard: null,
    datatableLoading: false,
    btnLoading: false,
    headers: [
      { text: "", value: "foto", width: 70, sortable: false },
      { text: "NAMA MURID", value: "name", width: 280, sortable: true },
      { text: "KODE BILLING", value: "no_transaksi", sortable: true },
      { text: "CHANNEL", value: "nama_channel", sortable: false },
      { text: "TGL. BAYAR", value: "tanggal_bayar", sortable: true },
      { text: "TOTAL", value: "total_bayar", sortable: true },
      { text: "STATUS", value: "verified", sortable: true, width: 170 },
      { text: "AKSI", value: "actions", sortable: false, width: 80 },
    ],
    search: "",
    filterVerified: "0",
    datatable: [],
    dialogdetailitem: false,
    formdata: null,
  }),
  methods: {
    changeTahunPendaftaran(tahun) {
      this.tahun_pendaftaran = tahun;
    },
    changeJenjang(id) {
      this.kode_jenjang = id;
    },
    initialize: async function() {
      this.datatableLoading = true;
      const payload = {
        TA: this.tahun_pendaftaran,
        kode_jenjang: this.kode_jenjang,
      };
      if (this.filterVerified !== "all") {
        payload.verified = this.filterVerified;
      }
      await this.$ajax
        .post("/keuangan/konfirmasipembayaran", payload, {
          headers: {
            Authorization: this.$store.getters["auth/Token"],
          },
        })
        .then(({ data }) => {
          this.datatable = data.konfirmasi;
          this.datatableLoading = false;
        })
        .catch(() => {
          this.datatableLoading = false;
        });
      this.firstloading = false;
      if (this.$refs.filter7) {
        this.$refs.filter7.setFirstTimeLoading(this.firstloading);
      }
    },
    statusBayarText(item) {
      return Number(item.verified) === 1 ? "SUDAH DIVERIFIKASI" : "MENUNGGU VERIFIKASI";
    },
    statusBayarColor(item) {
      return Number(item.verified) === 1 ? "success" : "warning";
    },
    viewItem(item) {
      this.formdata = Object.assign({}, item);
      this.dialogdetailitem = true;
    },
    async verifikasi(item) {
      this.btnLoading = true;
      try {
        await this.$ajax.post(
          "/keuangan/konfirmasipembayaran/" + item.transaksi_id,
          {
            _method: "put",
            verified: 1,
            ta: item.ta || this.tahun_pendaftaran,
            kode_jenjang: item.kode_jenjang || this.kode_jenjang,
            formulir_id: item.formulir_id,
          },
          {
            headers: {
              Authorization: this.$store.getters["auth/Token"],
            },
          }
        );
        this.dialogdetailitem = false;
        await this.initialize();
      } catch (e) {
        // interceptor already surfaces the error
      } finally {
        this.btnLoading = false;
      }
    },
    closedialogdetailitem() {
      this.dialogdetailitem = false;
      this.formdata = null;
    },
  },
  watch: {
    tahun_pendaftaran() {
      if (!this.firstloading) {
        this.initialize();
      }
    },
    kode_jenjang(val) {
      if (!this.firstloading) {
        this.nama_jenjang = this.$store.getters["uiadmin/getNamaJenjang"](val);
        this.initialize();
      }
    },
    filterVerified() {
      if (!this.firstloading) {
        this.initialize();
      }
    },
  },
  components: {
    KeuanganLayout,
    ModuleHeader,
    Filter7,
  },
};
</script>
