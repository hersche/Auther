<template>
<div class="">
  <h1 class="text-center">{{ $t('2FA-Login') }}</h1>
          <v-form
            ref="form"
            id="oneTimePasswordForm"
            v-model="valid"
            lazy-validation
            >
            <input type="hidden" name="_token" :value="csrf">
            <input type="hidden" name="ajaxLogin" value="1">
            <v-text-field
              label="2fa-Token"
              name="one_time_password"
              v-on:keyup.enter="submitLogin()"
              required
              ></v-text-field>
              </v-form>
                      <v-btn @click="submitLogin()">
                          {{ $t('Login') }}
                      </v-btn>

                      <v-form
                        ref="form"
                        id="oneTimePasswordRecoveryForm"
                        v-model="recoveryvalid"
                        lazy-validation
                        >
                        <input type="hidden" name="_token" :value="csrf">
                          <input type="hidden" name="ajaxLogin" value="1">
                        <v-text-field
                          label="2fa-token-recovery-code"
                          v-on:keyup.enter="recoveryLogin()"
                          v-model="recoverycode"
                          required
                          ></v-text-field>


                          </v-form>
                                  <v-btn @click="recoveryLogin()">
                                      {{ $t('Apply') }} {{ $t('recovery-code') }}
                                  </v-btn>

                      <v-btn @click="cancelProcess()">
                          {{ $t('Cancel') }} {{ $t('login') }}
                      </v-btn>
</div>
</template>


<script>
 const axios = require("axios");
  import { store } from '../../store.js';
  import { eventBus } from '../../eventBus.js';
  export default {
    props: ['baseUrl'],
    data(){
      return {
        valid:true,
        recoveryvalid:true,
        recoverycode:'',
        email:'',
        password:'',
        show1:false,
        checkbox:false,
      }
    },
    computed: {
      csrf: function(){
        return store.getters.getCSRF()
      },
    },
    methods:{
      cancelProcess(){
        axios.post("/internal-api/twofactor/cancel",{"_token":this.csrf})
        .then(function (response) {
          this.$router.push("/")
       })
       .catch(function (error) {
         console.log(error);
       })
      },
      recoveryLogin(){
        let that = this
        axios.post("/internal-api/twofactor/recovery",{"_token":this.csrf,"recovery-secret":this.recoverycode,"ajaxLogin":"1"})
        .then(function (response) {
          //console.log("recovery-login: ",JSON.parse(response.request.response).data)
          eventBus.$emit('login',JSON.parse(response.request.response).data)
          that.$router.push("/")
       })
       .catch(function (error) {
         console.log(error);
       })
      },
      submitLogin() {
        let that = this;
        $.ajax({
            url: '/2faVerify',
            type: 'POST',
            data: new FormData($("#oneTimePasswordForm")[0]),
            cache: false,
            contentType: false,
            processData: false,
            complete : function(res) {
              if(res.status==200){
                eventBus.$emit('login',res.responseJSON.data);
              } else if(res.status==401){
                this.password = '';
                eventBus.$emit('alert',"2-factor auth failed");
              } else if(res.status==500){
                  eventBus.$emit('alert',"System-error");
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
