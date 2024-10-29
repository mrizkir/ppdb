<template>
  <FrontLayout>
    <v-container class="fill-height" fluid v-if="bukaPPDB">
      <v-row align="center" justify="center" no-gutters>
        <v-col cols="12">
          <h1 class="text-center display-1 font-weight-black primary--text">
            PRA-PENDAFTARAN CALON PESERTA DIDIK
          </h1>
          <h3 class="text-center display-1 font-weight-black primary--text">
            JENJANG PENDIDIKAN MENENGAH ATAS
          </h3>
          <h4 class="text-center title font-weight-black primary--text">
            TAHUN PELAJARAN {{ tahunPendaftaran | formatTA }}
          </h4>
          <h6 class="text-center title font-weight-black primary--text">
            KHUSUS GURU DAN TENAGA KEPENDIDIKAN
          </h6>
        </v-col>
      </v-row>
      <v-row align="center" justify="center" no-gutters>
        <v-col xs="12" md="7" sm="12">
          <v-form ref="frmpendaftaran" v-model="form_valid" lazy-validation>
            <v-card outlined>
              <v-card-text>
                <v-text-field
                  v-model="formdata.name"
                  label="NAMA CALON PESERTA DIDIK"
                  :rules="rule_name"
                  outlined
                  dense
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
                      outlined
                      dense
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
                    <v-btn
                      text
                      color="primary"
                      @click="menuTanggalLahir = false"
                    >
                      Cancel
                    </v-btn>
                    <v-btn
                      text
                      color="primary"
                      @click="
                        $refs.menuTanggalLahir.save(formdata.tanggal_lahir)
                      "
                    >
                      OK
                    </v-btn>
                  </v-date-picker>
                </v-menu>
                <v-text-field
                  v-model="formdata.nomor_hp"
                  label="NOMOR KONTAK WA (ex: +628123456789)"
                  :rules="rule_nomorhp"
                  outlined
                  dense
                />
                <v-radio-group v-model="formdata.jk" row>
                  JENIS KELAMIN :
                  <v-radio label="LAKI-LAKI" value="L"></v-radio>
                  <v-radio label="PEREMPUAN" value="P"></v-radio>
                </v-radio-group>
                <v-text-field
                  v-model="formdata.email"
                  label="SURAT ELEKTRONIK"
                  :rules="rule_email"
                  outlined
                  dense
                />
                <v-text-field
                  v-model="formdata.username"
                  label="USERNAME"
                  :rules="rule_username"
                  outlined
                  dense
                />
                <v-text-field
                  v-model="formdata.password"
                  label="PASSWORD"
                  type="password"
                  :rules="rule_password"
                  outlined
                  dense
                />
                <span class="font-weight-medium">
                  Apakah Peserta Didik Penyandang Disabilitas (PDPD) ?
                </span>
                <v-switch
                  v-model="formdata.penyandang_disabilitas"
                  label="YA"
                />
                <v-alert
                  color="warning"
                  class="mb-1"
                  text
                  v-if="isPenyandangDisabilitas && formdata.jk == 'L'"
                >
                  Pada periode ini belum menerima Peserta Didik Penyandang Disabilitas (PDPD).
                </v-alert>
                <v-alert
                  color="warning"
                  class="mb-1"
                  text
                  v-if="isPenyandangDisabilitas && formdata.jk == 'P'"
                >
                  Silakan mendaftar secara manual kepada Admin di setiap jenjang terkait.
                </v-alert>
                <v-alert
                  color="error"
                  class="mb-0"
                  text
                  v-if="formdata.captcha_response.length <= 0"
                >
                  Mohon dicentang Google Captcha (ANTI SPAMMERS)
                </v-alert>
              </v-card-text>
              <v-card-actions class="justify-center">
                <vue-recaptcha
                  ref="recaptcha"
                  :sitekey="sitekey"
                  @verify="onVerify"
                  @expired="onExpired"
                  :loadRecaptchaScript="true"
                />
              </v-card-actions>
              <v-card-actions class="justify-center">
                <v-btn
                  color="primary"
                  @click="save"
                  :loading="btnLoading"
                  :disabled="btnLoading || isPenyandangDisabilitas"
                  block
                >
                  DAFTAR
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-form>
          <v-dialog v-model="dialogkonfirmasipendaftaran" max-width="500px" persistent>
            <v-form ref="frmkonfirmasi" v-model="form_valid" lazy-validation>
              <v-card>
                <v-card-title>
                  <span class="headline">Konfirmasi Pendaftaran</span>
                </v-card-title>
                <v-card-text>
                  <v-alert type="success">
                    Proses PRA-PENDAFTARAN berhasil, silahkan Ayah/Bunda/Wali melakukan pembayaran dengan mentransfer beserta Biaya Pendaftaran sebesar :
                  </v-alert>
                  <v-text-field
                    v-model="formkonfirmasi.code"
                    label="BIAYA PENDAFTARAN + KODE TRANSFER"
                    outlined
                    :disabled="true"
                  >
                  </v-text-field>
                  Transfer ke Rekening berikut :
                  <v-alert type="info">
                    BANK RIAU KEPRI SYARIAH <br />
                    NOMOR REKENING : 821-21-28255 <br />
                    A.N : PPDB SEKOLAH ISLAM DE GREEN CAMP<br />
                  </v-alert>
                  <strong>SETELAH MELAKUKAN TRANSFER, SILAHKAN UNGGAH BUKTI TRANSFER/BAYAR DI HALAMAN KONFIRMASI.</strong>
                </v-card-text>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn
                    color="blue darken-1"
                    text
                    @click.stop="closedialogfrm"
                  >
                    OK
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-form>
          </v-dialog>
        </v-col>
        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly" />
      </v-row>
    </v-container>
    <v-container fluid v-if="registerSMA == false">
      <v-row>
        PPDB SMA Belum dibuka
      </v-row>
    </v-container>
  </FrontLayout>
</template>
<script>
  import { mapGetters } from "vuex";
  import VueRecaptcha from "vue-recaptcha";
  import FrontLayout from "@/views/layouts/FrontLayout";
  export default {
    name: "PSBSMA-GTK",
    created() {
      this.initialize();
    },
    data: () => ({
      registerSMA: null,
      btnLoading: false,
      //form
      form_valid: true,
      dialogkonfirmasipendaftaran: false,
      menuTanggalLahir: false,
      formdata: {
        name: "",
        tanggal_lahir: "",
        jk: "L",
        email: "",
        nomor_hp: "",
        username: "",
        password: "",
        captcha_response: "",
        penyandang_disabilitas: false,
      },
      formdefault: {
        name: "",
        tanggal_lahir: "",
        jk: "L",
        email: "",
        nomor_hp: "",
        username: "",
        password: "",
        captcha_response: "",
        penyandang_disabilitas: false,
      },
      formkonfirmasi: {
        email: "",
        code: "",
      },
      rule_name: [
        value => !!value || "Nama Calon Peserta Didik mohon untuk diisi !!!",
        value =>
          /^[A-Za-z\s\\,\\.]*$/.test(value) ||
          "Nama Calon Peserta Didik hanya boleh string dan spasi",
      ],
      rule_tanggal_lahir: [
        value => !!value || "Tanggal Lahir mohon untuk dipilih !!!",
      ],
      rule_nomorhp: [
        value => !!value || "Nomor Kontak WA mohon untuk diisi !!!",
        value =>
          /^\+[1-9]{1}[0-9]{1,14}$/.test(value) ||
          "Nomor Kontak WA hanya boleh angka dan gunakan kode negara didepan seperti +6281214553388",
      ],
      rule_email: [
        value => !!value || "Email mohon untuk diisi !!!",
        v => /.+@.+\..+/.test(v) || "Format E-mail mohon di isi dengan benar",
      ],
      rule_jenjang: [
        value => !!value || "Program studi mohon untuk dipilih !!!",
      ],
      rule_username: [
        value =>
          !!value || "Username mohon untuk diisi dengan nama depan anak !!!",
      ],
      rule_password: [value => !!value || "Password mohon untuk diisi !!!"],
    }),
    methods: {
      initialize: async function() {
        await this.$ajax.get("/datamaster/jenjangstudi").then(({ data }) => {
          let jenjang_studi = data.jenjang_studi;
          jenjang_studi.forEach(element => {
            if (element.kode_jenjang == 4) {
              this.registerSMA = element.status_pendaftaran == 1;
            }
          });
        });
      },
      save: async function() {
        if (this.$refs.frmpendaftaran.validate()) {
          this.btnLoading = true;
          await this.$ajax
            .post("/spsb/psb/store", {
              name: this.formdata.name,
              tanggal_lahir: this.formdata.tanggal_lahir,
              jk: this.formdata.jk,
              email: this.formdata.email,
              nomor_hp: this.formdata.nomor_hp,
              username: this.formdata.username,
              kode_jenjang: 4,
              password: this.formdata.password,
              captcha_response: this.formdata.captcha_response,
              penyandang_disabilitas:
                this.formdata.penyandang_disabilitas == true ? 1 : 0,
            })
            .then(({ data }) => {
              this.formkonfirmasi.email = data.email;
              this.formkonfirmasi.code = data.code;
              this.btnLoading = false;
              this.dialogkonfirmasipendaftaran = true;
              this.form_valid = true;
              this.$refs.frmpendaftaran.reset();
              this.formdata = Object.assign({}, this.formdefault);
            })
            .catch(() => {
              this.btnLoading = false;
            });
        }
        this.resetRecaptcha();
      },
      onVerify: function(response) {
        this.formdata.captcha_response = response;
      },
      onExpired: function() {
        this.formdata.captcha_response = "";
      },
      resetRecaptcha() {
        this.$refs.recaptcha.reset();
        this.formdata.captcha_response = "";
      },
      closedialogfrm() {
        this.dialogkonfirmasipendaftaran = false;
        setTimeout(() => {
          this.frmpendaftaran = Object.assign({}, this.formdefault);
          this.$refs.frmpendaftaran.reset();
          this.$router.push("/konfirmasipembayaran");
        }, 300);
      },
    },
    computed: {
      ...mapGetters("uifront", {
        sitekey: "getCaptchaKey",
        tahunPendaftaran: "getTahunPendaftaran",
        bukaPPDB: "getBukaPPDB_GTK",
      }),
      isPenyandangDisabilitas() {
        return this.formdata.penyandang_disabilitas;
      },
    },
    components: {
      FrontLayout,
      VueRecaptcha,
    },
  };
</script>
