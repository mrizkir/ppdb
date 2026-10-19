<template>
  <FrontLayout>
    <v-container class="fill-height" fluid>
      <v-row align="center" justify="center" no-gutters>
        <v-col xs="12" sm="6" md="4">
          <h1 class="text-center display-1 font-weight-black primary--text">
            LUPA PASSWORD
          </h1>
          <v-alert
            v-if="formSuccess"
            outlined
            dense
            type="success"
            :value="true"
          >
            {{ successMessage }}
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
            v-if="!formSuccess"
            ref="frmlupa"
            @keyup.native.enter="kirim"
            lazy-validation
          >
            <v-card outlined>
              <v-card-text>
                <p class="mb-4">
                  Masukkan email yang terdaftar. Tautan reset berlaku 60 menit.
                </p>
                <v-text-field
                  v-model="form.email"
                  label="Email"
                  :rules="ruleEmail"
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
                  @click="kirim"
                >
                  Kirim Tautan Reset
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-form>
          <div class="text-center mt-3">
            <v-btn text small color="primary" to="/login">
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
    name: "LupaPassword",
    data: () => ({
      btnLoading: false,
      formSuccess: false,
      formError: false,
      successMessage: "",
      errorMessage: "",
      form: {
        email: "",
      },
      ruleEmail: [
        value => !!value || "Email mohon untuk diisi",
        value => /.+@.+\..+/.test(value) || "Format email terdapat kesalahan",
      ],
    }),
    methods: {
      kirim: async function() {
        if (!this.$refs.frmlupa.validate()) {
          return;
        }
        this.btnLoading = true;
        this.formError = false;
        await this.$ajax
          .post("/auth/forgot-password", {
            email: this.form.email,
          })
          .then(({ data }) => {
            this.formSuccess = true;
            this.successMessage = data.message;
            this.btnLoading = false;
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
          return "Terjadi kesalahan. Silakan coba lagi.";
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
