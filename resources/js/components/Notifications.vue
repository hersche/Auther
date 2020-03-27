<template>
    <div>
        <div class="" id="">
            <button @click="emitMarkNotifications('/internal-api/notifications/markasread')"
                    class="btn btn-sm btn-primary">Mark all as read
            </button>
            <button @click="emitMarkNotifications('/internal-api/notifications')" class="btn btn-sm btn-primary">Refresh
                notifications
            </button>
            <button @click="emitMarkNotifications('/internal-api/notifications/delete')" class="btn btn-sm btn-danger">
                Delete all notifications
            </button>

            <h4>{{ $t("Notifications") }}</h4>
            <div class="text-center" v-for="item in notifications">
                <div v-if="item.type==='App\\Notifications\\GenericNotification'">
                    <p>{{ item.created_at }}</p>
                    <p v-if="item.read_at==undefined">Unread</p>
                    <p>{{ item.data.appname }}</p>
                    <p>{{ item.data.msg }}</p>
                    <p>{{ item.data.link }}</p>
                    <v-divider></v-divider>

                </div>


            </div>
        </div>
    </div>
</template>
<script>
    import {store} from '../store.js';
    import {eventBus} from '../eventBus.js';

    const $ = require("jquery");
    export default {
        props: ['baseUrl', 'canloadmore'],
        computed: {
            notifications: function () {
                return store.state.notifications
            },
            users: function () {
                return store.state.users
            },
            loggeduserid: function () {
                return store.state.loginId
            },
            fullmedias: function () {
                return store.state.medias
            },
        },
        methods: {
            getLikeString(nr) {
                if (nr == -1) {
                    return "disliked"
                }
                if (nr == 0) {
                    return "reseted"
                }
                if (nr == 1) {
                    return "liked"
                }
            },
            emitMarkNotifications(url) {
                // TODO handle actions, but in a better way than in laratube
                let that = this;
                if (store.state.loginId != 0) {
                    $.getJSON(url, function name(data) {
                        store.commit("setNotifications", data)
                    });
                }


                //eventBus.$emit('getNotifications',url);
            },
            getMediaById2(id) {
                console.log("mediabyid " + id)
                if (id == null || id == 0) {
                    return;
                }
                var theMedia = undefined;
                this.fullmedias.forEach(function (val, key) {
                    if (val.id == id) {
                        theMedia = val;
                    }
                });
                if (theMedia == undefined) {
                    eventBus.$emit('loadMediaById', id);
                }
                return theMedia
            },
            getCommentById2(id) {
                if (id == null || id == 0) {
                    return;
                }
                var theMedia = undefined;
                this.fullmedias.forEach(function (val, key) {
                    val.comments.forEach(function (val2, key2) {
                        if (val2.id == id) {
                            theMedia = val2;
                        }
                    });
                });
                if (theMedia == undefined) {
                    eventBus.$emit('loadMediaByCommentId', id);
                }
                return theMedia
            },
            getUserById(id) {
                var theMedia = undefined;
                this.users.forEach(function (val, key) {
                    if (val.id == id) {
                        theMedia = val;
                    }
                });
                return theMedia
            }
        },
        components: {}
    }
</script>
