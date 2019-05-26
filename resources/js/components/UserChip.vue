<template>
<div>
  <v-chip color="primary" text-color="white">
    <router-link :to="'/profile/'+item.id"><v-avatar class="green darken-4" v-if="item.avatar!=''"><img :src="item.avatar" alt="test" /></v-avatar>
    {{ item.name }} ({{ item.username }})</router-link>
    <a  :title="$t('Infobox')"><v-icon @click="dialog=true" right>info</v-icon></a>
  </v-chip>
  
  <v-dialog v-model="dialog">
  <v-card >
    
  <v-card-text class="text-center" :style="'background-image:url('+item.background+'); background-position: center; background-size: cover;'">
    <div> <img class="text-center" v-if="item.avatar!=''" :src='item.avatar' /></div>
    <v-chip class="text-center" disabled color="blue" text-color="white"><v-subheader dark>{{ item.name }} ({{ item.username }})</v-subheader></v-chip>
  </v-card-text>

    <v-card-text>
      <div v-if="loginId!=item.id">
      <div v-if="friendstatus==0">No friend-thing yet 
        <v-btn @click="changeFriend('/internal-api/friends/friendRequest', item.id)" >Send friendrequest</v-btn>
        <v-btn @click="changeFriend('/internal-api/friends/block', item.id)" >Block</v-btn>
      </div>
      <div v-if="friendstatus==1">Accepted friend
      <v-btn @click="changeFriend('/internal-api/friends/block', item.id)" >Block</v-btn>
      </div>
      <div v-if="friendstatus==2">Your request is pending
      <v-btn @click="changeFriend('/internal-api/friends/block', item.id)" >Block</v-btn>
      <v-btn @click="changeFriend('/internal-api/friends/unfriend', item.id)" >Unfriend</v-btn>
      </div>
      <div v-if="friendstatus==3">Denied friend
      <v-btn @click="changeFriend('/internal-api/friends/acceptRequest', item.id)" >Accept request</v-btn>
      </div>
      <div v-if="friendstatus==4">Blocked friend
      <v-btn @click="changeFriend('/internal-api/friends/unblock', item.id)" >Unblock</v-btn></div>
      <div v-if="friendstatus==5">Pending request friend
        <v-btn @click="changeFriend('/internal-api/friends/acceptRequest', item.id)" >Accept request</v-btn>
      </div>
    </div>
      <VueMarkdown :source="item.bio"></VueMarkdown>
    </v-card-text>

    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn
        color="green darken-1"
        flat="flat"
        @click="dialog=false"
      >
        {{ $t('Go') }} {{ $t('back') }}
      </v-btn>


      <v-btn
        color="green darken-1"
        flat="flat"
        :to="'/profile/'+item.id"
      >
        {{ $t('Visit') }} {{ $t('profile') }}
      </v-btn>
    </v-card-actions>
  </v-card>
</v-dialog>
</div>
</template>

<script>
import { store } from '../store.js';
const axios = require("axios");
import VueMarkdown from 'vue-markdown'
  export default {
    props: ['item'],
    components : {
        VueMarkdown,
    },
    methods : {
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
    computed : {
      friendstatus: function(){
        var u = store.getters.getUserById(store.state.loginId)
        var pu = store.getters.getUserById(this.item.id)
        if(u.friends!=undefined&&u.id!=0){
          if(u.friends.accepted.indexOf(pu.username)>-1){
            return 1;
          } else if(u.friends.pending.indexOf(pu.username)>-1){
            return 2;
          } else if(u.friends.denied.indexOf(pu.username)>-1){
            return 3;
          } else if(u.friends.blocked.indexOf(pu.username)>-1){
            return 4;
          } else if(u.friends.pendingRequests.indexOf(pu.username)>-1){
            return 5;
          }
        }
        return 0;
      },
      loginId:function(){
        return store.state.loginId;
      }
    },
    data:()=>({
      dialog:false,
    })
  }
  </script>