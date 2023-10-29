<template>
  <DataMasterLayout>
    <ModuleHeader>
      <template v-slot:icon>
        mdi-stairs-up
      </template>
      <template v-slot:name>
        JENJANG STUDI
      </template>
      <template v-slot:subtitle>
        
      </template>
      <template v-slot:breadcrumbs>
        <v-breadcrumbs :items="breadcrumbs" class="pa-0">
          <template v-slot:divider>
            <v-icon>mdi-chevron-right</v-icon>
          </template>
        </v-breadcrumbs>
      </template>
      <template v-slot:desc>
        <v-alert                                        
          color="cyan"
          border="left"           
          colored-border
          type="info"
        >
          Halaman ini berisi informasi jenjang studi. ID dan Nama Jenjang melekat ke sistem sehingga tidak bisa diubah.
        </v-alert>
      </template>
    </ModuleHeader>
    <v-container fluid>
      <v-row class="mb-4" no-gutters>
        <v-col cols="12">
          <v-data-table
            :headers="headers"
            :items="datatable"               
            item-key="kode_jenjang"
            sort-by="kode_jenjang"
            show-expand
            :disable-pagination="true"
            :hide-default-footer="true"
            :expanded.sync="expanded"
            :single-expand="true"
            @click:row="dataTableRowClicked"
            class="elevation-1"
            :loading="datatableLoading"
            loading-text="Loading... Please wait"> 
            <template v-slot:top>
              <v-dialog v-model="dialogfrm" max-width="500px" persistent>                                    
                <v-form ref="frmdata" v-model="form_valid" lazy-validation>
                  <v-card>
                    <v-card-title>
                      <span class="headline">SETTING JENJANG STUDI</span>
                    </v-card-title>
                    <v-card-subtitle>
                      <span class="info--text">
                        Konfigurasi terkait dengan jenjang studi
                      </span>
                    </v-card-subtitle>
                    <v-card-text>                    
                      <v-switch v-model="formdata.status_pendaftaran" label="Aktif"></v-switch>
                    </v-card-text>
                    <v-card-actions>
                      <v-spacer></v-spacer>
                      <v-btn color="blue darken-1" text @click.stop="closedialogfrm">BATAL</v-btn>
                      <v-btn 
                        color="blue darken-1" 
                        text 
                        @click.stop="save" 
                        :loading="btnLoading"
                        :disabled="!form_valid || btnLoading">
                          SIMPAN
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-form>
              </v-dialog>
            </template>    
            <template v-slot:expanded-item="{ headers, item }">
              <td :colspan="headers.length" class="text-center">
                <v-col cols="12">                                    
                  <strong>created_at:</strong>{{ $date(item.created_at).format('DD/MM/YYYY HH:mm') }}
                  <strong>updated_at:</strong>{{ $date(item.updated_at).format('DD/MM/YYYY HH:mm') }}
                </v-col>                                
              </td>
            </template>
            <template v-slot:item.actions="{ item }">
              <v-icon
                small
                class="mr-2"
                :loading="btnLoading"
                :disabled="btnLoading"
                @click.stop="editItem(item)"
              >
                mdi-pencil
              </v-icon>
            </template>
            <template v-slot:no-data>
              Data belum tersedia
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-container>
  </DataMasterLayout>
</template>
<script>
  import DataMasterLayout from "@/views/layouts/DataMasterLayout";
  import ModuleHeader from "@/components/ModuleHeader";
  export default {
    name: "JenjangStudi",
    created() {
      this.breadcrumbs = [
        {
          text: "HOME",
          disabled: false,
          href: "/dashboard/" + this.$store.getters["auth/AccessToken"]
        },
        {
          text: "DATA MASTER",
          disabled: false,
          href: "/dmaster"
        },
        {
          text: "JENJANG STUDI",
          disabled: true,
          href: "#"
        }
      ];
      this.initialize();
    },
    data: () => ({
      breadcrumbs: [],  

      btnLoading: false,
      datatableLoading: false,
      expanded: [],
      datatable: [],
      
      //dialog
      dialogfrm: false,

      headers: [                                            
        { text: "ID", value: "kode_jenjang", width:10, sortable: false },
        { text: "NAMA JENJANG", value: "nama_jenjang", sortable: false },
        { text: "AKSI", value: "actions", sortable: false, width: 100 },
      ],
      form_valid: true,
      formdata: {
        kode_jenjang: "",
        nama_jenjang: "",
        status_pendaftaran: false, 
      },  
      formdefault: {
        kode_jenjang: "",
        nama_jenjang: "",
        status_pendaftaran: false, 
      },

      editedIndex: -1,
    }),
    methods: {
      initialize: async function() {
        this.datatableLoading = true;
        await this.$ajax.get("/datamaster/jenjangstudi", 
        {
          headers: {
            Authorization: this.$store.getters["auth/Token"]
          }
        }).then(({ data }) => {            
          this.datatable = data.jenjang_studi;
          this.datatableLoading = false;
        });
      },
      dataTableRowClicked(item) {
        if (item === this.expanded[0]) {
          this.expanded = [];
        } else {
          this.expanded = [item];
        }
      },
      async editItem(item) {
        this.editedIndex = this.datatable.indexOf(item);
        this.formdata = Object.assign({}, item);
        this.dialogfrm = true;
      },
      save: async function() {
        if (this.$refs.frmdata.validate()) {
          this.btnLoading = true;
          if (this.editedIndex > -1) {
            await this.$ajax
              .post(
                "/datamaster/jenjangstudi/" + this.formdata.kode_jenjang,
                {
                  _method: "PUT",
                  status_pendaftaran: this.formdata.status_pendaftaran,
                },
                {
                  headers: {
                    Authorization: this.$store.getters["auth/Token"],
                  },
                }
              )
              .then(() => {
                this.initialize();
                this.closedialogfrm();
                this.btnLoading = false;
              })
              .catch(() => {
                this.btnLoading = false;
              });
          }
        }
      },
      closedialogfrm() {
        this.dialogfrm = false;
        setTimeout(() => {
          this.formdata = Object.assign({}, this.formdefault);
          this.editedIndex = -1;
          this.$refs.frmdata.reset();
        }, 300);
      },
    },
    components: {
      DataMasterLayout,
      ModuleHeader,
    },
  };
</script>
