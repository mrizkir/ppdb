import Vue from "vue";
import VueRouter from "vue-router";
import store from "../store/index";
import NotFoundComponent from "../components/NotFoundComponent";

Vue.use(VueRouter);
const routes = [
  {
    path: "/",
    name: "FrontDashboard",
    meta: {
      title: "DASHBOARD",
    },
    component: () => import("../views/pages/front/Home.vue"),
  },
  {
    path: "/psbtk",
    name: "FrontPSBtk",
    meta: {
      title: "PENDAFTARAN CALON PESERTA DIDIK TK",
    },
    component: () => import("../views/pages/front/PSBTK.vue"),
  },
  {
    path: "/psbsd",
    name: "FrontPSBsd",
    meta: {
      title: "PENDAFTARAN CALON PESERTA DIDIK SD",
    },
    component: () => import("../views/pages/front/PSBSD.vue"),
  },
  {
    path: "/psbsmp",
    name: "FrontPSBsmp",
    meta: {
      title: "PENDAFTARAN CALON PESERTA DIDIK SMP",
    },
    component: () => import("../views/pages/front/PSBSMP.vue"),
  },
  {
    path: "/psbsma",
    name: "FrontPSBsma",
    meta: {
      title: "PENDAFTARAN CALON PESERTA DIDIK SMA",
    },
    component: () => import("../views/pages/front/PSBSMA.vue"),
  },
  {
    path: "/konfirmasipembayaran",
    name: "FrontKonfirmasiPembayaran",
    meta: {
      title: "KONFIRMASI PEMBAYARAN",
    },
    component: () => import("../views/pages/front/KonfirmasiPembayaran.vue"),
  },
  {
    path: "/videotutorial",
    name: "FrontVideoTutorial",
    meta: {
      title: "VIDEO TUTORIAL",
    },
    component: () => import("../views/pages/front/VideoTutorial.vue"),
  },
  {
    path: "/login",
    name: "FrontLogin",
    meta: {
      title: "LOGIN",
    },
    component: () => import("../views/pages/front/Login.vue"),
  },
  {
    path: "/dashboard/:token",
    name: "AdminDashboard",
    meta: {
      title: "DASHBOARD",
    },
    component: () => import("../views/pages/admin/Dashboard.vue"),
  },
  //dmaster
  {
    path: "/dmaster",
    name: "DMaster",
    meta: {
      title: "DATA MASTER",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/dmaster/DMaster.vue"),
  },
  {
    path: "/dmaster/jenjangstudi",
    name: "DMasterJenjangStudi",
    meta: {
      title: "DATA MASTER - JENJANG STUDI",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/dmaster/JenjangStudi.vue"),
  },
  {
    path: "/dmaster/ta",
    name: "DMasterTahunAjaran",
    meta: {
      title: "DATA MASTER - TAHUN AJARAN",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/dmaster/TahunAjaran.vue"),
  },
  {
    path: "/dmaster/kuotapendaftaran",
    name: "DMasterKuotaPendaftaran",
    meta: {
      title: "DATA MASTER - KUOTA PENDAFTARAN",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/dmaster/KuotaPendaftaran.vue"),
  },
  //spsb
  {
    path: "/spsb",
    name: "SPSB",
    meta: {
      title: "SPSB",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/spsb/SPSB.vue"),
  },
  {
    path: "/spsb/pendaftaranbaru",
    name: "SPSBPendaftaranBaru",
    meta: {
      title: "SPSB - PENDAFTARAN BARU",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/spsb/PendaftaranBaru.vue"),
  },
  {
    path: "/spsb/formulirpendaftaran",
    name: "SPSBFormulirPendaftaran",
    meta: {
      title: "SPSB - BIODATA ANANDA",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/spsb/FormulirPendaftaran.vue"),
  },
  {
    path: "/spsb/situasikeluarga",
    name: "SPSBFormSituasiKeluarga",
    meta: {
      title: "SPSB - SITUASI KELUARGA",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/spsb/SituasiKeluarga.vue"),
  },
  {
    path: "/spsb/biodataayah",
    name: "SPSBFormulirBiodataAyah",
    meta: {
      title: "SPSB - BIOADATA AYAH",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/spsb/BiodataAyah.vue"),
  },
  {
    path: "/spsb/biodataibu",
    name: "SPSBFormulirBiodataIbu",
    meta: {
      title: "SPSB - BIOADATA IBU",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/spsb/BiodataIbu.vue"),
  },
  {
    path: "/spsb/persyaratan",
    name: "SPSBPersyaratan",
    meta: {
      title: "SPSB - PERSYARATAN",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/spsb/PersyaratanPSB.vue"),
  },
  {
    path: "/spsb/kontakdarurat",
    name: "SPSBKontakDarurat",
    meta: {
      title: "SPSB - KONTAK DARURAT",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/spsb/KontakDarurat.vue"),
  },
  {
    path: "/spsb/persyaratantambah",
    name: "SPSBPersyaratanTambah",
    meta: {
      title: "SPSB - PERSYARATAN",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/spsb/PersyaratanPPDBTambah.vue"),
  },
  {
    path: "/spsb/laporanpeserta",
    name: "SPSBReportPeserta",
    meta: {
      title: "SPSB - LAPORAN CALON PESERTA DIDIK",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/spsb/ReportPeserta.vue"),
  },
  {
    path: "/spsb/laporanjenjang",
    name: "SPSBReportJenjang",
    meta: {
      title: "SPSB - LAPORAN JENJANG STUDI",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/spsb/ReportJenjang.vue"),
  },
  {
    path: "/spsb/laporankelulusan",
    name: "SPSBReportKelulusan",
    meta: {
      title: "SPSB - LAPORAN KELULUSAN",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/spsb/ReportKelulusan.vue"),
  },
  //keuangan
  {
    path: "/keuangan",
    name: "Keuangan",
    meta: {
      title: "KEUANGAN",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/keuangan/Keuangan.vue"),
  },
  {
    path: "/keuangan/biayakomponenperiode",
    name: "KeuanganBiayaKomponenPeriode",
    meta: {
      title: "KEUANGAN - BIAYA KOMPONEN PERIODE",
      requiresAuth: true,
    },
    component: () =>
      import("../views/pages/admin/keuangan/BiayaKomponenPeriode.vue"),
  },
  //system
  {
    path: "/system-setting",
    name: "SystemSetting",
    meta: {
      title: "SETTING - SISTEM",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/SystemSetting.vue"),
  },
  {
    path: "/system-setting/identitasdiri",
    name: "SettingIdentitasDiri",
    meta: {
      title: "SETTING - IDENTITAS DIRI",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/IdentitasDiri.vue"),
  },
  {
    path: "/system-setting/ppdb",
    name: "SettingPPDB",
    meta: {
      title: "SETTING - PPDB",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/SettingPPDB.vue"),
  },
  {
    path: "/system-setting/headerlaporan",
    name: "HeaderLaporan",
    meta: {
      title: "SETTING - HEADER LAPORAN",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/HeaderLaporan.vue"),
  },
  {
    path: "/system-setting/captcha",
    name: "SettingCaptcha",
    meta: {
      title: "SETTING - CAPTCHA",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/Captcha.vue"),
  },
  {
    path: "/system-setting/email",
    name: "SettingEmail",
    meta: {
      title: "SETTING - EMAIL",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/Email.vue"),
  },
  {
    path: "/system-users",
    name: "SystemUsers",
    meta: {
      title: "SYSTEM - USERS",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/SystemUsers.vue"),
  },
  {
    path: "/system-users/permissions",
    name: "UsersPermissions",
    meta: {
      title: "USERS - PERMISSIONS",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/Permissions.vue"),
  },
  {
    path: "/system-users/roles",
    name: "UsersRoles",
    meta: {
      title: "USERS - ROLES",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/Roles.vue"),
  },
  {
    path: "/system-users/superadmin",
    name: "UsersSuperadmin",
    meta: {
      title: "USERS - SUPER ADMIN",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/UsersSuperadmin.vue"),
  },
  {
    path: "/system-users/psb",
    name: "UsersPSB",
    meta: {
      title: "USERS - PSB",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/UsersPSB.vue"),
  },
  {
    path: "/system-users/jenjang",
    name: "UsersJenjang",
    meta: {
      title: "USERS - JENJANG STUDI",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/UsersJenjang.vue"),
  },
  {
    path: "/system-users/dosen",
    name: "UsersDosen",
    meta: {
      title: "USERS - DOSEN",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/UsersDosen.vue"),
  },
  {
    path: "/system-users/keuangan",
    name: "UsersKeuangan",
    meta: {
      title: "USERS - KEUANGAN",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/UsersKeuangan.vue"),
  },
  {
    path: "/system-users/profil",
    name: "UsersProfil",
    meta: {
      title: "USERS - PROFILE",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/UsersProfile.vue"),
  },
  {
    path: "/system-migration",
    name: "SystemMigration",
    meta: {
      title: "MIGRASI SISTEM",
      requiresAuth: true,
    },
    component: () => import("../views/pages/admin/system/SystemMigration.vue"),
  },
  {
    path: "/404",
    name: "NotFoundComponent",
    meta: {
      title: "PAGE NOT FOUND",
    },
    component: NotFoundComponent,
  },
  {
    path: "*",
    redirect: "/404",
  },
];

const router = new VueRouter({
  mode: "history",
  base: process.env.BASE_URL,
  routes,
});

router.beforeEach((to, from, next) => {
  document.title = to.meta.title;
  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (store.getters["auth/Authenticated"]) {
      next();
      return;
    }
    next("/login");
  } else {
    next();
  }
});
export default router;
