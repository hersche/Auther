<template>
    <div>
        <v-toolbar color="primary" dark fixed style="z-index:99999">
            <v-toolbar-side-icon @click="active=true"></v-toolbar-side-icon>
            <router-link class="" to="/">
                <v-toolbar-title class="white--text" to="/">{{ mixconfig.MIX_APP_NAME }}</v-toolbar-title>
            </router-link>
            <v-spacer></v-spacer>
            <v-flex align-right lg3 md3 sm4 xs5>
                <v-text-field
                    :placeholder="$t('Search')+'...'"
                    @focus="searching()"
                    @keyup="searching()"
                    append-icon="search"
                    hide-details
                    id="theLiveSearch"
                    single-line v-model="search"
                ></v-text-field>
            </v-flex>
        </v-toolbar>


        <v-navigation-drawer
            dark
            fixed
            style="z-index:99999; height: 100%;overflow-x:auto; max-width:90%;"
            temporary
            v-model="active"
        >
            <v-list-tile>
                <v-list-tile-action>
                    <v-btn :icon="true" @click="active=false" color="orange" style="cursor:pointer;">
                        <v-icon>close</v-icon>
                    </v-btn>
                </v-list-tile-action>
            </v-list-tile>
            <v-list-tile>
                <v-list-tile-action>
                    <v-icon>language</v-icon>
                </v-list-tile-action>
                <v-select
                    :items="[{value:'en',text:'English'},{value:'de',text:'Deutsch'}]"
                    :label="$t('Language')"
                    attach
                    chips
                    v-model="lang"
                ></v-select>
                <!--    <select id="langSelect" class="float-right custom-select custom-select-sm ml-1" v-model="lang" >
                      <option value="en">EN</option>
                      <option value="de">DE</option>
                    </select>-->
            </v-list-tile>
            <v-list class="pa-1">
                <v-list-tile :style="'background-image:url('+currentuser.background+');'" avatar tag="div"
                             v-if="currentuser.id!=0">
                    <v-badge color="orange" left overlap>
                        <router-link class="small" slot="badge" to="/notifications">{{ n }}</router-link>
                        <router-link :to="'/profile/'+currentuser.id">
                            <v-list-tile-avatar>
                                <img :src="currentuser.avatar">
                            </v-list-tile-avatar>
                        </router-link>
                    </v-badge>
                    <v-list-tile-content>
                        <v-list-tile-title>{{ currentuser.name }}</v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>

            <v-list class="pt-0" dense>
                <v-list-tile to="/">
                    <v-list-tile-action>
                        <v-icon>home</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-content>
                        <v-list-tile-title>{{ $t('Home') }}</v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>


                <v-list-group
                    no-action
                    prepend-icon="build"
                >
                    <v-list-tile slot="activator">
                        <v-list-tile-title>Debug</v-list-tile-title>
                    </v-list-tile>
                    <v-list-group
                        no-action
                        prepend-icon="warning"
                        sub-group
                        v-if="currentuser.id!=0"
                    >
                        <v-list-tile slot="activator">
                            <v-list-tile-title>Oauth</v-list-tile-title>
                        </v-list-tile>
                        <v-list-tile to="/passport/clients">
                            <v-list-tile-action>
                                <v-icon>account_box</v-icon>
                            </v-list-tile-action>
                            <v-list-tile-content>
                                <v-list-tile-title>{{ $t('Clients') }}</v-list-tile-title>
                            </v-list-tile-content>
                        </v-list-tile>

                        <v-list-tile to="/passport/personalaccess">
                            <v-list-tile-action>
                                <v-icon>account_box</v-icon>
                            </v-list-tile-action>
                            <v-list-tile-content>
                                <v-list-tile-title>{{ $t('Personal access tokens') }}</v-list-tile-title>
                            </v-list-tile-content>
                        </v-list-tile>
                    </v-list-group>
                    <v-list-tile>
                        <v-list-tile-action>
                            <v-icon>multiline_chart</v-icon>
                        </v-list-tile-action>
                    </v-list-tile>

                    <v-list-tile>
                        <v-list-tile-action>
                            <v-icon>account_box</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>{{ $t('Users') }}: {{ users.length }}</v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </v-list-group>
                <v-list-tile to="/login" v-if="currentuser.id==0">
                    <v-list-tile-action>
                        <v-icon>exit_to_app</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-content>
                        <v-list-tile-title>{{ $t('Login') }}</v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>

                <v-list-tile to="/register" v-if="currentuser.id==0">
                    <v-list-tile-action>
                        <v-icon>person_add</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-content>
                        <v-list-tile-title>{{ $t('Register') }}</v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>

                <v-list-group
                    no-action
                    prepend-icon="settings"
                    v-if="currentuser.admin"
                >
                    <v-list-tile slot="activator">

                        <v-list-tile-title>Admin</v-list-tile-title>

                    </v-list-tile>
                    <v-list-tile to="/admin/users">
                        <v-list-tile-action>
                            <v-icon>supervised_user_circle</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>Users</v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>

                    <v-list-tile to="/admin/roles">
                        <v-list-tile-action>
                            <v-icon>reorder</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>Roles</v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>

                    <v-list-tile to="/admin/projects">
                        <v-list-tile-action>
                            <v-icon>whatshot</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>Projects</v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </v-list-group>


                <v-list-group
                    no-action
                    prepend-icon="settings"
                    v-if="currentuser.id!=0"
                >
                    <v-list-tile slot="activator">

                        <v-list-tile-title>{{ $t('Settings') }}</v-list-tile-title>

                    </v-list-tile>


                    <v-list-tile to="/settings/profile">
                        <v-list-tile-action>
                            <v-icon>account_circle</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>{{ $t('Profile') }}</v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>

                    <v-list-tile to="/settings/editusername" v-if="currentuser.allow_username_change">
                        <v-list-tile-action>
                            <v-icon>account_circle</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>{{ $t('Edit login') }}</v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>

                    <v-list-tile to="/settings/friends">
                        <v-list-tile-action>
                            <v-icon>accessibility</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>{{ $t('Friends') }}</v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>

                    <v-list-tile to="/settings/apps">
                        <v-list-tile-action>
                            <v-icon>apps</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>{{ $t('Manage apps') }}</v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>


                    <v-list-tile to="/settings/2fa">
                        <v-list-tile-action>
                            <v-icon>security</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>2factor-Auth</v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>

                    <v-list-tile to="/settings/password">
                        <v-list-tile-action>
                            <v-icon>lock</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>{{ $t('Password') }} {{ $t('and') }} {{ $t('Email') }}
                            </v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </v-list-group>


                <v-list-tile @click="emitLogout()" v-if="currentuser.id!=0">
                    <v-list-tile-action>
                        <v-icon>power_settings_new</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-content>
                        <v-list-tile-title>{{ $t('Logout') }}</v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
                <v-list-tile to="/about">
                    <v-list-tile-action>
                        <v-icon>help</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-content>
                        <v-list-tile-title>{{ $t('About') }}</v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>

            </v-list>
        </v-navigation-drawer>
        <form class="d-none" id="logoutForm">
            <input name="ajaxLogin" type="hidden" value="1"/>
            <input :value="csrf" name="_token" type="hidden">
        </form>
        <v-snackbar
            :bottom="true"
            :color="alertcolor"
            :multi-line="true"
            :timeout="9000"
            v-model="alarmEnabledInternal"
        >
            {{ alerttext }}
            <v-btn

                @click="closeAlarm()"
                flat
            >
                {{ $t('Close') }}
            </v-btn>
        </v-snackbar>
    </div>
</template>

<script>
    import {store} from '../store.js';
    import {eventBus} from '../eventBus.js';
    const axios = require("axios");
    export default {
        mounted() {
            if (localStorage.getItem("language") != '' && localStorage.getItem("language") != undefined) {
                this.lang = localStorage.getItem("language");
            }
            if (this.$route.params.term != undefined && this.$route.params.term != '') {
                this.search = this.$route.params.term;
            }

        },
        methods: {
            closeAlarm() {
                eventBus.$emit('closeAlarm', "");
            },
            searching() {
                if (this.search != '') {
                    if (this.$route.fullPath.indexOf("search") == -1) {
                        this.urlBeforeSearch = this.$route.fullPath;
                    }
                    this.$router.push("/search/" + this.search)
                } else {
                    if (this.$route.fullPath.indexOf("search") != -1) {
                        this.$router.push(this.urlBeforeSearch)
                    }
                }
                //  this.$route.params.profileId
                //  eventBus.$emit('refreshSearch',"");
            },
            emitLogout: function () {
                var formData = new FormData(document.getElementById('logoutForm'));
                axios({
                    method: 'post',
                    url: '/logout',
                    data: formData,
                    headers: {'Content-Type': 'multipart/form-data' }
                }).then(function (response) {
                        eventBus.$emit('logout', "");
                        localStorage.setItem('jwt_token', '')
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
            },
        },
        props: ['alertshown', 'alerttext', 'alertcolor', 'mixconfig'],
        computed: {
            csrf: function () {
                return store.getters.getCSRF()
            },
            notifications: function () {
                return store.state.notifications
            },
            currentuser() {
                //return undefined
                return store.getters.getUserById(store.state.loginId)
            },
            users: function () {
                return store.state.users
            }
        },
        watch: {
            alarmEnabledInternal: function (val) {
                if (val == false) {
                    eventBus.$emit('closeAlarm', "");
                }
            },
            alertshown: function (val) {
                console.log("react to alertshown", val)
                this.alarmEnabledInternal = val
            },
            lang: function (val) {
                localStorage.setItem("language", val);
                eventBus.$emit('languageChange', val);
            },
            notifications: function (val) {
                var tmpArray = []
                this.notifications.forEach(function (val, key) {
                    if (val.read_at == null || val.read_at == 0 || val.read_at == undefined) {
                        tmpArray.push(val)
                    }
                });
                this.n = tmpArray.length;
            }
        },
        data: () => ({
            active: false,
            activeItem: 0,
            lang: 'en',
            n: 0,
            urlBeforeSearch: '/',
            search: '',
            mini: false,
            alarmEnabledInternal: false,
            speedDeal: false,
            treeTypes: [{id: 'audio', label: 'Audio'}, {id: 'video', label: 'Video'}]


        })
    }
</script>

<style lang="stylus">
.vue-treeselect__multi-value
  display inline-flex
  overflow hidden
vs-navbar >>> .vue-treeselect__menu
  width 100px
  padding-left 15px
  padding-bottom 15px
  z-index 999999
.header-sidebar
  display flex
  align-items center
  justify-content center
  flex-direction column
  width 100%
  h4
    display flex
    align-items center
    justify-content center
    width 100%
    > button
      margin-left 10px
.footer-sidebar
  display flex
  align-items center
  justify-content space-between
  width 100%
  > button
      border 0px solid rgba(0,0,0,0) !important
      border-left 1px solid rgba(0,0,0,.07) !important
      border-radius 0px !important
</style>
