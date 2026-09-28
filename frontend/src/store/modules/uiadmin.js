//state
const getDefaultState = () => {
  return {
    loaded: false,
    //page
    default_dashboard: null,
    pages: [],

    daftar_ta: [],
    tahun_pendaftaran: null,
    tahun_ajaran: null,

    daftar_semester: [],
    semester_pendaftaran: null,
    semester_akademik: null,

    daftar_jenjang: [],
    kode_jenjang: null,

    filter_halaman: {},

    theme: null,
  };
};
const state = getDefaultState();

//mutations
const mutations = {
  setNewPage(state, page) {
    state.pages.push(page);
  },
  replacePage(state, page, index) {
    state.pages[index] = page;
  },
  removePage(state, name) {
    var i;
    for (i = 0; i < state.pages.length; i++) {
      if (state.pages[i].name == name) {
        state.pages.splice(i, 1);
        break;
      }
    }
  },
  setLoaded(state, loaded) {
    state.loaded = loaded;
  },
  setDashboard(state, name) {
    state.default_dashboard = name;
  },

  setDaftarTA(state, daftar) {
    state.daftar_ta = daftar;
  },
  setTahunPendaftaran(state, tahun) {
    state.tahun_pendaftaran = tahun;
  },
  setTahunAjaran(state, tahun) {
    state.tahun_ajaran = tahun;
  },

  setDaftarSemester(state, daftar) {
    state.daftar_semester = daftar;
  },
  setSemesterPendaftaran(state, semester) {
    state.semester_pendaftaran = semester;
  },
  setSemester(state, semester) {
    state.semester_akademik = semester;
  },

  setDaftarJenjang(state, daftar) {
    state.daftar_jenjang = daftar;
  },
  setKodeJenjang(state, id) {
    state.kode_jenjang = id;
  },
  setFilterHalaman(state, { name, tahun_pendaftaran, kode_jenjang }) {
    state.filter_halaman = {
      ...(state.filter_halaman || {}),
      [name]: {
        tahun_pendaftaran,
        kode_jenjang,
      },
    };
  },

  setTheme(state, theme) {
    state.theme = theme;
  },

  resetState(state) {
    Object.assign(state, getDefaultState());
  },
};
const getters = {
  Page: state => name => {
    let page = state.pages.find(halaman => halaman.name == name);
    return page;
  },
  AtributeValueOfPage: state => (name, key) => {
    let page = state.pages.find(halaman => halaman.name == name);
    return page[key];
  },

  getDefaultDashboard: state => {
    return state.default_dashboard;
  },

  getDaftarTA: state => {
    return state.daftar_ta;
  },
  getDaftarTABefore: state => ta => {
    let daftar_ta = state.daftar_ta;
    var daftar = [];
    daftar_ta.forEach(element => {
      if (element.value <= ta) {
        daftar.push(element);
      }
    });
    return daftar;
  },
  getTahunPendaftaran: state => {
    return parseInt(state.tahun_pendaftaran);
  },
  getTahunAjaran: state => {
    return parseInt(state.tahun_ajaran);
  },

  getDaftarSemester: state => {
    return state.daftar_semester;
  },
  getNamaSemester: state => key => {
    var nama_semester = "";
    let found = state.daftar_semester.find(semester => semester.id == key);
    if (typeof found !== "undefined") {
      nama_semester = found.text;
    }
    return nama_semester;
  },
  getSemesterPendaftaran: state => {
    return parseInt(state.semester_pendaftaran);
  },
  getSemester: state => {
    return parseInt(state.semester_akademik);
  },

  getDaftarJenjang: state => {
    return state.daftar_jenjang.filter(el => el != null);
  },
  getKodeJenjang: state => {
    return parseInt(state.kode_jenjang);
  },
  getFilterHalaman: state => name => {
    const filter = state.filter_halaman && state.filter_halaman[name];
    if (!filter || filter.tahun_pendaftaran == null || filter.kode_jenjang == null) {
      return null;
    }
    return {
      tahun_pendaftaran: parseInt(filter.tahun_pendaftaran),
      kode_jenjang: parseInt(filter.kode_jenjang),
    };
  },
  getNamaJenjang: state => key => {
    var jenjang = state.daftar_jenjang.find(record => record.id == key);
    return jenjang.text;
  },

  getTheme: state => key => {
    return state.theme == null ? "" : state.theme[key];
  },
};
const actions = {
  init: async function({ commit, state, rootGetters }, ajax) {
    if (!state.loaded && rootGetters["auth/Authenticated"]) {
      commit(
        "setSemesterPendaftaran",
        rootGetters["uifront/getSemesterPendaftaran"]
      );
      let token = rootGetters["auth/Token"];
      await ajax
        .get("/system/setting/uiadmin", {
          headers: {
            Authorization: token,
          },
        })
        .then(({ data }) => {
          commit("setDaftarTA", data.daftar_ta);
          commit("setTahunPendaftaran", data.tahun_pendaftaran);
          commit("setTahunAjaran", data.tahun_ajaran);
          commit("setDaftarSemester", data.daftar_semester);
          commit("setSemester", data.semester_akademik);

          commit("setDaftarJenjang", data.daftar_jenjang);
          commit("setKodeJenjang", data.kode_jenjang);

          commit("setTheme", data.theme);

          commit("setLoaded", true);
        });
    }
  },

  addToPages({ commit, state }, page) {
    let found = state.pages.find(halaman => halaman.name == page.name);
    if (!found) {
      commit("setNewPage", page);
    }
  },
  updatePage({ commit, state }, page) {
    var i;
    for (i = 0; i < state.pages.length; i++) {
      if (state.pages[i].name == page.name) {
        break;
      }
    }
    commit("replacePage", page, i);
  },
  deletePage({ commit }, name) {
    commit("removePage", name);
  },

  changeDashboard({ commit }, name) {
    commit("setDashboard", name);
  },

  updateJenjang({ commit }, id) {
    commit("setKodeJenjang", id);
  },

  ensureFilterHalaman({ commit, getters }, name) {
    const existing = getters.getFilterHalaman(name);
    if (existing) {
      return existing;
    }
    const tahun_pendaftaran = getters.getTahunPendaftaran;
    const kode_jenjang = getters.getKodeJenjang;
    if (!tahun_pendaftaran || !kode_jenjang) {
      return null;
    }
    commit("setFilterHalaman", { name, tahun_pendaftaran, kode_jenjang });
    return { tahun_pendaftaran, kode_jenjang };
  },
  updateFilterHalaman({ commit }, payload) {
    commit("setFilterHalaman", payload);
  },

  updateTahunPendaftaran({ commit }, tahun) {
    commit("setTahunPendaftaran", tahun);
  },
  updateTahunAjaran({ commit }, tahun) {
    commit("setTahunAjaran", tahun);
  },

  updateSemesterPendaftaran({ commit }, semester) {
    commit("setSemesterPendaftaran", semester);
  },
  updateSemester({ commit }, semester) {
    commit("setSemester", semester);
  },

  reinit({ commit }) {
    commit("resetState");
  },
};
export default {
  namespaced: true,
  state,
  mutations,
  getters,
  actions,
};
