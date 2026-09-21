<template>
  <FrontLayout>
    <v-container class="fill-height" fluid>
      <v-row align="center" justify="center" no-gutters>
        <v-col cols="12">
          <h1 class="text-center display-1 font-weight-black primary--text">
            UNDANGAN PMB
          </h1>
        </v-col>
        <v-col xs="12" md="6" sm="12">
          <v-alert
            v-if="pageError"
            outlined
            dense
            type="error"
            :value="true"
          >
            {{ pageError }}
          </v-alert>

          <v-card
            v-if="step === 'waiting'"
            outlined
          >
            <v-card-title>Menunggu Verifikasi Pembayaran</v-card-title>
            <v-card-text>
              <v-alert
                outlined
                dense
                type="info"
                :value="true"
              >
                Bukti pembayaran sudah diterima. Silahkan menunggu verifikasi panitia sekolah.
                Setelah diverifikasi, silahkan login dengan username dan password.
              </v-alert>
            </v-card-text>
          </v-card>

          <v-form
            v-if="step === 'otp'"
            ref="frmotp"
            @keyup.native.enter="verifyOtp"
            lazy-validation
          >
            <v-card outlined>
              <v-card-title>Masukan Kode OTP</v-card-title>
              <v-card-subtitle v-if="undanganPreview">
                {{ undanganPreview.name }}
                <br />
                {{ undanganPreview.nama_jenjang }} - {{ undanganPreview.ta }}
                <br />
                Berlaku
                {{ $date(undanganPreview.berlaku_mulai).format("DD/MM/YYYY") }}
                s.d.
                {{ $date(undanganPreview.berlaku_sampai).format("DD/MM/YYYY") }}
                <br />
                Kode OTP dapat dipakai berulang selama masa berlaku.
                Jika form pembayaran belum selesai, isi OTP yang sama.
              </v-card-subtitle>
              <v-card-text>
                <v-alert
                  outlined
                  dense
                  type="error"
                  :value="form_error"
                >
                  {{ otpError || "Kode OTP tidak valid." }}
                </v-alert>
                <v-text-field
                  v-model="formotp.otp"
                  label="KODE OTP 6 DIGIT"
                  :rules="rule_otp"
                  outlined
                  dense
                  maxlength="6"
                />
              </v-card-text>
              <v-card-actions class="justify-center">
                <v-btn
                  color="primary"
                  @click="verifyOtp"
                  :loading="btnLoading"
                  :disabled="btnLoading"
                  block
                >
                  LANJUT
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-form>

          <v-form
            v-else-if="step === 'payment'"
            ref="frmkonfirmasi"
            v-model="form_valid"
            @keyup.native.enter="savePembayaran"
            lazy-validation
          >
            <v-card outlined class="mb-2">
              <v-card-text>
                <v-card flat>
                  <v-card-title>NAMA PESERTA DIDIK:</v-card-title>
                  <v-card-subtitle>{{ data_pd.name }}</v-card-subtitle>
                </v-card>
                <v-card flat>
                  <v-card-title>NOMOR KONTAK WA:</v-card-title>
                  <v-card-subtitle>{{ data_pd.nomor_hp }}</v-card-subtitle>
                </v-card>
                <v-card flat>
                  <v-card-title>EMAIL:</v-card-title>
                  <v-card-subtitle>{{ data_pd.email }}</v-card-subtitle>
                </v-card>
                <v-card flat>
                  <v-card-title>JENJANG STUDI:</v-card-title>
                  <v-card-subtitle>{{ data_pd.nama_jenjang }}</v-card-subtitle>
                </v-card>
                <v-card flat>
                  <v-card-title>TAHUN PENDAFTARAN:</v-card-title>
                  <v-card-subtitle>{{ data_pd.ta }}</v-card-subtitle>
                </v-card>
                <v-card flat>
                  <v-card-title>BIAYA + KODE TRANSFER:</v-card-title>
                  <v-card-subtitle>{{ totalTransfer | formatUang }}</v-card-subtitle>
                </v-card>
                <v-alert
                  outlined
                  dense
                  type="info"
                  :value="true"
                  class="mt-2 mb-0"
                >
                  Nominal ini tetap sama jika OTP diisi ulang. Silahkan transfer sesuai jumlah tersebut.
                </v-alert>
              </v-card-text>
            </v-card>
            <v-card>
              <v-card-text>
                <v-select
                  label="PEMBAYARAN MELALUI :"
                  v-model="formdata.id_channel"
                  :items="daftar_channel"
                  item-text="nama_channel"
                  item-value="id_channel"
                  :rules="rule_channel_pembayaran"
                  outlined
                />
                <v-text-field
                  v-model="formdata.total_bayar"
                  label="SEBESAR :"
                  :rules="rule_total_bayar"
                  outlined
                />
                <v-text-field
                  v-model="formdata.nomor_rekening_pengirim"
                  label="NOMOR REKENING PENGIRIM:"
                  :rules="rule_nomor_rekening"
                  outlined
                />
                <v-text-field
                  v-model="formdata.nama_rekening_pengirim"
                  label="NAMA PENGIRIM:"
                  :rules="rule_nama_pengirim"
                  outlined
                />
                <v-text-field
                  v-model="formdata.nama_bank_pengirim"
                  label="BANK PENGIRIM:"
                  :rules="rule_nama_bank"
                  outlined
                />
                <v-menu
                  ref="menuTanggalBayar"
                  v-model="menuTanggalBayar"
                  :close-on-content-click="false"
                  :return-value.sync="formdata.tanggal_bayar"
                  transition="scale-transition"
                  offset-y
                  max-width="290px"
                  min-width="290px"
                >
                  <template v-slot:activator="{ on }">
                    <v-text-field
                      v-model="formdata.tanggal_bayar"
                      label="TANGGAL BAYAR/TRANSFER"
                      readonly
                      outlined
                      v-on="on"
                      :rules="rule_tanggal_bayar"
                    />
                  </template>
                  <v-date-picker
                    v-model="formdata.tanggal_bayar"
                    no-title
                    scrollable
                  >
                    <v-spacer></v-spacer>
                    <v-btn text color="primary" @click="menuTanggalBayar = false">Cancel</v-btn>
                    <v-btn text color="primary" @click="$refs.menuTanggalBayar.save(formdata.tanggal_bayar)">OK</v-btn>
                  </v-date-picker>
                </v-menu>
                <v-textarea
                  v-model="formdata.desc"
                  label="CATATAN:"
                  outlined
                />
                <v-file-input
                  accept="image/jpeg,image/png"
                  label="BUKTI BAYAR (MAKS. 2MB)"
                  :rules="rule_bukti_bayar"
                  show-size
                  v-model="formdata.bukti_bayar"
                  @change="previewImage"
                />
                <v-img class="white--text align-end" :src="buktiBayar" />
              </v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                  color="blue darken-1"
                  text
                  @click.stop="savePembayaran"
                  :loading="btnLoading"
                  :disabled="!form_valid || btnLoading"
                >
                  SIMPAN
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-form>
        </v-col>
      </v-row>
    </v-container>
  </FrontLayout>
</template>
<script>
  import FrontLayout from "@/views/layouts/FrontLayout";
  export default {
    name: "UndanganPMB",
    created() {
      this.loadPreview();
    },
    data: () => ({
      btnLoading: false,
      step: "",
      pageError: "",
      form_error: false,
      otpError: "",
      undanganPreview: null,
      data_pd: null,
      form_valid: true,
      menuTanggalBayar: false,
      image_prev: null,
      formotp: {
        otp: "",
      },
      daftar_channel: [
        { id_channel: 1, nama_channel: "TELLER BANK" },
        { id_channel: 2, nama_channel: "TRANSFER BANK" },
        { id_channel: 3, nama_channel: "INTERNET BANKING" },
        { id_channel: 4, nama_channel: "MOBILE BANKING" },
      ],
      formdata: {
        id: "",
        id_channel: 2,
        total_bayar: 0,
        nomor_rekening_pengirim: "",
        nama_rekening_pengirim: "",
        nama_bank_pengirim: "",
        desc: "",
        tanggal_bayar: "",
        bukti_bayar: [],
      },
      rule_otp: [
        value => !!value || "Kode OTP mohon untuk diisi !!!",
        value => /^\d{6}$/.test(value) || "Kode OTP harus 6 digit angka",
      ],
      rule_channel_pembayaran: [
        value => !!value || "Mohon dipilih Channel Pembayaran mohon untuk dipilih !!!",
      ],
      rule_nama_pengirim: [
        value => !!value || "Mohon diisi nama pengirim !!!",
      ],
      rule_nomor_rekening: [
        value => !!value || "Mohon diisi nomor rekening pengirim !!!",
        value => /^[0-9]+$/.test(value) || "Nomor Rekening hanya boleh angka",
      ],
      rule_nama_bank: [
        value => !!value || "Mohon diisi nama bank !!!",
      ],
      rule_tanggal_bayar: [
        value => !!value || "Tanggal Bayar mohon untuk diisi !!!",
      ],
      rule_bukti_bayar: [
        value => !!value || "Mohon pilih foto !!!",
        value => !value || value.size < 2000000 || "File Bukti Bayar harus kurang dari 2MB.",
      ],
      rule_total_bayar: [
        value => !!value || "Dana yang  ditransfer mohon untuk untuk di isi !!!",
        value => /^[0-9]+$/.test(value) || "Dana yang  ditransfer hanya boleh angka",
      ],
    }),
    computed: {
      otpRoute() {
        return String(this.$route.params.otp || "").padStart(6, "0");
      },
      buktiBayar: {
        get() {
          if (this.image_prev == null) {
            return require("@/assets/no-image.png");
          }
          return this.image_prev;
        },
        set(val) {
          this.image_prev = val;
        },
      },
      totalTransfer() {
        if (!this.data_pd) {
          return 0;
        }
        if (this.data_pd.total_transfer) {
          return Number(this.data_pd.total_transfer);
        }
        return Number(this.data_pd.biaya || 0) + Number(this.data_pd.code || 0);
      },
    },
    methods: {
      async loadPreview() {
        this.pageError = "";
        await this.$ajax
          .get("/spsb/undangan/kode/" + this.otpRoute)
          .then(({ data }) => {
            this.undanganPreview = data.undangan;
            const status = data.undangan.status;
            if (status === "berlaku") {
              this.step = "otp";
            } else if (status === "menunggu_verifikasi") {
              this.step = "waiting";
            } else if (status === "sudah_diverifikasi") {
              this.step = "otp";
            } else if (status === "belum_mulai") {
              this.pageError = "Link undangan belum berlaku.";
            } else if (status === "kadaluarsa") {
              this.pageError = "Link undangan sudah kadaluarsa.";
            } else {
              this.step = "otp";
            }
          })
          .catch(({ response }) => {
            this.pageError =
              (response && response.data && response.data.message) ||
              "Link undangan tidak valid.";
          });
      },
      async verifyOtp() {
        if (!this.$refs.frmotp.validate()) {
          return;
        }
        this.btnLoading = true;
        this.form_error = false;
        this.otpError = "";
        await this.$ajax
          .post("/spsb/undangan/kode/" + this.otpRoute + "/verify", {
            otp: this.formotp.otp,
          })
          .then(({ data }) => {
            this.btnLoading = false;
            if (data.need_payment) {
              this.data_pd = data.user;
              this.formdata.id = data.user.id;
              this.formdata.total_bayar = data.user.total_transfer;
              this.step = "payment";
              return;
            }
            if (data.waiting) {
              this.step = "waiting";
              return;
            }
            if (data.access_token) {
              this.afterLoginSuccess(data);
              return;
            }
            this.form_error = true;
            this.otpError = data.message || "Kode OTP tidak valid.";
          })
          .catch(({ response }) => {
            this.btnLoading = false;
            this.form_error = true;
            this.otpError =
              (response && response.data && response.data.message) ||
              "Kode OTP tidak valid.";
          });
      },
      previewImage(e) {
        if (typeof e === "undefined") {
          this.image_prev = null;
        } else {
          let reader = new FileReader();
          reader.readAsDataURL(e);
          reader.onload = img => {
            this.image_prev = img.target.result;
          };
        }
      },
      savePembayaran() {
        if (!this.$refs.frmkonfirmasi.validate()) {
          return;
        }
        this.btnLoading = true;
        var data = new FormData();
        data.append("user_id", this.formdata.id);
        data.append("transaksi_id", this.data_pd.code);
        data.append("formulir_id", this.data_pd.formulir_id || "");
        data.append("ta", this.data_pd.ta || "");
        data.append("kode_jenjang", this.data_pd.kode_jenjang || "");
        data.append("id_channel", this.formdata.id_channel);
        data.append("total_bayar", this.formdata.total_bayar);
        data.append("nomor_rekening_pengirim", this.formdata.nomor_rekening_pengirim);
        data.append("nama_rekening_pengirim", this.formdata.nama_rekening_pengirim);
        data.append("nama_bank_pengirim", this.formdata.nama_bank_pengirim);
        data.append("desc", this.formdata.desc);
        data.append("tanggal_bayar", this.formdata.tanggal_bayar);
        data.append("bukti_bayar", this.formdata.bukti_bayar);

        this.$ajax
          .post("/spsb/psb/konfirmasipembayaran", data, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          })
          .then(({ data }) => {
            this.btnLoading = false;
            if (data.konfirmasi && data.konfirmasi.transaksi_id) {
              this.step = "waiting";
              return;
            }
            this.form_error = true;
            this.otpError =
              (data && data.message) ||
              "Bukti pembayaran belum tersimpan. Silahkan unggah ulang.";
            this.step = "payment";
          })
          .catch(() => {
            this.btnLoading = false;
          });
      },
      afterLoginSuccess(data) {
        this.$ajax
          .get("/auth/me", {
            headers: {
              Authorization: data.token_type + " " + data.access_token,
            },
          })
          .then(response => {
            this.$store.dispatch("auth/afterLoginSuccess", {
              token: data,
              user: response.data,
            });
            this.$router.push("/dashboard/" + data.access_token);
          });
      },
    },
    components: {
      FrontLayout,
    },
  };
</script>
