<template>
    <div>
        <v-app-bar app>
            <v-toolbar-title>
                <span class="hidden-sm-and-down">
                    &nbsp;
                </span>
            </v-toolbar-title>
            <v-spacer />
            <v-toolbar-items class="hidden-sm-and-down">
                <v-btn to="/" class="mr-2" color="primary" text large>
                    BERANDA
                </v-btn>
                <v-btn to="/videotutorial" class="mr-2" color="primary" text large>
                    VIDEO TUTORIAL
                </v-btn>
                <v-btn to="/psb" class="mr-2" color="primary" text large v-if="bukaPPDB">
                    PRA-PENDAFTARAN
                </v-btn>
                <v-btn to="/konfirmasipembayaran" class="mr-2" color="primary" text large v-if="bukaPPDB">
                    KONFIRMASI PEMBAYARAN
                </v-btn>
                <v-btn to="/login" color="primary" text large>
                    LOGIN FORMULIR
                </v-btn>
            </v-toolbar-items>
            <v-menu class="hidden-md-and-up" v-if="$vuetify.breakpoint.xsOnly">
                <template v-slot:activator="{ on }">
                    <v-btn icon v-on="on">
                        <v-icon>mdi-dots-vertical</v-icon>
                    </v-btn>
                </template>
                <v-list>
                    <v-list-item to="/">
                        <v-list-item-title>BERANDA</v-list-item-title>
                    </v-list-item>
                    <v-list-item to="/videotutorial">
                        <v-list-item-title>VIDEO TUTORIAL</v-list-item-title>
                    </v-list-item>
                    <v-list-item to="/psb">
                        <v-list-item-title>PRA-PENDAFTARAN</v-list-item-title>
                    </v-list-item>
                    <v-list-item to="/konfirmasipembayaran">
                        <v-list-item-title>KONFIRMASI PEMBAYARAN</v-list-item-title>
                    </v-list-item>
                    <v-list-item to="/login">
                        <v-list-item-title>LOGIN FORMULIR</v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>
        </v-app-bar>
        <v-main>
            <slot/>
        </v-main>
    </div>
</template>
<script>
import { mapGetters } from 'vuex';

export default {
    name:'FrontLayout',
    created ()
    {
        this.$store.dispatch('uifront/init',this.$ajax);
    },
    computed :{
        ...mapGetters('uifront',{
            namaSekolahAlias: 'getNamaSekolahAlias',
            bukaPPDB: 'getBukaPPDB',
        })
    }
}
</script>
