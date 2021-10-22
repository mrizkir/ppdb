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
    name:'FilterMode7',
    created()
    {
        this.daftar_jenjang=this.$store.getters['uiadmin/getDaftarJenjang'];  
        this.kode_jenjang=this.$store.getters['uiadmin/getKodeJenjang'];                                    

        this.daftar_ta=this.$store.getters['uiadmin/getDaftarTA'];  
        this.tahun_pendaftaran=this.$store.getters['uiadmin/getTahunPendaftaran'];  
    },
    data:()=>({
        firstloading:true,
        daftar_jenjang:[],
        kode_jenjang:null,

        daftar_ta:[],
        tahun_pendaftaran:null
    }),
    methods:{
        setFirstTimeLoading (bool)
        {
            this.firstloading=bool;
        }
    },
    watch:{
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