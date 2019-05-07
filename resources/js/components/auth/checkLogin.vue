<template>
  <div class="">
    <h1 class="text-center">{{ $t('Login') }}</h1>
    <p>This profile already exists. Please enter your login-details once to enable the provider</p>
      <v-form ref="form" id="loginForm" v-model="valid" lazy-validation>
        <input type="hidden" name="_token" :value="csrf">
        <input type="hidden" name="ajaxLogin" value="1">
        <v-text-field v-model="email" label="E-mail or username"
          name="email" v-on:keyup.enter="submitLogin()"
          required></v-text-field>
        <v-text-field
          v-model="password"
          v-on:keyup.enter="submitLogin()"
          :append-icon="showPassword ? 'visibility_off' : 'visibility'"
          :rules="[rules.required, rules.min]"
          :type="showPassword ? 'text' : 'password'"
          name="password"
          :label="$t('Password')"
          hint="At least 8 characters"
          counter
          @click:append="showPassword = !showPassword"
          ></v-text-field>
      </v-form>
      <v-btn @click="submitLogin()">
        {{ $t('Login') }}
      </v-btn>
    </div>
</template>


<script>
  const $ = require("jquery");
  import { store } from '../../store.js';
  import { eventBus } from '../../eventBus.js';
  export default {
    props: ['baseUrl','mixconfig'],
    data(){
      return {
        valid:true,
        email:'',
        password:'',
        showPassword:false,
        rememberMe:false,
        rules: {
          required: value => !!value || 'Required.',
          min: v => v.length >= 8 || 'Min 8 characters',
          emailMatch: () => ('The email and password you entered don\'t match')
        }
      }
    },
    mounted(){
      if(this.loggeduserid!=0){
        this.$router.push("/");
      }
    },
    computed: {
      csrf: function(){
        return store.getters.getCSRF()
      },
      loggeduserid(){
        return store.state.loginId
      },
    },
    methods:{
      submitLogin() {
        let that = this;
        $.ajax({
            url: '/login',
            type: 'POST',
            data: new FormData($("#loginForm")[0]),
            cache: false,
            contentType: false,
            processData: false,
            complete : function(res) {
              if(res.status==200){
                console.log("received login", res)
                if(res.responseText=="{\"twofactor\":true}"){
                  that.$router.push('/twofaLogin');
                } else {
                  eventBus.$emit('login',res.responseJSON.data);
                }
                
              } else if(res.status==401){
                eventBus.$emit('alert',"Login failed");
              }
              console.log("received login")
            //  eventBus.$emit('refreshMedia',that.currentmedia.id);
            //  eventBus.$emit('videoEdited',[that.currentmedia.title,res.responseJSON])
            }

        });
        return false;
      },
    }

  }
</script>
