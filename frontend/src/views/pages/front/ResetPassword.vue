<template>
  <FrontLayout>
    <v-container class="fill-height" fluid>
      <v-row align="center" justify="center" no-gutters>
        <v-col xs="12" sm="6" md="4">
          <h1 class="text-center display-1 font-weight-black primary--text">
            PASSWORD BARU
          </h1>
          <v-alert
            v-if="loadingToken"
            outlined
            dense
            type="info"
            :value="true"
          >
            Memeriksa tautan reset...
          </v-alert>
          <v-alert
            v-if="formError"
            outlined
            dense
            type="error"
            :value="true"
          >
            {{ errorMessage }}
          </v-alert>
          <v-form
            v-if="tokenValid"
            ref="frmreset"
            @keyup.native.enter="simpan"
            lazy-validation
          >
            <v-card outlined>
              <v-card-text>
                <v-text-field
                  v-model="form.password"
                  label="Password baru"
                  type="password"
                  :rules="rulePassword"
                  outlined
                  dense
                />
                <v-text-field
                  v-model="form.password_confirmation"
                  label="Ulangi password baru"
                  type="password"
                  :rules="ruleConfirm"
                  outlined
                  dense
                />
              </v-card-text>
              <v-card-actions class="justify-center">
                <v-btn
                  color="primary"
                  :loading="btnLoading"
                  :disabled="btnLoading"
                  block
                  @click="simpan"
                >
                  Simpan Password
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-form>
          <div class="text-center mt-3">
            <v-btn
              v-if="!tokenValid && !loadingToken"
              text
              small
              color="primary"
              to="/lupa-password"
            >
              Minta tautan baru
            </v-btn>
            <v-btn v-else text small color="primary" to="/login">
              Kembali ke login
            </v-btn>
          </div>
        </v-col>
      </v-row>
    </v-container>
  </FrontLayout>
</template>
<script>
  import FrontLayout from "@/views/layouts/FrontLayout";
  export default {
    name: "ResetPassword",
    created() {
      this.periksaToken();
    },
    data() {
      return {
        btnLoading: false,
        loadingToken: true,
        tokenValid: false,
        formError: false,
        errorMessage: "",
        form: {
          password: "",
          password_confirmation: "",
        },
        rulePassword: [
          value => !!value || "Password baru mohon untuk diisi",
          value => (value && value.length >= 6) || "Password baru minimal 6 karakter",
        ],
        ruleConfirm: [
          value => !!value || "Ulangi password baru mohon untuk diisi",
          value => value === this.form.password || "Konfirmasi password tidak sama",
        ],
      };
    },
    methods: {
      periksaToken: async function() {
        this.loadingToken = true;
        this.formError = false;
        await this.$ajax
          .get("/auth/reset-password/" + this.$route.params.token)
          .then(() => {
            this.tokenValid = true;
            this.loadingToken = false;
          })
          .catch(error => {
            this.tokenValid = false;
            this.loadingToken = false;
            this.formError = true;
            this.errorMessage = this.pesanError(error);
          });
      },
      simpan: async function() {
        if (!this.$refs.frmreset.validate()) {
          return;
        }
        this.btnLoading = true;
        this.formError = false;
        await this.$ajax
          .post("/auth/reset-password", {
            token: this.$route.params.token,
            password: this.form.password,
            password_confirmation: this.form.password_confirmation,
          })
          .then(() => {
            this.btnLoading = false;
            this.$router.push({ path: "/login", query: { reset: "1" } });
          })
          .catch(error => {
            this.formError = true;
            this.errorMessage = this.pesanError(error);
            this.btnLoading = false;
          });
      },
      pesanError(error) {
        const data = error.response && error.response.data;
        if (!data) {
          return "Tautan reset tidak berlaku atau sudah kedaluwarsa.";
        }
        if (typeof data.message === "string" && data.message) {
          return data.message;
        }
        const errors = data.errors || data;
        const first = Object.values(errors).find(value => Array.isArray(value));
        if (first) {
          return first[0];
        }
        return "Terjadi kesalahan. Silakan coba lagi.";
      },
    },
    components: {
      FrontLayout,
    },
  };
</script>
