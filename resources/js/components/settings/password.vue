<template>
    <div>
        <div class="text-center">
            <h1>{{ $t("Change") }} {{ $t("password") }}</h1>
            <v-text-field
                :append-icon="showOldPass ? 'visibility_off' : 'visibility'"
                :type="showOldPass ? 'text' : 'password'"
                @click:append="showOldPass=!showOldPass"
                label="Old password (always needed for do something here)"
            ></v-text-field>
            <v-text-field
                :append-icon="showNewPass ? 'visibility_off' : 'visibility'"
                :type="showNewPass ? 'text' : 'password'"
                @click:append="showNewPass=!showNewPass"
                label="New password"
            ></v-text-field>
            <v-text-field
                :append-icon="showNewPass2 ? 'visibility_off' : 'visibility'"
                :type="showNewPass2 ? 'text' : 'password'"
                @click:append="showNewPass2=!showNewPass2"
                label="Confirm new password"
            ></v-text-field>
            <v-btn @click="changePassword()">Change password</v-btn>
            <v-text-field
                label="Change email"
                type="text"
                v-model="email"
            ></v-text-field>
            <v-btn @click="changeEmail()">Change email</v-btn>
        </div>
    </div>
</template>
<script>
    import {store} from '../../store.js';
    import {eventBus} from '../../eventBus.js';
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
            loggeduserid() {
                return store.state.loginId
            },
            csrf: function () {
                return store.getters.getCSRF()
            },
            currentuser: function () {
                var u = store.getters.getUserById(store.state.loginId)
                if (u != undefined) {
                    this.tmpBio = u.bio
                }
                return u
            },
        },

        methods: {
            changePassword() {
                axios.post("/internal-api/settings/password", {
                    "oldpass": this.oldPass,
                    "newpass": this.newPass,
                    "newpass2": this.newPass2,
                    "_token": this.csrf
                })
                    .then(function (response) {
                        //store.commit("setTwofactor",JSON.parse(response.request.response).data)
                        console.log("get twofactor", JSON.parse(response.request.response).data);
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
            },
            changeEmail() {
                axios.post("/internal-api/settings/email", {
                    "oldpass": this.oldPass,
                    "email": this.email,
                    "_token": this.csrf
                })
                    .then(function (response) {
                        //store.commit("setTwofactor",JSON.parse(response.request.response).data)
                        console.log("get twofactor", JSON.parse(response.request.response).data);
                        window.location.href = "/email/verify?token=" + localStorage.getItem('jwt_token')
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
            },
        },
        data() {
            return {
                mediaType: '',
                checkTwofactorCode: '',
                public: false,
                editpicloaded: false,
                showdismissiblealert: false,
                avatarCropped: null,
                tmpBio: '',
                showUserpassword: false,
                userpassword: '',
                showOldPass: false,
                showNewPass: false,
                showNewPass2: false,
                email: "",
            }
        }
    }
</script>
