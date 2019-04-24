<template>
  <div v-if="currentuser.allow_username_change">
    <div class="text-center">
      <h1>{{ $t("Change") }} {{ $t("username") }}</h1>
      <p>Because your username was generated randomly / you logged in with a external provider like google, you can change your username once here. After you did this, you can not change the username as it's basicly static.</p>
      <v-text-field
        label="New username"
        v-model="username"
        type="text"
        ></v-text-field>
        <v-alert
        :value="usernameAvaible"
        type="success"
        style="background-color:green"
        >
          Username {{ username }} is avaible
        </v-alert>
        <v-alert
        :value="usernameAvaible==false"
        type="error"
        style="background-color:red"
        >
          Username {{ username }} is NOT avaible
        </v-alert>
        <v-btn v-if="usernameAvaible" @click="submitAction()">Change username</v-btn>
      </div>
    </div>
</template>
<script>
  import { store } from '../../store.js';
  import { eventBus } from '../../eventBus.js';
  import MarkdownCreator from '../MarkdownCreator'
  const $ = require("jquery");
  const axios = require("axios");
  export default {
    props: ['baseUrl'],
    components: {
      MarkdownCreator
    },
    mounted: function () {

    //  this.$refs.croppieBackgroundRef.bind({
      //  url: '/img/404/background.png',
      //})
    },
    updated: function () {

    },
    computed: {
      usernameAvaible(){
        if(this.username==""){
          return false
        }
        if(store.getters.getUserByUsername(this.username)!=undefined){
          return false
        } else {
          return true
        }
      },

      loggeduserid(){
        return store.state.loginId
      },
      csrf: function(){
        return store.getters.getCSRF()
      },
      currentuser: function(){
        var u = store.getters.getUserById(store.state.loginId)
        if(u!=undefined){
          this.tmpBio = u.bio
        }
        return u
      },
    },

    methods: {
      rmBr(str) {
        return str.replace(/<br\s*\/?>/mg,"");
      },
      submitAction() {
        let that = this;
        if(this.usernameAvaible){
        axios.post("/internal-api/setusername",{"username":this.username,"_token":this.csrf})  
            .then(function (response) {
              store.commit("setUsers",JSON.parse(response.request.response).data)
              that.$router.push("/profile/"+that.currentuser.id);
           })
           .catch(function (error) {
             console.log(error);
           })
         }
        return false;
        
      },
    },
    data(){
      return {
        mediaType: '',
        username:'',
        checkTwofactorCode: '',
        public: false,
        editpicloaded:false,
        showdismissiblealert: false,
        avatarCropped: null,
        tmpBio:'',
        showUserpassword:false,
        userpassword:'',
        showOldPass:false,
        showNewPass:false,
        showNewPass2:false,
        backgroundCropped: null,
      }
    }
  }
</script>
