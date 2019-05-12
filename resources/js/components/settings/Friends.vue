<template>
  <div v-if="currentuser.friends!=undefined">
    <h1>Open requests from others</h1>
    <div v-for="item in currentuser.friends.pendingRequests">
      <UserChip :item="toUser(item)"></UserChip>
      <v-btn @click="changeFriend('/internal-api/friends/acceptRequest', toUser(item).id)" >Accept request</v-btn>
      <v-btn @click="changeFriend('/internal-api/friends/denyRequest', toUser(item).id)" >Deny request</v-btn>
      <v-btn @click="changeFriend('/internal-api/friends/block', toUser(item).id)" >Block</v-btn>
    </div>
    <div v-if="currentuser.friends.pending.length>0">
    <h1>Open requests by myself</h1>
    <div v-for="item in currentuser.friends.pending">
      <UserChip :item="toUser(item)"></UserChip>
      <v-btn @click="changeFriend('/internal-api/friends/block', toUser(item).id)" >Block</v-btn>
      <v-btn @click="changeFriend('/internal-api/friends/unfriend', toUser(item).id)" >Unfriend</v-btn>
    </div>
  </div>
    <h1>Blocked by myself</h1>
    <div v-for="item in currentuser.friends.blocked">
      <UserChip :item="toUser(item)"></UserChip>
      <v-btn @click="changeFriend('/internal-api/friends/unblock', toUser(item).id)" >Unblock</v-btn>
    </div>
    <h1>Accepted by myself</h1>
    <div v-for="item in currentuser.friends.accepted">
      <UserChip :item="toUser(item)"></UserChip>
      <v-btn @click="changeFriend('/internal-api/friends/unfriend', toUser(item).id)" >Unfriend</v-btn>
    </div>
    <h1>Denied by myself</h1>
    <div v-for="item in currentuser.friends.denied">
      <UserChip :item="toUser(item)"></UserChip>
      <v-btn @click="changeFriend('/internal-api/friends/unblock', toUser(item).id)" >Unblock</v-btn>
    </div>
</div>
</template>
<script>
  import { store } from '../../store.js';
  import { eventBus } from '../../eventBus.js';
  import MarkdownCreator from '../MarkdownCreator'
  import UserChip from '../UserChip'
  const $ = require("jquery");
  const axios = require("axios");
  export default {
    props: ['baseUrl'],
    components: {
      MarkdownCreator,
      UserChip
    },
    mounted: function () {
    },
    updated: function () {
    },
    computed: {
      loggeduserid(){
        return store.state.loginId
      },
      csrf: function(){
        return store.getters.getCSRF()
      },
      currentuser: function(){
        var u = store.getters.getUserById(store.state.loginId)
        return u
      },
    },

    methods: {
      toUser(str) {
        return store.getters.getUserByUsername(str);
      },
      changeFriend(url, uid){
        axios.post(url,{fid:uid,_token:this.csrf})  
        .then(function (response) {
          store.commit("setUsers",JSON.parse(response.request.response).data)
       })
       .catch(function (error) {
         console.log(error);
       })
      },
    },
    data(){
      return {
      }
    }
  }
</script>
