<template>
    <SystemConfigLayout>
		<ModuleHeader>
            <template v-slot:icon>
                mdi-face-profile
            </template>
            <template v-slot:name>
                IDENTITAS DIRI
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
                    Mengatur halaman informasi dan bentuk sekolah. Perubahan berlaku pada Login selanjutnya.
                    </v-alert>
            </template>
        </ModuleHeader> 
        <v-container fluid>
            <v-row class="mb-4" no-gutters>
                <v-col cols="12">
                    <v-form ref="frmdata" v-model="form_valid" lazy-validation>
                        <v-card>
                            <v-card-title>
                                SEKOLAH
                            </v-card-title>
                            <v-card-text>
                                <v-text-field 
                                    v-model="formdata.nama_sekolah" 
                                    label="NAMA SEKOLAH"
                                    outlined
                                    :rules="rule_nama_sekolah">
                                </v-text-field>
                                <v-text-field 
                                    v-model="formdata.nama_alias_pt" 
                                    label="NAMA SINGKATAN SEKOLAH"
                                    outlined
                                    :rules="rule_nama_singkatan_pt">
                                </v-text-field>
                                <v-text-field 
                                    v-model="formdata.kode_sekolah" 
                                    label="KODE SEKOLAH (SESUAI DAPODIK)"
                                    outlined
                                    :rules="rule_kode_sekolah">
                                </v-text-field>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn 
                                    color="blue darken-1" 
                                    text 
                                    @click.stop="save" 
                                    :loading="btnLoading"
                                    :disabled="!form_valid || btnLoading">SIMPAN</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-form>
                </v-col>
            </v-row>
        </v-container>
    </SystemConfigLayout>
</template>
<script>
import {mapGetters} from "vuex";
import SystemConfigLayout from '@/views/layouts/SystemConfigLayout';
import ModuleHeader from '@/components/ModuleHeader';
export default {
    name: 'IdentitasDiri',
    created() {
        this.breadcrumbs = [
            {
                text: 'HOME',
                disabled: false,
                href: '/dashboard/' + this.ACCESS_TOKEN
            },
            {
                text: 'KONFIGURASI SISTEM',
                disabled: false,
                href: '/system-setting'
            },
            {
                text: 'SEKOLAH',
                disabled: false,
                href: '#'
            },
            {
                text: 'IDENTITAS DIRI',
                disabled: true,
                href: '#'
            }
        ];
        this.initialize();
    },
    data: () => ({
        breadcrumbs: [],
        datatableLoading: false,
        btnLoading: false,
        //form
        form_valid: true,
        formdata: {
            nama_sekolah: "",
            nama_alias_pt: "",
            bentuk_sekolah: "",
            kode_sekolah: 0,
        },
        //form rules        
        rule_nama_sekolah: [
            value => !!value || "Mohon untuk di isi Nama Sekolah !!!",
        ],
        rule_nama_singkatan_pt: [
            value => !!value || "Mohon untuk di isi Nama Alias Sekolah !!!",
        ],
        rule_kode_sekolah: [
            value => !!value || "Mohon untuk di isi Kode Sekolah !!!",
            value => /^[0-9]+$/.test(value) || 'Kode Sekolah hanya boleh angka',
        ]
    }),
    methods: {
        initialize: async function() 
        {
            this.datatableLoading = true;
            await this.$ajax.get('/system/setting/variables',
            {
                headers: {
                    Authorization: this.TOKEN
                }
            }).then(({ data }) => {
                let setting = data.setting;
                this.formdata.nama_sekolah=setting.NAMA_SEKOLAH;
                this.formdata.nama_alias_pt=setting.NAMA_SEKOLAH_ALIAS;
                this.formdata.bentuk_sekolah=setting.BENTUK_SEKOLAH;
                this.formdata.kode_sekolah=setting.KODE_SEKOLAH;
            });
            
        },
        save () {
            if (this.$refs.frmdata.validate())
            {
                this.btnLoading = true;
                this.$ajax.post('/system/setting/variables',
                    {
                        '_method': 'PUT', 
                        'pid': 'Identitas Sekolah',
                        setting:JSON.stringify({
                            101: this.formdata.nama_sekolah,
                            102: this.formdata.nama_alias_pt, 
                            104: this.formdata.kode_sekolah,
                        }),                                                              
                    },
                    {
                        headers: {
                            Authorization: this.TOKEN
                        }
                    }
                ).then(() => {
                    this.btnLoading = false;
                }).catch(() => {
                    this.btnLoading = false;
                });
            }
        }
    },
    computed: { 
        ...mapGetters('auth',{
            ACCESS_TOKEN: 'AccessToken',
            TOKEN: 'Token',
        }),
    },
    components: {
		SystemConfigLayout,
        ModuleHeader,
	}
}
</script>