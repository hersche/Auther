<template>
<div>
  <v-toolbar fixed dark color="primary" style="z-index:99999">
    <v-toolbar-side-icon @click="active=true"></v-toolbar-side-icon>
    <router-link class="" to="/"><v-toolbar-title class="white--text" to="/">LaraTube</v-toolbar-title></router-link>

    <v-spacer></v-spacer>
    <v-flex xs5 sm4 md3 lg3 align-right>
      <v-text-field
      hide-details
      append-icon="search"
      single-line
      v-model="search"
      id="theLiveSearch"
      :placeholder="$t('Search')+'...'"
      @keyup="searching()" @focus="searching()"
      ></v-text-field>
    </v-flex>
  </v-toolbar>
    
    
    <v-navigation-drawer
      v-model="active"
      fixed
      style="z-index:99999; height: 100%;overflow-x:auto; max-width:90%;"
      dark
      temporary
      >
      <v-list-tile>
        <v-list-tile-action>
          <v-btn @click="active=false" color="orange" style="cursor:pointer;" :icon="true"><v-icon>close</v-icon></v-btn>
        </v-list-tile-action>  
      </v-list-tile>
      <v-list-tile>
        <v-list-tile-action>
          <v-icon>language</v-icon>
        </v-list-tile-action>
        <v-select
        v-model="lang"
        attach
        chips
        :items="[{value:'en',text:'English'},{value:'de',text:'Deutsch'}]"
        :label="$t('Language')"
        ></v-select>
<!--    <select id="langSelect" class="float-right custom-select custom-select-sm ml-1" v-model="lang" >
      <option value="en">EN</option>
      <option value="de">DE</option>
    </select>-->
  </v-list-tile>
    <v-list class="pa-1">
      <v-list-tile  avatar tag="div" v-if="currentuser.id!=0" :style="'background-image:url('+currentuser.background+');'">
      <v-badge left color="orange" overlap>
        <router-link class="small" slot="badge" to="/notifications">{{ n }}</router-link>
        <router-link :to="'/profile/'+currentuser.id">
        <v-list-tile-avatar >
          <img :src="currentuser.avatar">
        </v-list-tile-avatar>
      </router-link>
      </v-badge>
      <v-list-tile-content>
        <v-list-tile-title>{{ currentuser.name }}</v-list-tile-title>
      </v-list-tile-content>
    </v-list-tile>
  </v-list>

  <v-list class="pt-0" dense>
    <v-list-tile to="/">
      <v-list-tile-action>
        <v-icon>home</v-icon>
      </v-list-tile-action>
      <v-list-tile-content>
        <v-list-tile-title>{{ $t('Home') }}</v-list-tile-title>
      </v-list-tile-content>
    </v-list-tile>
    

    
    <v-list-group
      prepend-icon="build"
      no-action
    >
      <v-list-tile slot="activator">
        <v-list-tile-title>Debug</v-list-tile-title>
      </v-list-tile>
      <v-list-group
        prepend-icon="warning"
        no-action
        sub-group
        v-if="currentuser.id!=0"
      >
        <v-list-tile slot="activator">
          <v-list-tile-title>Oauth</v-list-tile-title>
        </v-list-tile>
        <v-list-tile to="/passport/clients">
          <v-list-tile-action>
            <v-icon>account_box</v-icon>
          </v-list-tile-action>
          <v-list-tile-content>
            <v-list-tile-title>{{ $t('Clients') }}</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>
        
        <v-list-tile to="/passport/personalaccess">
          <v-list-tile-action>
            <v-icon>account_box</v-icon>
          </v-list-tile-action>
          <v-list-tile-content>
            <v-list-tile-title>{{ $t('Personal access tokens') }}</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>
      </v-list-group>
      <v-list-tile>
        <v-list-tile-action>
          <v-icon>multiline_chart</v-icon>
        </v-list-tile-action>
      </v-list-tile>

      <v-list-tile>
        <v-list-tile-action>
          <v-icon>account_box</v-icon>
        </v-list-tile-action>
        <v-list-tile-content>
          <v-list-tile-title>{{ $t('Users') }}: {{ users.length }}</v-list-tile-title>
        </v-list-tile-content>
      </v-list-tile>
    </v-list-group>
    <v-list-tile v-if="currentuser.id==0" to="/login">
      <v-list-tile-action>
        <v-icon>exit_to_app</v-icon>
      </v-list-tile-action>
      <v-list-tile-content>
        <v-list-tile-title>{{ $t('Login') }}</v-list-tile-title>
      </v-list-tile-content>
    </v-list-tile>

    <v-list-tile v-if="currentuser.id==0" to="/register">
      <v-list-tile-action>
        <v-icon>person_add</v-icon>
      </v-list-tile-action>
      <v-list-tile-content>
        <v-list-tile-title>{{ $t('Register') }}</v-list-tile-title>
      </v-list-tile-content>
    </v-list-tile>

    <v-list-group
      prepend-icon="settings"
      no-action
      v-if="currentuser.admin"
    >    
    <v-list-tile slot="activator">

      <v-list-tile-title>Admin</v-list-tile-title>

    </v-list-tile>
    <v-list-tile to="/admin/users">
      <v-list-tile-action>
        <v-icon>supervised_user_circle</v-icon>
      </v-list-tile-action>
      <v-list-tile-content>
        <v-list-tile-title>Users</v-list-tile-title>
      </v-list-tile-content>
    </v-list-tile>
    
    <v-list-tile to="/admin/roles">
      <v-list-tile-action>
        <v-icon>reorder</v-icon>
      </v-list-tile-action>
      <v-list-tile-content>
        <v-list-tile-title>Roles</v-list-tile-title>
      </v-list-tile-content>
    </v-list-tile>
    
    <v-list-tile to="/admin/projects">
      <v-list-tile-action>
        <v-icon>whatshot</v-icon>
      </v-list-tile-action>
      <v-list-tile-content>
        <v-list-tile-title>Projects</v-list-tile-title>
      </v-list-tile-content>
    </v-list-tile>
  </v-list-group>
      
      
    <v-list-group
      prepend-icon="settings"
      no-action
      v-if="currentuser.id!=0"
    >    
    <v-list-tile slot="activator">

      <v-list-tile-title>{{ $t('Settings') }}</v-list-tile-title>

    </v-list-tile>

    
    <v-list-tile to="/settings/profile">
      <v-list-tile-action>
        <v-icon>account_circle</v-icon>
      </v-list-tile-action>
      <v-list-tile-content>
        <v-list-tile-title>{{ $t('Profile') }}</v-list-tile-title>
      </v-list-tile-content>
    </v-list-tile>
  
    <v-list-tile v-if="currentuser.allow_username_change" to="/settings/editusername">
      <v-list-tile-action>
        <v-icon>account_circle</v-icon>
      </v-list-tile-action>
      <v-list-tile-content>
        <v-list-tile-title>{{ $t('Edit username') }}</v-list-tile-title>
      </v-list-tile-content>
    </v-list-tile>
  
    <v-list-tile to="/settings/friends">
      <v-list-tile-action>
        <v-icon>accessibility</v-icon>
      </v-list-tile-action>
      <v-list-tile-content>
        <v-list-tile-title>{{ $t('Friends') }}</v-list-tile-title>
      </v-list-tile-content>
    </v-list-tile>
    
    <v-list-tile to="/settings/apps">
      <v-list-tile-action>
        <v-icon>apps</v-icon>
      </v-list-tile-action>
      <v-list-tile-content>
        <v-list-tile-title>{{ $t('Manage apps') }}</v-list-tile-title>
      </v-list-tile-content>
    </v-list-tile>
    
    
    <v-list-tile to="/settings/2fa">
      <v-list-tile-action>
        <v-icon>security</v-icon>
      </v-list-tile-action>
      <v-list-tile-content>
        <v-list-tile-title>2factor-Auth</v-list-tile-title>
      </v-list-tile-content>
    </v-list-tile>
    
    <v-list-tile to="/settings/password">
      <v-list-tile-action>
        <v-icon>lock</v-icon>
      </v-list-tile-action>
      <v-list-tile-content>
        <v-list-tile-title>{{ $t('Password') }}</v-list-tile-title>
      </v-list-tile-content>
    </v-list-tile>
  </v-list-group>

      
      
      
      
      
      
      
    <v-list-tile v-if="currentuser.id!=0" @click="emitLogout()" >
      <v-list-tile-action>
        <v-icon>power_settings_new</v-icon>
      </v-list-tile-action>
      <v-list-tile-content>
        <v-list-tile-title>{{ $t('Logout') }}</v-list-tile-title>
      </v-list-tile-content>
    </v-list-tile>
    <v-list-tile to="/about">
      <v-list-tile-action>
        <v-icon>help</v-icon>
      </v-list-tile-action>
      <v-list-tile-content>
        <v-list-tile-title>{{ $t('About') }}</v-list-tile-title>
      </v-list-tile-content>
    </v-list-tile>
    
  </v-list>
</v-navigation-drawer>
<!--
<v-speed-dial
  v-model="speedDeal"
  bottom
  v-if="currentuser!=undefined"
  right
  fixed
>
  <v-btn
    slot="activator"
    v-model="speedDeal"
    color="blue darken-2"
    dark
    fab
  >
    <v-icon>account_circle</v-icon>
    <v-icon>close</v-icon>
  </v-btn>
  <v-btn
    fab
    dark
    small
    color="green"
  >
    <v-icon>edit</v-icon>
  </v-btn>
  <v-btn
    fab
    dark
    small
    color="indigo"
    to="/upload"
  >
    <v-icon>add</v-icon>
  </v-btn>
  <v-btn
    fab
    dark
    small
    color="red"
  >
    <v-icon>delete</v-icon>
  </v-btn>
</v-speed-dial>
-->

</v-toolbar>
<form class="d-none" id="logoutForm">
  <input type="hidden" name="_token" :value="csrf">
</form>
<v-snackbar
v-model="alarmEnabledInternal"
:bottom="true"
:color="alertcolor"
:multi-line="true"
:timeout="9000"
>
{{ alerttext }}
<v-btn

flat
@click="closeAlarm()"
>
{{ $t('Close') }}
</v-btn>
</v-snackbar>
    </div>
</template>

<script>
  import { store } from '../store.js';
    import { eventBus } from '../eventBus.js';
   const $ = require("jquery");
export default {
  mounted(){
    if(localStorage.getItem("language")!=''&&localStorage.getItem("language")!=undefined){
      this.lang = localStorage.getItem("language");
    }
    if(this.$route.params.term!=undefined&&this.$route.params.term!=''){
      this.search = this.$route.params.term;
    }

},
  methods:{
    closeAlarm(){
      eventBus.$emit('closeAlarm',"");
    },
    searching(){
      if(this.search!=''){
        if(this.$route.fullPath.indexOf("search")==-1){
          this.urlBeforeSearch = this.$route.fullPath;
        }
        this.$router.push("/search/"+this.search)
      } else {
        if(this.$route.fullPath.indexOf("search")!=-1){
          this.$router.push(this.urlBeforeSearch)
        }
      }
    //  this.$route.params.profileId
    //  eventBus.$emit('refreshSearch',"");
    },
    emitLogout: function() {
      $.ajax({
          url: '/logout',
          type: 'POST',
          data: new FormData($("#logoutForm")[0]),
          cache: false,
          contentType: false,
          processData: false,
          complete : function(res) {
            if(res.status==200){
              eventBus.$emit('logout',"");
              //eventBus.$emit('login',res.responseJSON.data);
            }
          }

      });

    },
  },
  props:['alertshown','alerttext','alertcolor'],
  computed:{
    csrf: function(){
      return store.getters.getCSRF()
    },
    notifications: function(){
      return store.state.notifications
    },
    currentuser(){
      //return undefined
      return store.getters.getUserById(store.state.loginId)
    },
    users: function(){ 
      return store.state.users
    }
  },
  watch:{
    alarmEnabledInternal: function(val){
      if(val==false){
        eventBus.$emit('closeAlarm',"");
      }
    },
    alertshown: function(val){
      console.log("react to alertshown",val)
      this.alarmEnabledInternal = val
    },  
    lang:function(val){
      localStorage.setItem("language",val);
      eventBus.$emit('languageChange',val);
    },
    notifications:function(val){
      var tmpArray = []
      this.notifications.forEach(function(val,key){
        if(val.read_at==null||val.read_at==0||val.read_at==undefined){
          tmpArray.push(val)
        }
      });
      this.n = tmpArray.length;
    }
  },
  data:()=>({
    active:false,
    activeItem:0,
    lang:'en',
    n:0,
    urlBeforeSearch:'/',
    search:'',
    mini:false,
    alarmEnabledInternal:false,
    speedDeal:false,
    treeTypes: [{id:'audio',label:'Audio'},{id:'video',label:'Video'}]
    

  })
}
</script>

<style lang="stylus">
.vue-treeselect__multi-value
  display inline-flex
  overflow hidden
vs-navbar >>> .vue-treeselect__menu
  width 100px
  padding-left 15px
  padding-bottom 15px
  z-index 999999
.header-sidebar
  display flex
  align-items center
  justify-content center
  flex-direction column
  width 100%
  h4
    display flex
    align-items center
    justify-content center
    width 100%
    > button
      margin-left 10px
.footer-sidebar
  display flex
  align-items center
  justify-content space-between
  width 100%
  > button
      border 0px solid rgba(0,0,0,0) !important
      border-left 1px solid rgba(0,0,0,.07) !important
      border-radius 0px !important
</style>
