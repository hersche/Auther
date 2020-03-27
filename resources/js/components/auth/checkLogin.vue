<template>
    <div class="">
        <h1 class="text-center">{{ $t('Login') }}</h1>
        <p>This profile already exists. Please enter your login-details once to enable the provider</p>
        <v-form id="loginForm" lazy-validation ref="form" v-model="valid">
            <input :value="csrf" name="_token" type="hidden">
            <input name="ajaxLogin" type="hidden" value="1">
            <v-text-field label="E-mail or username" name="email" required v-model="email"
                          v-on:keyup.enter="submitLogin()"></v-text-field>
            <v-text-field
                :append-icon="showPassword ? 'visibility_off' : 'visibility'"
                :label="$t('Password')"
                :rules="[rules.required, rules.min]"
                :type="showPassword ? 'text' : 'password'"
                @click:append="showPassword = !showPassword"
                counter
                hint="At least 8 characters"
                name="password"
                v-model="password"
                v-on:keyup.enter="submitLogin()"
            ></v-text-field>
        </v-form>
        <v-btn @click="submitLogin()">{{ $t('Login') }}</v-btn>
    </div>
</template>


<script>
    const $ = require("jquery");
    import {store} from '../../store.js';
    import {eventBus} from '../../eventBus.js';

    export default {
        props: ['baseUrl', 'mixconfig'],
        data() {
            return {
                valid: true,
                email: '',
                password: '',
                showPassword: false,
                rememberMe: false,
                rules: {
                    required: value => !!value || 'Required.',
                    min: v => v.length >= 8 || 'Min 8 characters',
                    emailMatch: () => ('The email and password you entered don\'t match')
                }
            }
        },
        mounted() {
            if (this.loggeduserid != 0) {
                this.$router.push("/");
            }
        },
        computed: {
            csrf: function () {
                return store.getters.getCSRF()
            },
            loggeduserid() {
                return store.state.loginId
            },
        },
        methods: {
            submitLogin() {
                let that = this;
                $.ajax({
                    url: '/login',
                    type: 'POST',
                    data: new FormData($("#loginForm")[0]),
                    cache: false,
                    contentType: false,
                    processData: false,
                    complete: function (res) {
                        if (res.status == 200) {
                            console.log("received login", res)
                            if (res.responseText == "{\"twofactor\":true}") {
                                that.$router.push('/twofaLogin');
                            } else {
                                eventBus.$emit('login', res.responseJSON.data);
                            }

                        } else if (res.status == 401) {
                            eventBus.$emit('alert', "Login failed");
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
