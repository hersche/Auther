<style scoped>
    .action-link {
        cursor: pointer;
    }
</style>

<template>
  <div>
    <div v-if="tokens.length > 0">
      <h3 class="text-center">{{ $t('Manage') }} {{ $t('apps')}}</h3>
      <v-data-table
        :headers="headers"
        :items="tokens"
        class="elevation-1"
        >
        <template v-slot:items="props">
          <td class="" v-if="theInfos[props.item.client.id]!=undefined"><ProjectChip v-bind:item="theInfos[props.item.client.id]"></ProjectChip></td>
          <td class="" v-if="theInfos[props.item.client.id]==undefined"> {{ props.item.client.name }}</td>
          <td class="">{{ props.item.scopes.join(', ') }}</td>
          <td class="">
            <v-btn icon @click="revoke(props.item)">
              <v-icon>delete</v-icon>
            </v-btn>
          </td>
        </template>
      </v-data-table>
    </div>
  </div>
</template>

<script>
  import { store } from '../../store.js';
  const axios = require('axios')
  import ProjectChip from '../ProjectChip'
  export default {
    data() {
      return {
        tokens: [],
        theInfos:[],
        headers: [
          { text: 'Name', value: 'client.name' },
          { text: 'Scopes', value: 'scopes' },
          { text: '', value: '' },
        ],
      };
    },
    watch: {
      projects: function (val) {
        this.theInfos = []
        let that = this
        this.tokens.forEach(function(item){
          that.theInfos[item.client.id] = that.projectByClientId(item.client.id)
        })
      },
      tokens: function (val) {
        this.theInfos = []
        let that = this
        this.tokens.forEach(function(item){
          that.theInfos[item.client.id] = that.projectByClientId(item.client.id)
        })
      }
    },
    computed: {
      projects(){
        return store.state.projects
      },
    },
    components: {
      ProjectChip
    },
    mounted(){
      this.getTokens();
    },
    methods: {
        projectByClientId(id) {
          return store.getters.getProjectByClientId(id)
        },
        /**
        * Get all of the authorized tokens for the user.
        */
        getTokens() {
          axios.get('/oauth/tokens')
            .then(response => {
              this.tokens = response.data;
            });
          },
          /**
          * Revoke the given token.
          */
          revoke(token) {
            axios.delete('/oauth/tokens/' + token.id)
              .then(response => {
                this.getTokens();
              });
          }
        }
    }
</script>
