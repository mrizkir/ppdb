<template>
  <SPSBLayout>
    <ModuleHeader v-if="dashboard!='siswabaru'">

    </ModuleHeader>
    <v-container fluid v-if="dashboard== 'siswabaru'">
      <v-row align="center" justify="center" class="mb-4" no-gutters>
        <v-col xs="12" sm="12" md="7">
          <v-alert type="info">
            Lakukan unggah persyaratan secara bertahap, klik tombol Unggah setelah melampirkan file.
          </v-alert>
        </v-col>
        <v-col xs="12" sm="12" md="7">
          <v-form v-model="form_valid_foto_selfi" ref="frmuploadfotoselfi" lazy-validation>
            <v-card class="mb-3">
              <v-card-title>
                FOTO WEFIE / KELUARGA (<span class="red--text text--lighten-1">WAJIB</span>)
              </v-card-title>
              <v-card-text>
                Foto "Wefie" Keluarga yang terdiri dari (Kedua Orangtua/wali dan Calon Peserta Didik)
                <v-file-input 
                  accept="application/pdf,image/jpeg,image/png" 
                  label="(.pdf, .png, atau .jpg) MAX 2MB"
                  :rules="rule_filefotoselfi"
                  show-size
                  v-model="filefotoselfi">
                </v-file-input>
              </v-card-text>
              <v-card-actions> 
                <v-btn
                  color="green"
                  text
                  :href="this.$api.storageURL + '/' + peryaratanppdb.file_fotoselfi"
                  v-if="peryaratanppdb.file_fotoselfi">
                  LIHAT
                </v-btn>
                <v-spacer />
                <v-btn 
                  color="grey darken-1" 
                  text 
                  @click.stop="$router.push('/dashboard/' + $store.getters['auth/AccessToken'])">KEMBALI</v-btn>
                <v-btn
                  color="orange"
                  text
                  @click="uploadFotoSelfi"
                  :loading="btnLoadingFotoSelfi"
                  :disabled="!form_valid_foto_selfi||btnLoadingFotoSelfi">
                  UNGGAH
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-form>
          <v-form v-model="form_valid_ktp_ayah" ref="frmuploadktpayah" lazy-validation>
            <v-card class="mb-3">
              <v-card-title>
                SCAN KTP AYAH (<span class="red--text text--lighten-1">WAJIB</span>)
              </v-card-title>
              <v-card-text>
                Scan sisi depan KTP ayah.
                <v-file-input 
                  accept="application/pdf,image/jpeg,image/png" 
                  label="(.pdf, .png, atau .jpg) MAX 2MB"
                  :rules="rule_file_ktp_ayah"
                  show-size
                  v-model="filektpayah">
                </v-file-input>
              </v-card-text>
              <v-card-actions> 
                <v-btn
                  color="green"
                  text
                  :href="this.$api.storageURL + '/' + peryaratanppdb.file_ktp_ayah"
                  v-if="peryaratanppdb.file_ktp_ayah">
                  LIHAT
                </v-btn>
                <v-spacer />
                <v-btn 
                  color="grey darken-1" 
                  text 
                  @click.stop="$router.push('/dashboard/' + $store.getters['auth/AccessToken'])">KEMBALI</v-btn>
                <v-btn
                  color="orange"
                  text
                  @click="uploadKTPAyah"
                  :loading="btnLoadingKTPAyah"
                  :disabled="!form_valid_ktp_ayah||btnLoadingKTPAyah">
                  UNGGAH
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-form>
          <v-form v-model="form_valid_ktp_ibu" ref="frmuploadktpibu" lazy-validation>
            <v-card class="mb-3">
              <v-card-title>
                SCAN KTP IBU (<span class="red--text text--lighten-1">WAJIB</span>)
              </v-card-title>
              <v-card-text>
                Scan sisi depan KTP Ibu.
                <v-file-input 
                  accept="application/pdf,image/jpeg,image/png" 
                  label="(.pdf, .png, atau .jpg) MAX 2MB"
                  :rules="rule_file_ktp_ibu"
                  show-size
                  v-model="filektpibu">
                </v-file-input>
              </v-card-text>
              <v-card-actions> 
                <v-btn
                  color="green"
                  text
                  :href="this.$api.storageURL + '/' + peryaratanppdb.file_ktp_ibu"
                  v-if="peryaratanppdb.file_ktp_ibu">
                  LIHAT
                </v-btn>
                <v-spacer />
                <v-btn 
                  color="grey darken-1" 
                  text 
                  @click.stop="$router.push('/dashboard/' + $store.getters['auth/AccessToken'])">KEMBALI</v-btn>
                <v-btn
                  color="orange"
                  text
                  @click="uploadKTPIbu"
                  :loading="btnLoadingKTPIbu"
                  :disabled="!form_valid_ktp_ibu||btnLoadingKTPIbu">
                  UNGGAH
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-form>
          <v-form v-model="form_valid_kk" ref="frmuploadkk" lazy-validation>
            <v-card class="mb-3">
              <v-card-title>
                SCAN KARTU KELUARGA (<span class="red--text text--lighten-1">WAJIB</span>)
              </v-card-title>
              <v-card-text>
                Scan Kartu Keluarga.
                <v-file-input 
                  accept="application/pdf,image/jpeg,image/png" 
                  label="(.pdf, .png, atau .jpg) MAX 2MB"
                  :rules="rule_filekk"
                  show-size
                  v-model="filekk">
                </v-file-input>
              </v-card-text>
              <v-card-actions> 
                <v-btn
                  color="green"
                  text
                  :href="this.$api.storageURL + '/' + peryaratanppdb.file_kk"
                  v-if="peryaratanppdb.file_kk"
                >
                  LIHAT
                </v-btn>
                <v-spacer />
                <v-btn 
                  color="grey darken-1" 
                  text 
                  @click.stop="$router.push('/dashboard/' + $store.getters['auth/AccessToken'])">KEMBALI</v-btn>
                <v-btn
                  color="orange"
                  text
                  @click="uploadKK"
                  :loading="btnLoadingKK"
                  :disabled="!form_valid_kk||btnLoadingKK">
                  UNGGAH
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-form>
          <v-form v-model="form_valid_aktalahir" ref="frmuploadaktalahir" lazy-validation>
            <v-card class="mb-3">
              <v-card-title>
                SCAN AKTA KELAHIRAN (<span class="red--text text--lighten-1">WAJIB</span>)
              </v-card-title>
              <v-card-text>
                Scan Akta Kelahiran Calon Peserta Didik.
                <v-file-input 
                  accept="application/pdf,image/jpeg,image/png" 
                  label="(.pdf, .png, atau .jpg) MAX 2MB"
                  :rules="rule_fileaktalahir"
                  show-size
                  v-model="fileaktalahir"
                >
                </v-file-input>
              </v-card-text>
              <v-card-actions> 
                <v-btn
                  color="green"
                  text
                  :href="this.$api.storageURL + '/' + peryaratanppdb.file_aktalahir"
                  v-if="peryaratanppdb.file_aktalahir"
                >
                  LIHAT
                </v-btn>
                <v-spacer />
                <v-btn 
                  color="grey darken-1" 
                  text 
                  @click.stop="$router.push('/dashboard/' + $store.getters['auth/AccessToken'])"
                >
                  KEMBALI
                </v-btn>
                <v-btn
                  color="orange"
                  text
                  @click="uploadAktaLahir"
                  :loading="btnLoadingAktaLahir"
                  :disabled="!form_valid_aktalahir||btnLoadingAktaLahir">
                  UNGGAH
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-form>
          <v-form v-model="form_valid_screenshoot" ref="frmuploadscreenshoot" lazy-validation>
            <v-card class="mb-3">
              <v-card-title>
                TANGKAPAN LAYAR MEDIA SOSIAL (<span class="red--text text--lighten-1">WAJIB</span>)
              </v-card-title>
              <v-card-text>
                Tangkapan layar follow akun IG Sekolah Islam DGC
                <v-file-input 
                  accept="application/pdf,image/jpeg,image/png" 
                  label="(.pdf, .png, atau .jpg) MAX 2MB"
                  :rules="rule_screenshoot_medsos"
                  show-size
                  v-model="filescreenshoot"
                >
                </v-file-input>
              </v-card-text>
              <v-card-actions> 
                <v-btn
                  color="green"
                  text
                  :href="this.$api.storageURL + '/' + peryaratanppdb.file_screenshoot_medsos"
                  v-if="peryaratanppdb.file_screenshoot_medsos"
                >
                  LIHAT
                </v-btn>
                <v-spacer />
                <v-btn 
                  color="grey darken-1" 
                  text 
                  @click.stop="$router.push('/dashboard/' + $store.getters['auth/AccessToken'])">KEMBALI</v-btn>
                <v-btn
                  color="orange"
                  text
                  @click="uploadScreenShoot"
                  :loading="btnLoadingScreenShoot"
                  :disabled="!form_valid_screenshoot||btnLoadingScreenShoot">
                  UNGGAH
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-form>
          <v-form v-model="form_valid_sertifikat" ref="frmuploadfilesertifikat" lazy-validation>
            <v-card class="mb-3">
              <v-card-title>
                SERTIFIKAT PENGHARGAAN (JIKA ADA)
              </v-card-title>
              <v-card-text>                
                <v-file-input 
                  accept="application/pdf,image/jpeg,image/png" 
                  label="(.pdf, .png, atau .jpg) MAX 2MB"
                  show-size
                  v-model="filesertifikat"
                  :rules="rule_sertifikat"
                />
              </v-card-text>
              <v-card-actions> 
                <v-btn
                  color="green"
                  text
                  :href="this.$api.storageURL + '/' + peryaratanppdb.file_sertifikat"
                  v-if="peryaratanppdb.file_sertifikat">
                  LIHAT
                </v-btn>
                <v-spacer />
                <v-btn 
                  color="grey darken-1" 
                  text 
                  @click.stop="$router.push('/dashboard/' + $store.getters['auth/AccessToken'])">KEMBALI</v-btn>
                <v-btn
                  color="orange"
                  text
                  @click="uploadSertifikat"
                  :loading="btnLoadingSertifikat"
                  :disabled="btnLoadingSertifikat">
                  UNGGAH
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-form>
          <v-form v-model="form_valid_nisn" ref="frmuploadfilenisn" lazy-validation>
            <v-card class="mb-3">
              <v-card-title>
                KARTU NISN (JIKA ADA)
              </v-card-title>
              <v-card-text>                
                <v-file-input 
                  accept="application/pdf,image/jpeg,image/png" 
                  label="(.pdf, .png, atau .jpg) MAX 2MB"
                  show-size
                  v-model="filenisn"
                  :rules="rule_nisn"
                />
              </v-card-text>
              <v-card-actions> 
                <v-btn
                  color="green"
                  text
                  :href="this.$api.storageURL + '/' + peryaratanppdb.file_nisn"
                  v-if="peryaratanppdb.file_nisn">
                  LIHAT
                </v-btn>
                <v-spacer />
                <v-btn 
                  color="grey darken-1" 
                  text 
                  @click.stop="$router.push('/dashboard/' + $store.getters['auth/AccessToken'])">KEMBALI</v-btn>
                <v-btn
                  color="orange"
                  text
                  @click="uploadNISN"
                  :loading="btnLoadingNISN"
                  :disabled="btnLoadingNISN">
                  UNGGAH
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-form>
          <v-form v-model="form_valid_kia" ref="frmuploadfilekia" lazy-validation>
            <v-card class="mb-3">
              <v-card-title>
                KARTU KIA (JIKA ADA)
              </v-card-title>
              <v-card-text>                
                <v-file-input 
                  accept="application/pdf,image/jpeg,image/png" 
                  label="(.pdf, .png, atau .jpg) MAX 2MB"
                  show-size
                  v-model="filekia"
                  :rules="rule_kia"
                />
              </v-card-text>
              <v-card-actions> 
                <v-btn
                  color="green"
                  text
                  :href="this.$api.storageURL + '/' + peryaratanppdb.file_kia"
                  v-if="peryaratanppdb.file_kia">
                  LIHAT
                </v-btn>
                <v-spacer />
                <v-btn 
                  color="grey darken-1" 
                  text 
                  @click.stop="$router.push('/dashboard/' + $store.getters['auth/AccessToken'])">KEMBALI</v-btn>
                <v-btn
                  color="orange"
                  text
                  @click="uploadKIA"
                  :loading="btnLoadingKIA"
                  :disabled="btnLoadingKIA">
                  UNGGAH
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-form>
        </v-col>
      </v-row>
    </v-container>
  </SPSBLayout>
</template>
<script>
  import SPSBLayout from "@/views/layouts/SPSBLayout";
  import ModuleHeader from "@/components/ModuleHeader";

  export default {
    name: "PersyaratanPPDBTambah", 
    created() {
      this.dashboard = this.$store.getters["uiadmin/getDefaultDashboard"];
      if (this.dashboard == "siswabaru")
      {
        this.pesertadidik_id=this.$store.getters["auth/AttributeUser"]("id");
      }
      this.initialize()
    },
    data: () => ({ 
      dashboard: null,
      peryaratanppdb: {},
      //formdata
      form_valid_foto_selfi: true,
      form_valid_ktp_ayah: true,
      form_valid_ktp_ibu: true,
      form_valid_kk: true,
      form_valid_aktalahir: true,
      form_valid_screenshoot: true,
      form_valid_sertifikat: true,
      form_valid_nisn: true,
      form_valid_kia: true,
      pesertadidik_id: null,

      btnLoadingFotoSelfi: false,
      btnLoadingKTPAyah: false,
      btnLoadingKTPIbu: false,
      btnLoadingKK: false,
      btnLoadingAktaLahir: false,
      btnLoadingScreenShoot: false,
      btnLoadingSertifikat: false,
      btnLoadingNISN: false,
      btnLoadingKIA: false,

      filefotoselfi: null,
      filektpayah: null,
      filektpibu: null,
      filekk: null,
      fileaktalahir: null,
      filescreenshoot: null,
      filesertifikat: null,
      filenisn: null,
      filekia: null,
      
      rule_filefotoselfi: [
        value => !!value || "Mohon pilih file foto selfi !!!",
        value =>!value || value.size < 2000000 || "File foto selfi harus kurang dari 2MB."                
      ],
      rule_file_ktp_ayah: [
        value => !!value || "Mohon pilih file ktp !!!",
        value =>!value || value.size < 2000000 || "File ktp harus kurang dari 2MB."                
      ],
      rule_file_ktp_ibu: [
        value => !!value || "Mohon pilih file ktp !!!",
        value =>!value || value.size < 2000000 || "File ktp harus kurang dari 2MB."                
      ],
      rule_filekk: [
        value => !!value || "Mohon pilih file Kartu Keluarga !!!",
        value =>!value || value.size < 2000000 || "File kartu keluarga harus kurang dari 2MB."                
      ],
      rule_fileaktalahir: [
        value => !!value || "Mohon pilih file Akta Lahir !!!",
        value =>!value || value.size < 2000000 || "File akta lahir harus kurang dari 2MB."                
      ],
      rule_screenshoot_medsos: [
        value => !!value || "Mohon screen shoot akun IG DGC!!!",
        value =>!value || value.size < 2000000 || "File hasil screen shoot akun IG mesti kurang dari 2MB."                
      ],
      rule_sertifikat: [
        value => !!value || "Mohon file sertifikat untuk diisi!!!",
        value =>!value || value.size < 2000000 || "File sertifikat mesti kurang dari 2MB."                
      ],
      rule_nisn: [
        value => !!value || "Mohon file nisn untuk diisi!!!",
        value =>!value || value.size < 2000000 || "File nisn mesti kurang dari 2MB."                
      ],
      rule_kia: [
        value => !!value || "Mohon file kia untuk diisi!!!",
        value =>!value || value.size < 2000000 || "File kia mesti kurang dari 2MB."                
      ],
    }),
    methods: {
      initialize: async function() {	
        await this.$ajax.get("/spsb/formulirpendaftaran/persyaratanppdb/" + this.$store.getters["auth/AttributeUser"]("id"),
          {
            headers: {
              Authorization: this.$store.getters["auth/Token"]
            }
          },
          
        )
        .then(({ data }) => {
          this.peryaratanppdb = data.formulir;
          this.$refs.frmuploadfotoselfi.resetValidation();
        });
      },
      async uploadFotoSelfi() {
        if (this.$refs.frmuploadfotoselfi.validate()) {
          if (typeof this.filefotoselfi !== "undefined" && this.filefotoselfi !== null ) {
            this.btnLoadingFotoSelfi = true;
            var formdata = new FormData();
            formdata.append("filefotoselfi", this.filefotoselfi);
            await this.$ajax.post("/spsb/formulirpendaftaran/uploadfileselfi/" + this.pesertadidik_id,formdata,
              {
                headers: {
                  Authorization: this.$store.getters["auth/Token"],
                  "Content-Type": "multipart/form-data"
                }
              }
            ).then(() => {
              this.btnLoadingFotoSelfi = false;
              this.$router.go();
            }).catch(() => {
              this.btnLoadingFotoSelfi = false;
            });
          }
        }
      }, 
      async uploadKTPAyah ()
      {
        if (this.$refs.frmuploadktpayah.validate())
        {
          if (typeof this.filektpayah !== "undefined" && this.filektpayah !== null )
          {
            this.btnLoadingKTPAyah = true;
            var formdata = new FormData();
            formdata.append("filektpayah", this.filektpayah);
            await this.$ajax.post("/spsb/formulirpendaftaran/uploadfilektpayah/" + this.pesertadidik_id,formdata,
              {
                headers: {
                  Authorization: this.$store.getters["auth/Token"],
                  "Content-Type": "multipart/form-data"
                }
              }
            ).then(() => {
              this.btnLoadingKTPAyah = false;
              this.$router.go();
            }).catch(() => {
              this.btnLoadingKTPAyah = false;
            });
          }
        }
      }, 
      async uploadKTPIbu ()
      {
        if (this.$refs.frmuploadktpibu.validate())
        {
          if (typeof this.filektpibu !== "undefined" && this.filektpibu !== null )
          {
            this.btnLoadingKTPIbu = true;
            var formdata = new FormData();
            formdata.append("filektpibu", this.filektpibu);
            await this.$ajax.post("/spsb/formulirpendaftaran/uploadfilektpibu/" + this.pesertadidik_id,formdata,
              {
                headers: {
                  Authorization: this.$store.getters["auth/Token"],
                  "Content-Type": "multipart/form-data"
                }
              }
            ).then(() => {
              this.btnLoadingKTPIbu = false;
              this.$router.go();
            }).catch(() => {
              this.btnLoadingKTPIbu = false;
            });
          }
        }
      }, 
      async uploadKK ()
      {
        if (this.$refs.frmuploadkk.validate())
        {
          if (typeof this.filekk !== "undefined" && this.filekk !== null )
          {
            this.btnLoadingKK = true;
            var formdata = new FormData();
            formdata.append("filekk", this.filekk);
            await this.$ajax.post("/spsb/formulirpendaftaran/uploadfilekk/" + this.pesertadidik_id,formdata,
              {
                headers: {
                  Authorization: this.$store.getters["auth/Token"],
                  "Content-Type": "multipart/form-data"
                }
              }
            ).then(() => {
              this.btnLoadingKK = false;
              this.$router.go();
            }).catch(() => {
              this.btnLoadingKK = false;
            });
          }
        }
      }, 
      async uploadAktaLahir() {
        if (this.$refs.frmuploadaktalahir.validate()) {
          if (typeof this.fileaktalahir !== "undefined" && this.fileaktalahir !== null ) {
            this.btnLoadingAktaLahir = true;
            var formdata = new FormData();
            formdata.append("fileaktalahir", this.fileaktalahir);
            await this.$ajax.post("/spsb/formulirpendaftaran/uploadfileaktalahir/" + this.pesertadidik_id,formdata,
              {
                headers: {
                  Authorization: this.$store.getters["auth/Token"],
                  "Content-Type": "multipart/form-data"
                }
              }
            )
            .then(() => {
              this.btnLoadingAktaLahir = false;
              this.$router.go();
            })
            .catch(() => {
              this.btnLoadingAktaLahir = false;
            });
          }
        }
      }, 
      async uploadScreenShoot() {
        if (this.$refs.frmuploadscreenshoot.validate()) {
          if (typeof this.filescreenshoot !== "undefined" && this.filescreenshoot !== null ) {
            this.btnLoadingScreenShoot = true;
            var formdata = new FormData();
            formdata.append("filescreenshoot", this.filescreenshoot);
            await this.$ajax.post("/spsb/formulirpendaftaran/uploadfilescreenshoot/" + this.pesertadidik_id, formdata,
              {
                headers: {
                  Authorization: this.$store.getters["auth/Token"],
                  "Content-Type": "multipart/form-data"
                }
              }
            )
            .then(() => {
              this.btnLoadingScreenShoot = false;
              this.$router.go();
            })
            .catch(() => {
              this.btnLoadingScreenShoot = false;
            });
          }
        }
      },
      async uploadSertifikat() {
        if (this.$refs.frmuploadfilesertifikat.validate()) {
          if (typeof this.filesertifikat !== "undefined" && this.filesertifikat !== null ) {
            this.btnLoadingSertifikat = true;
            var formdata = new FormData();
            formdata.append("filesertifikat", this.filesertifikat);
            await this.$ajax.post("/spsb/formulirpendaftaran/uploadfilesertifikat/" + this.pesertadidik_id, formdata,
              {
                headers: {
                  Authorization: this.$store.getters["auth/Token"],
                  "Content-Type": "multipart/form-data",
                },
              }
            )
            .then(() => {
              this.btnLoadingSertifikat = false;
              this.$router.go();
            })
            .catch(() => {
              this.btnLoadingSertifikat = false;
            });
          }
        }
      },
      async uploadNISN() {
        if (this.$refs.frmuploadfilenisn.validate()) {
          if (typeof this.filenisn !== "undefined" && this.filenisn !== null ) {
            this.btnLoadingNISN = true;
            var formdata = new FormData();
            formdata.append("filenisn", this.filenisn);
            await this.$ajax.post("/spsb/formulirpendaftaran/uploadfilenisn/" + this.pesertadidik_id, formdata,
              {
                headers: {
                  Authorization: this.$store.getters["auth/Token"],
                  "Content-Type": "multipart/form-data",
                },
              }
            )
            .then(() => {
              this.btnLoadingNISN = false;
              this.$router.go();
            })
            .catch(() => {
              this.btnLoadingNISN = false;
            });
          }
        }
      },
    },
    components: {
      SPSBLayout,
      ModuleHeader,
    },
  };
</script>