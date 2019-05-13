<template>
  <div v-if="currentuser.allow_username_change">
    <div class="text-center">
      <h1>{{ $t("Change") }} {{ $t("login") }}</h1>
      <p>Because your username and password was generated randomly / you logged in with a external provider like google, you can change your username and password once here.</p>
      <p>When you set this info, you can login with these information.</p>
      <p>HOWEVER: You can still login with the provider and you can set this login-information later.</p>
      <p>You can find this in the menu under Settings. <b>After you performed this action, this option will disappear.</b></p>
      <v-text-field
        :label="$t('New')+' '+$t('username')"
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
        <v-text-field
          v-model="password"
          :append-icon="showPassword ? 'visibility_off' : 'visibility'"
          :rules="[rules.required, rules.min]"
          :type="showPassword ? 'text' : 'password'"
          name="password"
          :label="$t('Password')"
          :hint="'At least ' +mixconfig.MIX_MIN_PASSWORDLENGTH+ ' characters'"
          counter
          @click:append="showPassword = !showPassword"
        ></v-text-field>
        <v-text-field
          v-model="confirmPassword"
          :append-icon="showConfirmPassword ? 'visibility_off' : 'visibility'"
          :rules="[rules.required, rules.min]"
          :type="showConfirmPassword ? 'text' : 'password'"
          :label="$t('Confirm Password')"
          :hint="'At least ' +mixconfig.MIX_MIN_PASSWORDLENGTH+ ' characters'"
          counter
          name="password_confirmation"
          @click:append="showConfirmPassword = !showConfirmPassword"
        ></v-text-field>
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
    props: ['baseUrl','mixconfig'],
    components: {
      MarkdownCreator
    },
    mounted: function () {
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
      submitAction() {
        let that = this;
        if(this.usernameAvaible){
        axios.post("/internal-api/setlogin",{"username":this.username,"password":this.password,"confirm_password":this.confirmPassword,"_token":this.csrf})  
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
        username:'',
        password:'',
        confirmPassword:'',
        showPassword:false,
        showConfirmPassword:false,
        rules: {
          required: value => !!value || 'Required.',
          min: v => v.length >= Number(this.mixconfig.MIX_MIN_PASSWORDLENGTH) || 'Min '+this.mixconfig.MIX_MIN_PASSWORDLENGTH+' characters',
          emailMatch: () => ('The email and password you entered don\'t match')
        }
      }
    }
  }
</script>
