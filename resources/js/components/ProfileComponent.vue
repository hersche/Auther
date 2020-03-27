<template>

    <div v-if="profileUser!=undefined">

        <div id="profile">
            <v-btn @click="$router.go(-1)" class="">{{ $t("Back") }}</v-btn>
            <div
                :style="'background-image:url('+profileUser.background+'); background-position: center; background-size: cover; text-align: center;'"
                id="profileheader">
                <img :src='profileUser.avatar' class='justify-center'/>
                <div class="justify-center">
                    <v-chip color="blue" disabled text-color="white">
                        <v-subheader dark>{{ profileUser.name }}</v-subheader>
                    </v-chip>
                </div>
                <v-chip class="text-center" color="lightblue" disabled small text-color="black">
                    <v-subheader>({{ profileUser.username }})</v-subheader>
                </v-chip>
                <v-chip class="text-center" color="red" disabled small text-color="white"
                        v-if="currentuser.id==profileUser.id">(You)
                </v-chip>
            </div>
            <div>

            </div>
            <VueMarkdown :source="profileUser.bio"></VueMarkdown>
            <div v-if="currentuser.id!=profileUser.id">
                <div v-if="friendstatus==0">No friend-thing yet
                    <v-btn @click="changeFriend('/internal-api/friends/friendRequest', profileUser.id)">Send
                        friendrequest
                    </v-btn>
                    <v-btn @click="changeFriend('/internal-api/friends/block', profileUser.id)">Block</v-btn>
                </div>
                <div v-if="friendstatus==1">Accepted friend
                    <v-btn @click="changeFriend('/internal-api/friends/block', profileUser.id)">Block</v-btn>
                </div>
                <div v-if="friendstatus==2">Your request is pending
                    <v-btn @click="changeFriend('/internal-api/friends/block', profileUser.id)">Block</v-btn>
                    <v-btn @click="changeFriend('/internal-api/friends/unfriend', profileUser.id)">Unfriend</v-btn>
                </div>
                <div v-if="friendstatus==3">Denied friend
                    <v-btn @click="changeFriend('/internal-api/friends/acceptRequest', profileUser.id)">Accept request
                    </v-btn>
                </div>
                <div v-if="friendstatus==4">Blocked friend
                    <v-btn @click="changeFriend('/internal-api/friends/unblock', profileUser.id)">Unblock</v-btn>
                </div>
                <div v-if="friendstatus==5">Pending request friend
                    <v-btn @click="changeFriend('/internal-api/friends/acceptRequest', profileUser.id)">Accept request
                    </v-btn>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    const axios = require('axios');
    import {store} from '../store.js';
    import VueMarkdown from 'vue-markdown'

    export default {
        props: ['baseUrl'],
        components: {
            VueMarkdown,
        },
        mounted() {
            //eventBus.$emit("loadUserVideos",this.profileUser.id)
        },
        computed: {
            csrf: function () {
                return store.getters.getCSRF()
            },
            profileUser: function () {
                var u = store.getters.getUserById(this.$route.params.profileId)
                if (u != undefined) {

                }
                return u
            },
            currentuser: function () {
                var u = store.getters.getUserById(store.state.loginId)
                return u
            },

            friendstatus: function () {
                var u = store.getters.getUserById(store.state.loginId)
                var pu = store.getters.getUserById(this.$route.params.profileId)
                if (u.friends != undefined && u.id != 0) {
                    if (u.friends.accepted.indexOf(pu.username) > -1) {
                        return 1;
                    } else if (u.friends.pending.indexOf(pu.username) > -1) {
                        return 2;
                    } else if (u.friends.denied.indexOf(pu.username) > -1) {
                        return 3;
                    } else if (u.friends.blocked.indexOf(pu.username) > -1) {
                        return 4;
                    } else if (u.friends.pendingRequests.indexOf(pu.username) > -1) {
                        return 5;
                    }
                }
                return 0;
            },
            // a computed getter
        },
        methods: {
            changeFriend(url, uid) {
                console.log("change friend fired??")
                axios.post(url, {fid: uid, _token: this.csrf})
                    .then(function (response) {
                        store.commit("setUsers", JSON.parse(response.request.response).data)
                        //   console.log(JSON.parse(response.request.response).data);
                        //   return response.data
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
            },
        }
    }
</script>
