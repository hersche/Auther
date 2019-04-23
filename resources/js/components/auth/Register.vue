<template>
  <div class="">
      <h1 class="text-center">{{ $t('Register') }}</h1>
      <v-form ref="form" id="theForm" v-model="valid" lazy-validation>
        <input type="hidden" name="ajaxLogin" value="1">
        <input type="hidden" name="_token" :value="csrf">
        <div class="form-group row">
          <label class="col-md-4 col-form-label text-md-right">Avatar</label>
          <Cropper v-bind:width="180" v-bind:height="180" type="circle" name="avatar" ></Cropper>
        </div>
        <div class="form-group row">
          <label class="col-md-4 col-form-label text-md-right">Background</label>
          <Cropper v-bind:width="800" v-bind:height="394" type="square" name="background" ></Cropper>
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
          hint="You can not change this name after registration"
          required
        ></v-text-field>  
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
          v-model="tags"
          label="Tags"
          name="tags"
        ></v-text-field>
        <v-text-field
          v-model="password"
          :append-icon="show1 ? 'visibility_off' : 'visibility'"
          :rules="[rules.required, rules.min]"
          :type="show1 ? 'text' : 'password'"
          name="password"
          :label="$t('Password')"
          hint="At least 8 characters"
          counter
          @click:append="show1 = !show1"
        ></v-text-field>
        <v-text-field
          v-model="password2"
          :append-icon="show2 ? 'visibility_off' : 'visibility'"
          :rules="[rules.required, rules.min]"
          :type="show2 ? 'text' : 'password'"
          :label="$t('Confirm Password')"
          hint="At least 8 characters"
          counter
          name="password_confirmation"
          @click:append="show2 = !show2"
        ></v-text-field>
        </v-form>
        <v-btn @click="submitAction()">{{ $t('Register') }}</v-btn>
  </div>
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
    props: ['baseUrl'],
    mounted: function () {
      this.$refs.croppieAvatarRef.bind({
        url: '/img/404/avatar.png',
      })
      this.$refs.croppieBackgroundRef.bind({
        url: '/img/404/background.png',
      })
    },
    computed: {
      csrf: function(){
        return store.getters.getCSRF()
      },
    },
    updated: function () {
      this.$nextTick(function () {
        if(this.$refs.croppieRef!=undefined&this.editpicloaded==false){
          this.editpicloaded=true;
          /*console.log("redo picture")
          this.$refs.croppieAvatarRef.bind({
            url: this.currentuser.avatar,
          })
          this.$refs.croppieBackgroundRef.bind({
            url: this.currentuser.background,
          })
          */
        }
      })
    },
    data(){
      return {
        avatarCropped: null,
        backgroundCropped: null,
        public: false,
        tmpBio:'',
        name:'',
        valid:true,
        email:'',
        username:'',
        password:'',
        password2:'',
        show1:false,
        show2:false,
        checkbox:false,
        tags:'',
        rules: {
          required: value => !!value || 'Required.',
          min: v => v.length >= 8 || 'Min 8 characters',
          emailMatch: () => ('The email and password you entered don\'t match')
        }
      }
    },
    methods: {
      avatarChange(){
        var reader = new FileReader();
        let that = this;
       reader.onload = function (e) {
         that.$refs.croppieAvatarRef.bind({
             url: e.target.result,
         });
        }
        reader.readAsDataURL($("#avatarUpload")[0].files[0]);

      },
      backgroundChange(){
        var reader = new FileReader();
        let that = this;
       reader.onload = function (e) {
         that.$refs.croppieBackgroundRef.bind({
             url: e.target.result,
         });
        }
        reader.readAsDataURL($("#backgroundUpload")[0].files[0]);

      },
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
              if(res.status==200){
              }
              //eventBus.$emit('login',res.responseJSON.data);
            }

        });
        return false;
      },
      // CALBACK USAGE
      resultAvatar(output) {
        this.avatarCropped = output;
      },
      resultBackground(output) {
        this.backgroundCropped = output;
      },
      updateAvatar(val) {
        let options = {
          format: 'png'
        }
        this.$refs.croppieAvatarRef.result(options, (output) => {
          this.avatarCropped = output;
        });
      },
      updateBackground(val) {
        let options = {
          format: 'png'
        }
        this.$refs.croppieBackgroundRef.result(options, (output) => {
          this.backgroundCropped = output;
        });
      },
      rotateAvatar(rotationAngle,event) {
        // Rotates the image
        if (event) event.preventDefault()
        this.$refs.croppieAvatarRef.rotate(rotationAngle);
      },
      rotateBackground(rotationAngle,event) {
        // Rotates the image
        if (event) event.preventDefault()
        this.$refs.croppieBackgroundRef.rotate(rotationAngle);
      }
    },
  }
</script>
