<template>
    <div class="container text-center">
      <h1>{{ $t('Search') }}</h1>
      <p>We search in the titles and names, as well as descriptions and biographies</p>
      <div class="text-center" v-if="projectSearchResult.length>0">
        <h2>Projects</h2>
      <div v-for="item in projectSearchResult">
        <ProjectChip v-bind:item="item"></ProjectChip>
      </div>
    </div>
      
      <div class="text-center" v-if="userSearchResult.length>0">
        <h2>Users</h2>
      <div v-for="item in userSearchResult">
        <router-link :to="'/profile/'+item.id">
          <v-chip>
            <v-avatar>
              <img :src="item.avatar" :alt="item.username">
            </v-avatar>
            {{ item.name }} ({{ item.username }})
          </v-chip>
        </router-link>
      </div>
    </div>
    
    <div class="text-center" v-if="projectSearchResult.length==0&userSearchResult.length==0">
      <h2>There are no projects or users like {{ $route.params.term }}</h2>
    </div>
    
    </div>
</template>

<script>
import { eventBus } from '../eventBus.js';
import { store } from '../store.js';
import ProjectChip from './ProjectChip'
    export default {      
      created () {
        
        // this.getContent(this.$route.params.uid);
      },
      components: {
        ProjectChip
      },
      computed: {
        csrf: function(){
          return store.getters.getCSRF()
        },
        userSearchResult(){
          return store.getters.getUsersBySearch(this.$route.params.term);
          //return store.getters.getUserById(store.state.loginId)
        },
        projectSearchResult(){
          return store.getters.getProjectsBySearch(this.$route.params.term);
        },
        loggeduserid(){
          return store.state.loginId
        },
        projects:function(){
          return store.state.projects
        },
      },
        mounted() {
          
            console.log('Component mounted.')
        },
        data(){
          return {
            //searchResult: [],
          }
        }
    }
</script>
