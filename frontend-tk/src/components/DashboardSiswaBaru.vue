<template>
    <v-row no-gutters>
        <v-col cols="12">
            <v-timeline align-top :dense="$vuetify.breakpoint.smAndDown">                
                <v-timeline-item color="red lighten-2" icon="mdi-roman-numeral-1" fill-dot>
                    <v-card color="red lighten-2" dark>
                        <v-card-title class="title">DATA ANANDA</v-card-title>
                        <v-card-text class="white text--primary">
                            <p>
                                Isi data ananda secara akurat                            
                            </p>                            
                            <v-btn
                                color="purple darken-1"
                                class="mx-0 mr-2"
                                outlined
                                to="/spsb/formulirpendaftaran"
                            >
                                Isian Ananda
                            </v-btn>                            
                        </v-card-text>                    
                    </v-card>                
                </v-timeline-item>         
                <v-timeline-item color="indigo" icon="mdi-roman-numeral-2" fill-dot>
                    <v-card color="indigo" dark>
                        <v-card-title class="title">SITUASI KELUARGA</v-card-title>
                        <v-card-text class="white text--primary">
                            <p>
                                Isilah situasi keluarga.                                  
                            </p>                            
                            <v-btn
                                color="purple darken-1"
                                class="mx-0 mr-2"
                                outlined
                                to="/spsb/situasikeluarga"
                            >
                                Isian Situasi Keluarga
                            </v-btn>
                        </v-card-text>                    
                    </v-card>            
                </v-timeline-item>          
                <v-timeline-item color="purple darken-1" icon="mdi-roman-numeral-3" fill-dot>
                    <v-card color="purple darken-1" dark>
                        <v-card-title class="title">DATA AYAH</v-card-title>
                        <v-card-text class="white text--primary">
                            <p>
                                Isi biodata ayah/wali, apabila tidak ada boleh dilewati.                               
                            </p>                            
                            <v-btn
                                color="purple darken-1"
                                class="mx-0 mr-2"
                                outlined
                                to="/spsb/biodataayah"
                            >
                                Isian Biodata Ayah   
                            </v-btn>                           
                        </v-card-text>                    
                    </v-card>                
                </v-timeline-item>                
                <v-timeline-item color="green lighten-1" icon="mdi-roman-numeral-4" fill-dot>
                    <v-card color="green lighten-1" dark>
                        <v-card-title class="title">DATA IBU</v-card-title>
                        <v-card-text class="white text--primary">
                            <p>
                                Isilah biodata ibu/wali, apabila tidak ada boleh dilewati.                                 
                            </p>                            
                            <v-btn
                                color="purple darken-1"
                                class="mx-0 mr-2"
                                outlined
                                to="/spsb/biodataibu"
                            >
                                Isian Biodata Ibu
                            </v-btn>
                        </v-card-text>                    
                    </v-card>                                    
                </v-timeline-item>                   
                <v-timeline-item color="orange" icon="mdi-roman-numeral-5" fill-dot>
                    <v-card color="orange" dark>
                        <v-card-title class="title">PERSYARATAN</v-card-title>
                        <v-card-text class="white text--primary">
                            <p>
                                Unggah Persyaratan secara cermat
                            </p>                                                        
                            <v-btn
                                color="purple darken-1"
                                class="mx-0"
                                outlined
                                to="/spsb/persyaratantambah"
                            >
                                Unggah Persyaratan
                            </v-btn>
                        </v-card-text>                    
                    </v-card>     
                </v-timeline-item>
                <v-timeline-item color="teal lighten-1" icon="mdi-roman-numeral-6" fill-dot>
                    <v-card color="teal lighten-1" dark>
                        <v-card-title class="title">KUESIONER</v-card-title>
                        <v-card-text class="white text--primary">
                            <p>
                                Isilah kuesioner berikut (WAJIB) melalui Google Form.
                            </p>                                                        
                            <v-btn
                                color="purple darken-1"
                                class="mx-0"
                                outlined
                                href="https://docs.google.com/forms/d/1vu6e4gXeY4n9niGOQyAyzpZ0JsLxRZo6owFm8j2AYhQ/edit?usp=sharing"
                                target="_blank"
                            >
                                Isi Kuesioner
                            </v-btn>
                        </v-card-text>                    
                    </v-card>     
                </v-timeline-item>
            </v-timeline>
        </v-col>        
    </v-row>
</template>
<script>
export default {
    name: 'DashboardSiswaBaru',
    created()
    {
        this.initialize();                  
    },
    data:()=>({
        btnLoading:false, 
    }),
    methods: {
        initialize:async function ()
        {
            
        },        
    }
}
</script>