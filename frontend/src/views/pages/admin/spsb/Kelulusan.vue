<template>
  <SPSBLayout>
    <ModuleHeader>
      <template v-slot:icon>
        mdi-account-check
      </template>
      <template v-slot:name>
        KELULUSAN
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
          Nyatakan kelulusan calon murid pada jenjang ini. Keputusan bisa diubah. Pengumuman ke pendaftar tetap disampaikan panitia melalui WhatsApp.
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
              <v-chip-group v-model="filterStatus" mandatory>
                <v-chip value="all" filter outlined>Semua</v-chip>
                <v-chip value="belum" filter outlined>Belum dinyatakan</v-chip>
                <v-chip value="1" filter outlined>Lulus</v-chip>
                <v-chip value="0" filter outlined>Tidak lulus</v-chip>
              </v-chip-group>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
      <v-row class="mb-4" no-gutters>
        <v-col cols="12">
          <v-data-table
            :headers="headers"
            :items="filteredItems"
            :search="search"
            item-key="formulir_id"
            sort-by="name"
            class="elevation-1"
            :loading="datatableLoading"
            loading-text="Loading... Please wait"
          >
            <template v-slot:top>
              <v-toolbar flat color="white">
                <v-toolbar-title>DAFTAR CALON MURID</v-toolbar-title>
              </v-toolbar>
            </template>
            <template v-slot:item.status="{ item }">
              <v-chip x-small :color="statusColor(item)" dark>
                {{ item.status }}
              </v-chip>
            </template>
            <template v-slot:item.actions="{ item }">
              <v-btn
                x-small
                color="success"
                class="mr-2"
                :disabled="isLulus(item) || savingId === item.formulir_id"
                :loading="savingId === item.formulir_id && savingStatus === 1"
                @click.stop="nyatakan(item, 1)"
              >
                Lulus
              </v-btn>
              <v-btn
                x-small
                color="error"
                :disabled="isTidakLulus(item) || savingId === item.formulir_id"
                :loading="savingId === item.formulir_id && savingStatus === 0"
                @click.stop="nyatakan(item, 0)"
              >
                Tidak lulus
              </v-btn>
            </template>
            <template v-slot:no-data>
              Data belum tersedia
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-container>
    <template v-slot:filtersidebar>
      <Filter7
        v-on:changeTahunPendaftaran="changeTahunPendaftaran"
        v-on:changeJenjang="changeJenjang"
        ref="filter7"
      />
    </template>
  </SPSBLayout>
</template>
<script>
import SPSBLayout from "@/views/layouts/SPSBLayout";
import ModuleHeader from "@/components/ModuleHeader";
import Filter7 from "@/components/sidebar/FilterMode7";

export default {
  name: "Kelulusan",
  created() {
    this.breadcrumbs = [
      {
        text: "HOME",
        disabled: false,
        href: "/dashboard/" + this.$store.getters["auth/AccessToken"],
      },
      {
        text: "SPMB",
        disabled: false,
        href: "/spsb",
      },
      {
        text: "KELULUSAN",
        disabled: true,
        href: "#",
      },
    ];
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
    datatableLoading: false,
    datatable: [],
    headers: [
      { text: "NAMA", value: "name", sortable: true },
      { text: "NOMOR HP", value: "nomor_hp", sortable: false },
      { text: "JENJANG", value: "nkelas", sortable: true },
      { text: "STATUS", value: "status", sortable: true },
      { text: "AKSI", value: "actions", sortable: false, width: 220 },
    ],
    search: "",
    filterStatus: "all",
    savingId: null,
    savingStatus: null,
  }),
  computed: {
    filteredItems() {
      if (this.filterStatus === "belum") {
        return this.datatable.filter((item) => item.ket_lulus === null || item.ket_lulus === "");
      }
      if (this.filterStatus === "1" || this.filterStatus === "0") {
        return this.datatable.filter((item) => String(item.ket_lulus) === this.filterStatus);
      }
      return this.datatable;
    },
  },
  methods: {
    changeTahunPendaftaran(tahun) {
      this.tahun_pendaftaran = tahun;
    },
    changeJenjang(id) {
      this.kode_jenjang = id;
    },
    initialize: async function() {
      this.datatableLoading = true;
      await this.$ajax
        .post(
          "/spsb/kelulusan",
          {
            TA: this.tahun_pendaftaran,
            kode_jenjang: this.kode_jenjang,
          },
          {
            headers: {
              Authorization: this.$store.getters["auth/Token"],
            },
          }
        )
        .then(({ data }) => {
          this.datatable = data.psb || [];
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
    isLulus(item) {
      return String(item.ket_lulus) === "1";
    },
    isTidakLulus(item) {
      return String(item.ket_lulus) === "0";
    },
    statusColor(item) {
      if (this.isLulus(item)) {
        return "success";
      }
      if (this.isTidakLulus(item)) {
        return "error";
      }
      return "grey";
    },
    nyatakan(item, ketLulus) {
      const label = ketLulus === 1 ? "LULUS" : "TIDAK LULUS";
      this.$root.$confirm
        .open(
          "Kelulusan",
          "Nyatakan " + item.name + " " + label + " pada jenjang " + this.nama_jenjang + "?",
          { color: ketLulus === 1 ? "success" : "red" }
        )
        .then((confirm) => {
          if (!confirm) {
            return;
          }
          this.savingId = item.formulir_id;
          this.savingStatus = ketLulus;
          this.$ajax
            .post(
              "/spsb/kelulusan/nyatakan",
              {
                formulir_id: item.formulir_id,
                ket_lulus: ketLulus,
              },
              {
                headers: {
                  Authorization: this.$store.getters["auth/Token"],
                },
              }
            )
            .then(() => {
              this.savingId = null;
              this.savingStatus = null;
              this.initialize();
            })
            .catch(() => {
              this.savingId = null;
              this.savingStatus = null;
            });
        });
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
  },
  components: {
    SPSBLayout,
    ModuleHeader,
    Filter7,
  },
};
</script>
