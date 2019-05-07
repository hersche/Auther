

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
 const $ = require("jquery");
 const axios = require('axios');
 var loadedLangs=['en'];
 import { store } from './store.js';
 import { eventBus } from './eventBus.js';
 import { translation,dateTranslation } from './translation.js';
 import Vue from 'vue'
 import Vuetify from 'vuetify'
 import Router from 'vue-router'
 import Vuex from 'vuex'
 import VueCroppie from 'vue-croppie';
 import 'vuetify/dist/vuetify.min.css' 
 import 'material-icons/iconfont/material-icons.css';
 import VueI18n from 'vue-i18n'
 Vue.use(Vuetify, {
   theme: {
     primary: 'blue',
     secondary: '#b0bec5',
     accent: '#8c9eff',
   }
 })
 Vue.use(Vuex)
 Vue.use(Router)
 Vue.use(VueI18n)
 //Vue.use(BootstrapVue);
 Vue.use(VueCroppie);
 var appName = process.env.MIX_APP_NAME;
 console.log("appName",appName)
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

    var sidebarComp = Vue.component('thesidebar', require("./components/Navigation.vue").default);
    var overview = Vue.component('overview', require("./components/Home.vue").default);
    var profileComp = Vue.component('profile', require("./components/ProfileComponent.vue").default);
    var searchComp = Vue.component('search', require("./components/Search.vue").default);
    var editProfileComp = Vue.component('editprofile', require("./components/settings/EditProfile.vue").default);
    var editUsernameComp = Vue.component('editusername', require("./components/settings/EditUsername.vue").default);
    
    var friendsComp = Vue.component('friends', require("./components/settings/Friends.vue").default);
    var passwordComp = Vue.component('friends', require("./components/settings/password.vue").default);
    var loginComp = Vue.component('login', require("./components/auth/Login.vue").default);
    
        var checkLoginComp = Vue.component('checklogin', require("./components/auth/checkLogin.vue").default);
    
    var faLoginComp = Vue.component('twofaLogin', require("./components/auth/twofaLogin.vue").default);
    var twofaSettings = Vue.component('twofasettings', require("./components/settings/twofa.vue").default);
    var registerComp = Vue.component('register', require("./components/auth/Register.vue").default);
    var useradminComp = Vue.component('useradmin', require("./components/admin/UserAdmin.vue").default);
    var personalAccessTokensComp = Vue.component('PersonalAccessTokens', require("./components/passport/PersonalAccessTokens.vue").default);
    var clientsComp = Vue.component('Clients', require("./components/passport/Clients.vue").default);
    var authorizedClientsComp = Vue.component('AuthorizedClients', require("./components/passport/AuthorizedClients.vue").default);
    var projects = Vue.component('projects', require("./components/admin/Projects.vue").default);
    var roles = Vue.component('roles', require("./components/admin/Roles.vue").default);
    var i18n = new VueI18n({
      locale: 'en',
      fallbackLocale: 'en'
    })
    var lang = "en"
    function getLang(lang){
      if(loadedLangs.includes(lang)==false){
        loadedLangs.push(lang)
        $.getJSON('/lang/'+lang+".json").done(function(data){
          i18n.setLocaleMessage(lang, data.default)
          i18n.locale = lang
        });
      } else {
        i18n.locale = lang
      }
    }
    
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
 const routes = [
   { path: '/', component: overview },
   { path: '/profile/:profileId', component: profileComp },
   { path: '/search/:term', component: searchComp },
   { path: '/login', component: loginComp },
   { path: '/checkLogin', component: checkLoginComp },
   { path: '/admin/users', component: useradminComp },
   { path: '/admin/projects', component: projects },
   { path: '/admin/roles', component: roles },
   { path: '/twofaLogin', component: faLoginComp },
   { path: '/settings/editusername', component: editUsernameComp },
   { path: '/settings/2fa', component: twofaSettings },
   { path: '/settings/friends', component: friendsComp },
   { path: '/settings/password', component: passwordComp },
   { path: '/passport/clients', component: clientsComp },
   { path: '/settings/apps', component: authorizedClientsComp },
   { path: '/passport/personalaccess', component: personalAccessTokensComp },
   { path: '/settings/profile', component: editProfileComp },
   { path: '/register', component: registerComp },
 ]
 

 $( document ).ready(function() {
   
   store.getters.receiveUsers()
   if(store.state.loginId!=0){
     store.getters.receiveRoles()
   }
   store.getters.receiveProjects()
   lang = 'en';
   if(localStorage.getItem("language")!=''&&localStorage.getItem("language")!=undefined){
     lang = localStorage.getItem("language");
   }
   if(lang!="en"){
     getLang(lang)
   }
var app = new Vue({
  i18n,
    data : {
      appname:process.env.MIX_APP_NAME,
      mixconfig:process.env,
      search:undefined,
      treecatptions:undefined,
      canloadmore:true,
      baseUrl:'',
      alertshown:false,
      alerttext:'',
      alertcolor:'success'
    },
    router:new Router({ routes,
      scrollBehavior (to, from, savedPosition) {
        return { x: 0, y: 0 }
      }
   }),
   methods:{
     alert(msg,type="green",icon=''){
       console.log("alert-method")
       this.alertshown=true
       this.alerttext=msg
       this.alertcolor=type
       // this.$vs.notify({title:msg,text:'',icon:icon,color:type,position:'bottom-center'})
     }
   },
    components : {
        'thesidebar': sidebarComp
    },
}).$mount('#app');
eventBus.$on('languageChange', lang => {
  getLang(lang)
});
eventBus.$on('login', user => {
  console.log("receive login-signal",user)
  store.commit("setLoginId", user.id)
  app.alert("Welcome back, "+store.getters.getUserById(store.state.loginId).name,"success","exit_to_app")
    app.$router.push('/');
  });
  eventBus.$on('closeAlarm', title => {
    app.alertshown = false
  });
  eventBus.$on('alert', a => {
    app.alert(a)
  });
  eventBus.$on('logout', settings => {
    store.commit("setLoginId", 0)
    app.alert("Logged out","danger","power_settings_new")
    app.$router.push('/');
    store.getters.getCSRF()
  });
});