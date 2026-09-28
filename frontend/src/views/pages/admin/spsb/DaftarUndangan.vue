<template>
  <SPSBLayout>
    <ModuleHeader>
      <template v-slot:icon>
        mdi-email-newsletter
      </template>
      <template v-slot:name>
        UNDANGAN PMB
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
          Daftar link undangan PMB. Kode OTP berlaku berulang selama masa berlaku. Status pembayaran menunggu verifikasi panitia sebelum calon bisa masuk dashboard.
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
              <v-chip-group v-model="filterUsed" mandatory>
                <v-chip value="all" filter outlined>Semua</v-chip>
                <v-chip value="0" filter outlined>Belum mengisi</v-chip>
                <v-chip value="1" filter outlined>Sudah mengisi</v-chip>
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
            item-key="id"
            sort-by="name"
            class="elevation-1"
            :loading="datatableLoading"
            loading-text="Loading... Please wait"
          >
            <template v-slot:top>
              <v-toolbar flat color="white">
                <v-toolbar-title>DAFTAR UNDANGAN PMB</v-toolbar-title>
              </v-toolbar>
            </template>
            <template v-slot:item.otp="{ item }">
              {{ String(item.otp).padStart(6, "0") }}
            </template>
            <template v-slot:item.berlaku_mulai="{ item }">
              {{ $date(item.berlaku_mulai).format("DD/MM/YYYY") }}
            </template>
            <template v-slot:item.berlaku_sampai="{ item }">
              {{ $date(item.berlaku_sampai).format("DD/MM/YYYY") }}
            </template>
            <template v-slot:item.used="{ item }">
              <v-chip x-small :color="item.used == 1 ? 'success' : 'warning'" dark>
                {{ item.used == 1 ? "SUDAH MENGISI" : "BELUM MENGISI" }}
              </v-chip>
            </template>
            <template v-slot:item.bayar="{ item }">
              <v-chip x-small :color="statusBayarColor(item)" dark>
                {{ statusBayarText(item) }}
              </v-chip>
            </template>
            <template v-slot:item.actions="{ item }">
              <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                  <v-icon
                    small
                    class="mr-2"
                    v-bind="attrs"
                    v-on="on"
                    @click.stop="copyText(undanganUrlOf(item))"
                  >
                    mdi-content-copy
                  </v-icon>
                </template>
                <span>Salin link</span>
              </v-tooltip>
              <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                  <v-icon
                    small
                    color="primary"
                    v-bind="attrs"
                    v-on="on"
                    @click.stop="undanganItem(item)"
                  >
                    mdi-link-variant
                  </v-icon>
                </template>
                <span>Generate ulang</span>
              </v-tooltip>
              <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                  <v-icon
                    small
                    color="red"
                    v-bind="attrs"
                    v-on="on"
                    @click.stop="deleteItem(item)"
                  >
                    mdi-delete
                  </v-icon>
                </template>
                <span>Hapus undangan</span>
              </v-tooltip>
            </template>
            <template v-slot:no-data>
              Data belum tersedia
            </template>
          </v-data-table>
          <v-dialog v-model="dialogUndangan" max-width="560px" persistent>
            <v-form ref="frmundangan" v-model="formUndanganValid" lazy-validation>
              <v-card>
                <v-card-title>
                  <span class="headline">LINK UNDANGAN PMB</span>
                </v-card-title>
                <v-card-text v-if="undanganSelected">
                  <div class="mb-3">
                    <strong>Calon:</strong> {{ undanganSelected.name }}
                  </div>
                  <template v-if="dialogUndanganMode === 'form'">
                    <v-menu
                      ref="menuBerlakuMulai"
                      v-model="menuBerlakuMulai"
                      :close-on-content-click="false"
                      :return-value.sync="formUndangan.berlaku_mulai"
                      transition="scale-transition"
                      offset-y
                      max-width="290px"
                      min-width="290px"
                    >
                      <template v-slot:activator="{ on }">
                        <v-text-field
                          v-model="formUndangan.berlaku_mulai"
                          label="BERLAKU MULAI"
                          readonly
                          outlined
                          v-on="on"
                          :rules="rule_tanggal"
                        />
                      </template>
                      <v-date-picker v-model="formUndangan.berlaku_mulai" no-title scrollable>
                        <v-spacer></v-spacer>
                        <v-btn text color="primary" @click="menuBerlakuMulai = false">BATAL</v-btn>
                        <v-btn text color="primary" @click="$refs.menuBerlakuMulai.save(formUndangan.berlaku_mulai)">OK</v-btn>
                      </v-date-picker>
                    </v-menu>
                    <v-menu
                      ref="menuBerlakuSampai"
                      v-model="menuBerlakuSampai"
                      :close-on-content-click="false"
                      :return-value.sync="formUndangan.berlaku_sampai"
                      transition="scale-transition"
                      offset-y
                      max-width="290px"
                      min-width="290px"
                    >
                      <template v-slot:activator="{ on }">
                        <v-text-field
                          v-model="formUndangan.berlaku_sampai"
                          label="BERLAKU SAMPAI"
                          readonly
                          outlined
                          v-on="on"
                          :rules="rule_tanggal_sampai"
                        />
                      </template>
                      <v-date-picker v-model="formUndangan.berlaku_sampai" no-title scrollable>
                        <v-spacer></v-spacer>
                        <v-btn text color="primary" @click="menuBerlakuSampai = false">BATAL</v-btn>
                        <v-btn text color="primary" @click="$refs.menuBerlakuSampai.save(formUndangan.berlaku_sampai)">OK</v-btn>
                      </v-date-picker>
                    </v-menu>
                  </template>
                  <template v-else-if="undanganResult">
                    <v-text-field
                      :value="undanganUrl"
                      label="LINK UNDANGAN"
                      outlined
                      readonly
                      append-icon="mdi-content-copy"
                      @click:append="copyText(undanganUrl)"
                    />
                    <v-text-field
                      :value="otpDisplay"
                      label="KODE OTP"
                      outlined
                      readonly
                      append-icon="mdi-content-copy"
                      @click:append="copyText(otpDisplay)"
                    />
                    <div>
                      Masa berlaku:
                      {{ $date(undanganResult.berlaku_mulai).format("DD/MM/YYYY") }}
                      s.d.
                      {{ $date(undanganResult.berlaku_sampai).format("DD/MM/YYYY") }}
                    </div>
                    <div class="caption mt-1 grey--text">
                      Kode OTP tetap sama dan dapat dipakai berulang selama masa berlaku.
                    </div>
                  </template>
                </v-card-text>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="blue darken-1" text @click.stop="closeDialogUndangan">TUTUP</v-btn>
                  <v-btn
                    v-if="dialogUndanganMode === 'result'"
                    color="blue darken-1"
                    text
                    @click.stop="dialogUndanganMode = 'form'"
                  >
                    GENERATE ULANG
                  </v-btn>
                  <v-btn
                    v-if="dialogUndanganMode === 'form'"
                    color="blue darken-1"
                    text
                    @click.stop="saveUndangan"
                    :loading="btnLoading"
                    :disabled="!formUndanganValid || btnLoading"
                  >
                    BUAT LINK
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-form>
          </v-dialog>
        </v-col>
      </v-row>
    </v-container>
    <template v-slot:filtersidebar>
      <Filter7 halaman="DaftarUndangan" v-on:changeTahunPendaftaran="changeTahunPendaftaran" v-on:changeJenjang="changeJenjang" ref="filter7" />
    </template>
  </SPSBLayout>
</template>
<script>
  import SPSBLayout from "@/views/layouts/SPSBLayout";
  import ModuleHeader from "@/components/ModuleHeader";
  import Filter7 from "@/components/sidebar/FilterMode7";
  export default {
    name: "DaftarUndangan",
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
          text: "UNDANGAN PMB",
          disabled: true,
          href: "#",
        },
      ];
      const filter = this.$store.dispatch("uiadmin/ensureFilterHalaman", "DaftarUndangan");
      Promise.resolve(filter).then(saved => {
        const kode_jenjang = saved
          ? saved.kode_jenjang
          : this.$store.getters["uiadmin/getKodeJenjang"];
        const tahun = saved
          ? saved.tahun_pendaftaran
          : this.$store.getters["uiadmin/getTahunPendaftaran"];
        this.kode_jenjang = kode_jenjang;
        this.nama_jenjang = this.$store.getters["uiadmin/getNamaJenjang"](kode_jenjang);
        this.tahun_pendaftaran = tahun;
        this.initialize();
      });
    },
    data: () => ({
      firstloading: true,
      kode_jenjang: null,
      tahun_pendaftaran: null,
      nama_jenjang: null,
      breadcrumbs: [],
      btnLoading: false,
      datatableLoading: false,
      datatable: [],
      headers: [
        { text: "NAMA PESERTA DIDIK", value: "name", width: 280, sortable: true },
        { text: "NOMOR HP", value: "nomor_hp", width: 120, sortable: false },
        { text: "KODE OTP", value: "otp", width: 110, sortable: false },
        { text: "BERLAKU MULAI", value: "berlaku_mulai", width: 130, sortable: true },
        { text: "BERLAKU SAMPAI", value: "berlaku_sampai", width: 140, sortable: true },
        { text: "STATUS OTP", value: "used", width: 150, sortable: true },
        { text: "PEMBAYARAN", value: "bayar", width: 140, sortable: false },
        { text: "AKSI", value: "actions", sortable: false, width: 130 },
      ],
      search: "",
      filterUsed: "all",
      dialogUndangan: false,
      dialogUndanganMode: "form",
      formUndanganValid: true,
      menuBerlakuMulai: false,
      menuBerlakuSampai: false,
      undanganSelected: null,
      undanganResult: null,
      formUndangan: {
        berlaku_mulai: "",
        berlaku_sampai: "",
      },
      rule_tanggal: [
        value => !!value || "Tanggal mulai mohon untuk diisi !!!"
      ],
    }),
    computed: {
      otpDisplay() {
        if (!this.undanganResult || !this.undanganResult.otp) {
          return "";
        }
        return String(this.undanganResult.otp).padStart(6, "0");
      },
      undanganUrl() {
        if (!this.otpDisplay) {
          return "";
        }
        return window.location.origin + "/undangan/" + this.otpDisplay;
      },
      rule_tanggal_sampai() {
        return [
          value => !!value || "Tanggal sampai mohon untuk diisi !!!",
          value =>
            !value ||
            !this.formUndangan.berlaku_mulai ||
            value >= this.formUndangan.berlaku_mulai ||
            "Tanggal sampai harus sama atau setelah tanggal mulai",
        ];
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
        const payload = {
          TA: this.tahun_pendaftaran,
          kode_jenjang: this.kode_jenjang,
        };
        if (this.filterUsed !== "all") {
          payload.used = this.filterUsed;
        }
        await this.$ajax
          .post("/spsb/undangan", payload, {
            headers: {
              Authorization: this.$store.getters["auth/Token"],
            },
          })
          .then(({ data }) => {
            this.datatable = data.undangan;
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
      undanganUrlOf(item) {
        return window.location.origin + "/undangan/" + String(item.otp).padStart(6, "0");
      },
      statusBayarText(item) {
        if (this.hasPembayaranUndangan(item) && item.verified == 1) {
          return "SUDAH DIVERIFIKASI";
        }
        if (this.hasPembayaranUndangan(item) && item.konfirmasi_id) {
          return "MENUNGGU VERIFIKASI";
        }
        return "BELUM BAYAR";
      },
      statusBayarColor(item) {
        if (this.hasPembayaranUndangan(item) && item.verified == 1) {
          return "success";
        }
        if (this.hasPembayaranUndangan(item) && item.konfirmasi_id) {
          return "warning";
        }
        return "grey";
      },
      hasPembayaranUndangan(item) {
        return item.used == 1 && !!item.konfirmasi_id;
      },
      undanganItem(item) {
        this.undanganSelected = item;
        this.undanganResult = {
          otp: item.otp,
          berlaku_mulai: item.berlaku_mulai,
          berlaku_sampai: item.berlaku_sampai,
        };
        this.formUndangan = {
          berlaku_mulai: item.berlaku_mulai,
          berlaku_sampai: item.berlaku_sampai,
        };
        this.dialogUndanganMode = "result";
        this.dialogUndangan = true;
      },
      async saveUndangan() {
        if (!this.$refs.frmundangan.validate()) {
          return;
        }
        this.btnLoading = true;
        await this.$ajax
          .post(
            "/spsb/undangan/store",
            {
              user_id: this.undanganSelected.user_id,
              formulir_id: this.undanganSelected.formulir_id,
              berlaku_mulai: this.formUndangan.berlaku_mulai,
              berlaku_sampai: this.formUndangan.berlaku_sampai,
              ta: this.undanganSelected.ta || this.tahun_pendaftaran,
              kode_jenjang: this.undanganSelected.kode_jenjang || this.kode_jenjang,
            },
            {
              headers: {
                Authorization: this.$store.getters["auth/Token"],
              },
            }
          )
          .then(({ data }) => {
            this.btnLoading = false;
            this.undanganResult = data.undangan;
            this.dialogUndanganMode = "result";
            this.initialize();
          })
          .catch(() => {
            this.btnLoading = false;
          });
      },
      copyText(text) {
        if (!text) {
          return;
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(text);
          return;
        }
        const el = document.createElement("textarea");
        el.value = text;
        document.body.appendChild(el);
        el.select();
        document.execCommand("copy");
        document.body.removeChild(el);
      },
      closeDialogUndangan() {
        this.dialogUndangan = false;
        this.undanganSelected = null;
        this.undanganResult = null;
        this.dialogUndanganMode = "form";
      },
      deleteItem(item) {
        const otp = String(item.otp).padStart(6, "0");
        this.$root.$confirm
          .open(
            "Delete",
            "Hapus undangan OTP " +
              otp +
              " milik " +
              item.name +
              "? Formulir jenjang ini juga akan dihapus.",
            { color: "red" }
          )
          .then((confirm) => {
            if (!confirm) {
              return;
            }
            this.btnLoading = true;
            this.$ajax
              .post(
                "/spsb/undangan/" + item.id,
                { _method: "DELETE" },
                {
                  headers: {
                    Authorization: this.$store.getters["auth/Token"],
                  },
                }
              )
              .then(() => {
                const index = this.datatable.indexOf(item);
                if (index > -1) {
                  this.datatable.splice(index, 1);
                }
                this.btnLoading = false;
              })
              .catch(() => {
                this.btnLoading = false;
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
      filterUsed() {
        if (!this.firstloading) {
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
