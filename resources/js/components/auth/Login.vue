<template>
    <v-card class="mx-auto">
       <v-card-title>{{ $t('Login') }}</v-card-title>

            <v-card-text>
                <v-form id="loginForm" lazy-validation ref="form" v-model="valid">
                    <input :value="csrf" name="_token" type="hidden">
                    <input name="ajaxLogin" type="hidden" value="1">
                    <v-text-field label="E-mail or username" name="email"
                                  required v-model="email"
                                  v-on:keyup.enter="submitLogin()"></v-text-field>
                    <v-text-field
                        :append-icon="showPassword ? 'visibility_off' : 'visibility'"
                        :label="$t('Password')"
                        :rules="[rules.required, rules.min]"
                        :type="showPassword ? 'text' : 'password'"
                        @click:append="showPassword = !showPassword"
                        counter
                        :hint='$t("Your password is long enough")'
                        name="password"
                        v-model="password"
                        v-on:keyup.enter="submitLogin()"
                    ></v-text-field>
                    <v-checkbox
                        :label='$t("Remember me")'
                        name="remember"
                        required
                        v-model="rememberMe"
                    ></v-checkbox>
                </v-form>
            </v-card-text>
            <v-card-actions>
                <v-btn @click="submitLogin()">
                    {{ $t('Login') }}
                </v-btn>
                <v-btn href="/password/reset">
                    Forgot Your Password?
                </v-btn>
                <a href="/oauth/google" v-if="mixconfig.MIX_GOOGLE_AUTH_ENABLED=='1'">
                    <img src="/img/loginwith/google/btn_google_signin_dark_normal_web.png"/>
                </a>
                <v-btn href="/oauth/github" v-if="externalLoginEnabled['google']">
                    {{ $t('Login') }} {{ $t('with') }} Github
                </v-btn>
                <v-btn href="/oauth/gitlab" v-if="externalLoginEnabled['gitlab']">
                    {{ $t('Login') }} {{ $t('with') }} Google
                </v-btn>
                <v-btn href="/oauth/facebook" v-if="externalLoginEnabled['facebook']">
                    {{ $t('Login') }} {{ $t('with') }} Facebook
                </v-btn>
                <v-btn href="/oauth/twitter" v-if="externalLoginEnabled['twitter']">
                    {{ $t('Login') }} {{ $t('with') }} Twitter
                </v-btn>
                <v-btn href="/oauth/bitbucket" v-if="externalLoginEnabled['bitbucket']">
                    {{ $t('Login') }} {{ $t('with') }} Bitbucket
                </v-btn>
                <v-btn href="/oauth/linkedin" v-if="externalLoginEnabled['linkedin']">
                    {{ $t('Login') }} {{ $t('with') }} Linkedin
                </v-btn>
            </v-card-actions>
        </v-card>
</template>


<script>
    const $ = require("jquery");
    import Vue from 'vue'
    import {store} from '../../store.js';
    import {eventBus} from '../../eventBus.js';

    export default {
        props: ['baseUrl', 'mixconfig'],
        data() {
            return {
                valid: true,
                email: '',
                password: '',
                externalLoginEnabled:{'google':false,'twitter':false,'github':false,'facebook':false,'gitlab':false,'bitbucket':false,'linkedin':false},
                showPassword: false,
                rememberMe: false,
                minChars: 8,
                rules: {
                    required: value => !!value || 'Required.',
                    min: v => v.length >= parseInt(this.minChars) || 'Min '+this.minChars+' characters',
                    emailMatch: () => ('The email and password you entered don\'t match')
                }
            }
        },
        mounted() {
            if (this.loggeduserid != 0) {
                this.$router.push("/");
            }
            if(process.env.MIX_MIN_PASSWORDLENGTH != undefined){
                this.minChars = process.env.MIX_MIN_PASSWORDLENGTH;
            }

            if(process.env.MIX_GOOGLE_AUTH_ENABLED == '1'){
                Vue.set(externalLoginEnabled, 'google', true)
            }
            if(process.env.MIX_GITHUB_AUTH_ENABLED == '1'){
                Vue.set(externalLoginEnabled, 'github', true)
            }
            if(process.env.MIX_GITLAB_AUTH_ENABLED == '1'){
                Vue.set(externalLoginEnabled, 'gitlab', true)
            }
            if(process.env.MIX_FACEBOOK_AUTH_ENABLED == '1'){
                Vue.set(externalLoginEnabled, 'facebook', true)
            }
            if(process.env.MIX_BITBUCKET_AUTH_ENABLED == '1'){
                Vue.set(externalLoginEnabled, 'bitbucket', true)
            }
            if(process.env.MIX_LINKEDIN_AUTH_ENABLED == '1'){
                Vue.set(externalLoginEnabled, 'linkedin', true)
            }
            if(process.env.MIX_TWITTER_AUTH_ENABLED == '1'){
                Vue.set(externalLoginEnabled, 'twitter', true)
            }
        },
        computed: {
            csrf: function () {
                return store.getters.getCSRF()
            },
            loggeduserid() {
                // console.log(this.mixconfig.MIX_MIN_PASSWORDLENGTH)
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
                        if (res.status == 200 || res.status == 302) {
                            console.log("received login", res)
                            if (res.responseText == "{\"twofactor\":true}") {
                                that.$router.push('/twofaLogin');
                            } else {
                                if (res.responseJSON.data.jwt_token !== undefined) {
                                    eventBus.$emit('login', res.responseJSON.data);
                                    localStorage.setItem('jwt_token', res.responseJSON.data.jwt_token)
                                }
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
