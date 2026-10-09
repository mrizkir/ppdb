<template>
  <SPSBLayout>
    <ModuleHeader>
      <template v-slot:icon>
        mdi-file-eye
      </template>
      <template v-slot:name>
        REVIEW PERSYARATAN
      </template>
      <template v-slot:subtitle>
        {{ calon ? calon.name : "" }}
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
          Semua berkas calon ini ditampilkan dalam satu halaman.
        </v-alert>
      </template>
    </ModuleHeader>
    <v-container fluid>
      <v-row class="mb-4" no-gutters>
        <v-col cols="12">
          <v-btn text color="grey darken-1" @click="kembali">
            <v-icon left>mdi-arrow-left</v-icon>
            KEMBALI KE DAFTAR
          </v-btn>
        </v-col>
      </v-row>
      <v-row v-if="loading" no-gutters>
        <v-col cols="12" class="text-center py-8">
          <v-progress-circular indeterminate color="primary" />
        </v-col>
      </v-row>
      <v-alert v-else-if="pageError" type="error">
        {{ pageError }}
      </v-alert>
      <template v-else-if="calon">
        <v-row class="mb-4" no-gutters>
          <v-col cols="12" sm="4">
            <v-card flat>
              <v-card-title class="subtitle-1">NAMA</v-card-title>
              <v-card-subtitle>{{ calon.name }}</v-card-subtitle>
            </v-card>
          </v-col>
          <v-col cols="12" sm="4">
            <v-card flat>
              <v-card-title class="subtitle-1">USERNAME</v-card-title>
              <v-card-subtitle>{{ calon.username }}</v-card-subtitle>
            </v-card>
          </v-col>
          <v-col cols="12" sm="4">
            <v-card flat>
              <v-card-title class="subtitle-1">NOMOR HP</v-card-title>
              <v-card-subtitle>{{ calon.nomor_hp }}</v-card-subtitle>
            </v-card>
          </v-col>
        </v-row>
        <v-row>
          <v-col
            v-for="berkas in daftarBerkas"
            :key="berkas.key"
            cols="12"
            md="6"
          >
            <v-card outlined class="mb-2">
              <v-card-title class="subtitle-1">{{ berkas.judul }}</v-card-title>
              <v-card-text>
                <div
                  v-if="!calon[berkas.key]"
                  class="grey--text py-8 text-center"
                >
                  Belum diunggah
                </div>
                <iframe
                  v-else-if="isPdf(calon[berkas.key])"
                  :src="fileUrl(calon[berkas.key])"
                  :title="berkas.judul"
                  class="review-berkas"
                />
                <v-img
                  v-else
                  :src="fileUrl(calon[berkas.key])"
                  contain
                  max-height="480"
                  class="review-berkas grey lighten-4"
                />
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </template>
    </v-container>
  </SPSBLayout>
</template>
<script>
  import SPSBLayout from "@/views/layouts/SPSBLayout";
  import ModuleHeader from "@/components/ModuleHeader";

  export default {
    name: "ReviewPersyaratan",
    components: {
      SPSBLayout,
      ModuleHeader,
    },
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
          text: "PERSYARATAN PPDB",
          disabled: false,
          href: "/spsb/persyaratan",
        },
        {
          text: "REVIEW",
          disabled: true,
          href: "#",
        },
      ];
      this.load();
    },
    data: () => ({
      loading: false,
      pageError: "",
      calon: null,
      breadcrumbs: [],
      daftarBerkas: [
        { key: "file_fotoselfi", judul: "FOTO WEFIE / KELUARGA" },
        { key: "file_ktp_ayah", judul: "PINDAIAN KTP AYAH" },
        { key: "file_ktp_ibu", judul: "PINDAIAN KTP IBU" },
        { key: "file_kk", judul: "PINDAIAN KARTU KELUARGA" },
        { key: "file_aktalahir", judul: "PINDAIAN AKTA KELAHIRAN" },
        {
          key: "file_screenshoot_medsos",
          judul: "TANGKAPAN LAYAR MEDIA SOSIAL",
        },
        { key: "file_sertifikat", judul: "SERTIFIKAT PENGHARGAAN" },
        { key: "file_nisn", judul: "KARTU NISN" },
        { key: "file_kia", judul: "KARTU KIA" },
        { key: "file_pemeriksaan_ahli", judul: "HASIL PEMERIKSAAN AHLI" },
      ],
    }),
    methods: {
      fileUrl(path) {
        return this.$api.storageURL + "/" + path;
      },
      isPdf(path) {
        return String(path || "")
          .toLowerCase()
          .endsWith(".pdf");
      },
      kembali() {
        this.$router.push("/spsb/persyaratan");
      },
      async load() {
        this.loading = true;
        this.pageError = "";
        await this.$ajax
          .get("/spsb/psbpersyaratan/review/" + this.$route.params.id, {
            headers: {
              Authorization: this.$store.getters["auth/Token"],
            },
          })
          .then(({ data }) => {
            this.calon = data.calon;
            this.loading = false;
          })
          .catch(({ response }) => {
            this.loading = false;
            const message = response && response.data && response.data.message;
            this.pageError = Array.isArray(message)
              ? message[0]
              : message || "Berkas persyaratan gagal dimuat.";
          });
      },
    },
  };
</script>
<style scoped>
  .review-berkas {
    width: 100%;
    height: 480px;
    border: 0;
  }
</style>
