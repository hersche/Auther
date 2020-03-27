<template>
    <div>
    </div>
</template>
<script>
    import {store} from '../../store.js';
    import {eventBus} from '../../eventBus.js';
    import MarkdownCreator from '../MarkdownCreator'

    const $ = require("jquery");
    const axios = require("axios");
    export default {
        props: ['baseUrl', 'mixconfig'],
        components: {
            MarkdownCreator
        },
        mounted: function () {
            this.providers = this.getProviders();
        },
        updated: function () {
        },
        computed: {
            usernameAvaible() {
                if (this.username == "") {
                    return false
                }
                if (store.getters.getUserByUsername(this.username) != undefined) {
                    return false
                } else {
                    return true
                }
            },

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
            getProviders() {
                let that = this;
                if (this.usernameAvaible) {
                    axios.post("/internal-api/socialAccounts", {
                        "username": this.username,
                        "password": this.password,
                        "confirm_password": this.confirmPassword,
                        "_token": this.csrf
                    })
                        .then(function (response) {
                            store.commit("setUsers", JSON.parse(response.request.response).data)
                            that.$router.push("/profile/" + that.currentuser.id);
                        })
                        .catch(function (error) {
                            console.log(error);
                        })
                }
                return false;

            },
            submitAction() {
                let that = this;
                if (this.usernameAvaible) {
                    axios.post("/internal-api/setlogin", {
                        "username": this.username,
                        "password": this.password,
                        "confirm_password": this.confirmPassword,
                        "_token": this.csrf
                    })
                        .then(function (response) {
                            store.commit("setUsers", JSON.parse(response.request.response).data)
                            that.$router.push("/profile/" + that.currentuser.id);
                        })
                        .catch(function (error) {
                            console.log(error);
                        })
                }
                return false;

            },
        },
        data() {
            return {
                providers: undefined,
            }
        }
    }
</script>
