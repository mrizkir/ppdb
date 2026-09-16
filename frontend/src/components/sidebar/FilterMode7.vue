<template>
    <v-list-item>
        <v-list-item-content>
            <v-select
                v-model="kode_jenjang"
                :items="daftar_jenjang"
                item-text="text"
                item-value="id"
                label="JENJANG STUDI"
                outlined/>
            <v-select
                v-model="tahun_pendaftaran"
                :items="daftar_ta"
                label="TAHUN PENDAFTARAN"
                outlined/>
        </v-list-item-content>
    </v-list-item>	
</template>
<script>
export default {
    name: 'FilterMode7',
    created()
    {
        this.syncFromStore();
    },
    data:()=>({
        firstloading: true,
        kode_jenjang:null,
        tahun_pendaftaran:null
    }),
    methods: {
        setFirstTimeLoading (bool)
        {
            this.firstloading=bool;
        },
        syncFromStore()
        {
            this.kode_jenjang=this.$store.getters['uiadmin/getKodeJenjang'];
            this.tahun_pendaftaran=this.$store.getters['uiadmin/getTahunPendaftaran'];
        }
    },
    computed: {
        daftar_jenjang() {
            return this.$store.getters['uiadmin/getDaftarJenjang'];
        },
        daftar_ta() {
            return this.$store.getters['uiadmin/getDaftarTA'];
        },
    },
    watch: {
        '$store.state.uiadmin.loaded'(loaded)
        {
            if (loaded) {
                this.syncFromStore();
            }
        },
        tahun_pendaftaran(val)
        {
            if (!this.firstloading)
            {
                this.$store.dispatch('uiadmin/updateTahunPendaftaran',val);
                this.$emit('changeTahunPendaftaran',val);
            }
        },
        kode_jenjang(val)
        {
            if (!this.firstloading)
            {
                this.$store.dispatch('uiadmin/updateJenjang',val);
                this.$emit('changeJenjang',val);
            }
        },
    }
}
</script>
