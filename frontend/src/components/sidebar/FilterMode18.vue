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
                v-model="tahun_ajaran"
                :items="daftar_ta"       
                label="TAHUN AJARAN"
                outlined/>            
        </v-list-item-content>
    </v-list-item>	
</template>
<script>
export default {
    name: 'FilterMode18',
    created()
    {
        this.daftar_jenjang=this.$store.getters['uiadmin/getDaftarJenjang'];
        this.kode_jenjang=this.$store.getters['uiadmin/getKodeJenjang']; 

        this.daftar_ta=this.$store.getters['uiadmin/getDaftarTA'];
        this.tahun_ajaran=this.$store.getters['uiadmin/getTahunAkademik'];
    },
    data:()=>({
        firstloading: true,
        daftar_jenjang: [],
        kode_jenjang:null,

        daftar_ta: [],
        tahun_ajaran:null
    }),
    methods:{
        setFirstTimeLoading (bool)
        {
            this.firstloading=bool;
        }
    },
    watch:{
        tahun_ajaran(val)
        {
            if (!this.firstloading)
            {
                this.$store.dispatch('uiadmin/updateTahunAkademik',val);
                this.$emit('changeTahunAkademik',val);
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