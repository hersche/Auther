<template>
  <v-card class="">
      <v-card-title><h1>{{ $t('Register') }}</h1></v-card-title>
      <v-card-text>
      <v-form ref="form" id="theForm" v-model="valid" lazy-validation>
        <input type="hidden" name="ajaxLogin" value="1">
        <input type="hidden" name="_token" :value="csrf">
        <div class="form-group col-12">
          <v-label>{{ $t('Avatar') }}</v-label>
          <Cropper v-bind:width="180" v-bind:height="180" type="circle" name="avatar" ></Cropper>
        </div>
        <div class="form-group col-12">
          <v-label>{{ $t('Background') }}</v-label>
          <Cropper v-bind:width="800" v-bind:height="394" type="square" name="background" theurl="/img/404/background.png" ></Cropper>
        </div>
        <v-text-field
          v-model="name"
          :label="$t('Name')"
          name="name"
          hint="This is only a showed name, that can be changed anytime"
          required
        ></v-text-field>
        <v-text-field
          v-model="username"
          :label="$t('Username')"
          name="username"
          :rules="[rules.required, usernameAvaible,usernameRules]"
          hint="You can not change this name after registration"
          required
        ></v-text-field>
        <v-alert
        :value="usernameAvaible&&usernameRules"
        type="success"
        style="background-color:green"
        >
          Username is avaible
        </v-alert>
        <v-alert
        :value="usernameRules==false"
        type="error"
        style="background-color:red"
        >
          Spaces in username are not allowed
        </v-alert>
        <v-alert
        :value="usernameAvaible==false&&this.username!=''"
        type="error"
        style="background-color:red"
        >
          Username is NOT avaible
        </v-alert>
        <v-text-field
          v-model="email"
          :label="$t('E-mail')"
          name="email"
          required
        ></v-text-field>
        <MarkdownCreator theText="" theId="bio" :theTitle="$t('Biographie')" :theHint="$t('Some words about you')+'...'" ></MarkdownCreator>
        <v-switch v-model="public" :label="$t('Public')+' '+$t('account')"></v-switch>
        <input type="hidden" name="public" :value="Number(public)" />
        <v-text-field
          v-model="password"
          :append-icon="showPassword ? 'visibility' : 'visibility_off'"
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
          :append-icon="showConfirmPassword ? 'visibility' : 'visibility_off'"
          :rules="[rules.required, rules.min]"
          :type="showConfirmPassword ? 'text' : 'password'"
          :label="$t('Confirm Password')"
          :hint="'At least ' +mixconfig.MIX_MIN_PASSWORDLENGTH+ ' characters'"
          counter
          name="password_confirmation"
          @click:append="showConfirmPassword = !showConfirmPassword"
        ></v-text-field>
        </v-form>
      </v-card-text>
      <v-card-actions>
        <v-btn @click="submitAction()">{{ $t('Register') }}</v-btn>
      </v-card-actions>
  </v-card>
</template>


<script>
  const $ = require("jquery");
  import { store } from '../../store.js';
  import Cropper from '../cropp'
  import MarkdownCreator from '../MarkdownCreator'
  export default {
    components: {
      MarkdownCreator,
      Cropper
    },
    props: ['baseUrl','mixconfig'],
    mounted: function () {
      if(this.loggeduserid!=0){
        this.$router.push("/");
      }
    },
    computed: {
      loggeduserid(){
        return store.state.loginId
      },
      usernameRules () {
        if(this.username.indexOf(' ')<0){
          return true
        }
        return false
      },
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
      csrf: function(){
        return store.getters.getCSRF()
      },
    },
    updated: function () {
    },
    data(){
      return {
        public: false,
        tmpBio:'',
        name:'',
        valid:true,
        email:'',
        username:'',
        password:'',
        confirmPassword:'',
        showPassword:false,
        showConfirmPassword:false,
        checkbox:false,
        tags:'',
        rules: {
          required: value => !!value || 'Required.',
          min: v => v.length >= Number(this.mixconfig.MIX_MIN_PASSWORDLENGTH) || 'Min '+this.mixconfig.MIX_MIN_PASSWORDLENGTH+' characters',
          emailMatch: () => ('The email and password you entered don\'t match')
        }
      }
    },
    methods: {
      submitAction() {
        let that = this;
        console.log("the form")
        console.log($("#theForm")[0])
        $.ajax({
            url: '/internal-api/register',
            type: 'POST',
            data: new FormData($("#theForm")[0]),
            cache: false,
            contentType: false,
            processData: false,
            complete : function(res) {
              if(res.status==201){
                  store.commit("addUser",res.responseJSON.data)
                  store.commit("setLoginId",res.responseJSON.data.id)
                  that.$router.push("/");
              }
              if(res.status==200){
                if(res.responseJSON.data.msg=="needemailverify"){
                  window.location.href = "/email/resend?token="+localStorage.getItem('jwt_token');
                }
              }
              //eventBus.$emit('login',res.responseJSON.data);
            }

        });
        return false;
      },
    },
  }
</script>
