<template>
  <div>
    <div class="" id="">
      <button class="btn btn-sm btn-primary" @click="emitMarkNotifications('/internal-api/notifications/markasread')">Mark all as read</button>
      <button class="btn btn-sm btn-primary" @click="emitMarkNotifications('/internal-api/notifications')">Refresh notifications</button>
      <button class="btn btn-sm btn-danger" @click="emitMarkNotifications('/internal-api/notifications/delete')">Delete all notifications</button>

      <h4>{{ $t("Notifications") }}</h4>
      <div v-for="item in notifications"  class="text-center">
        <div v-if="item.type==='App\\Notifications\\GenericNotification'">
          <v-divider>{{ item.created_at }}</v-divider>



        </div>
        
    
</div>
    </div>
  </div>
</template>
<script>
import {  store } from '../store.js';
  import { eventBus } from '../eventBus.js';
  export default {
    props: ['baseUrl','canloadmore'],
    computed:{
      notifications: function(){
        return store.state.notifications
      },
      users: function(){
        return store.state.users
      },
      loggeduserid: function(){
        return store.state.loginId
      },
      fullmedias: function(){
        return store.state.medias
      },
    },
    methods: {
      getLikeString(nr) {
        if(nr==-1){
          return "disliked"
        }
        if(nr==0){
          return "reseted"
        }
        if(nr==1){
          return "liked"
        }
      },
      emitMarkNotifications(url) {
        eventBus.$emit('getNotifications',url);
      },
      getMediaById2(id){
        console.log("mediabyid "+id)
        if(id==null||id==0){
          return;
        }
        var theMedia = undefined;
        this.fullmedias.forEach(function(val,key){
          if(val.id==id){
            theMedia = val;
          }
        });
        if(theMedia==undefined){
          eventBus.$emit('loadMediaById',id);
        }
        return theMedia
      },
      getCommentById2(id){
        if(id==null||id==0){
          return;
        }
        var theMedia = undefined;
        this.fullmedias.forEach(function(val,key){
          val.comments.forEach(function(val2,key2){
          if(val2.id==id){
            theMedia = val2;
          }
          });
        });
        if(theMedia==undefined){
          eventBus.$emit('loadMediaByCommentId',id);
        }
        return theMedia
      },
      getUserById(id){
        var theMedia = undefined;
        this.users.forEach(function(val,key){
          if(val.id==id){
            theMedia = val;
          }
        });
        return theMedia
      }
    },
    components : {
    }
  }
</script>
