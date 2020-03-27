<template>
    <div v-if="currentuser.id!=0">

        <v-card>
            <v-card-title><h1 class="text-center">{{ $t("Edit") }} {{ $t("profile") }}</h1></v-card-title>
            <v-card-text>
                <form id="theForm">
                    <input :value="csrf" name="_token" type="hidden">
                    <v-text-field
                        :label="$t('Name')"
                        :value="currentuser.name"
                        name="name"
                        required
                    ></v-text-field>
                    <div class="form-group">
                        <label>{{ $t("Avatar") }}</label>
                        <Cropper name="avatar" type="circle" v-bind:height="180" v-bind:theurl="currentuser.avatar"
                                 v-bind:width="180"></Cropper>
                    </div>
                    <div class="form-group">
                        <label>{{ $t("Background") }}</label>
                        <Cropper name="background" type="square" v-bind:height="394"
                                 v-bind:theurl="currentuser.background" v-bind:width="800"></Cropper>
                    </div>
                    <v-switch :label="$t('Public')+' '+$t('account')" v-model="public"></v-switch>
                    <input :value="Number(public)" name="public" type="hidden"/>
                    <v-switch :label="$t('Track')+' '+$t('logins')" v-model="track_logins"></v-switch>
                    <input :value="Number(track_logins)" name="track_logins" type="hidden"/>
                    <MarkdownCreator :theText="currentuser.bio" :theTitle="$t('Biographie')"
                                     theId="bio"></MarkdownCreator>
                </form>
            </v-card-text>
            <v-card-actions>
                <v-btn @click="submitAction()" color="green">
                    <v-icon>save</v-icon>
                    {{ $t('Save') }}
                </v-btn> <!-- <button @click="deleteAction();" class="btn btn-danger" >Delete</button> -->
            </v-card-actions>
        </v-card>
    </div>
</template>
<script>
    import {store} from '../../store.js';
    import {eventBus} from '../../eventBus.js';
    import MarkdownCreator from '../MarkdownCreator'
    import Cropper from '../cropp'
    const axios = require("axios");
    export default {
        props: ['baseUrl', 'mixconfig'],
        components: {
            MarkdownCreator,
            Cropper
        },
        mounted: function () {
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
                if (u.id != 0) {
                    if (this.init) {
                        this.init = false
                        this.tmpBio = u.bio
                        if (u.public != NaN && u.public != undefined) {
                            this.public = u.public
                        }
                        if (u.track_logins != NaN && u.track_logins != undefined) {
                            this.track_logins = u.track_logins
                        }
                    }
                }
                return u
            },
        },

        methods: {
            submitAction() {
                let that = this;
                var d = new FormData(document.getElementById('theForm'))
                d.delete("avatarFile")
                d.delete("backgroundFile")
                var formData = new FormData(document.getElementById('logoutForm'));
                axios({
                    method: 'post',
                    url: '/internal-api/profiles/edit/' + this.currentuser.id,
                    data: d,
                    headers: {'Content-Type': 'multipart/form-data' }
                }).then(function (response) {
                    store.commit("setUsers", response.data.data);
                    eventBus.$emit('alert', "Profile saved");
                    that.$router.push('/profile/' + that.currentuser.id);
                }).catch(function (error) {
                        console.log(error);
                    })
                return false;
            },
        },
        data() {
            return {
                mediaType: '',
                public: false,
                init: true,
                track_logins: false,
                editpicloaded: false,
                showdismissiblealert: false,
                avatarCropped: null,
                tmpBio: '',
                backgroundCropped: null,
            }
        }
    }
</script>
