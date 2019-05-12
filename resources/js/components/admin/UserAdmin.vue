<template>
  <div>
    <v-text-field
  v-model="search"
  append-icon="search"
  label="Search"
  single-line
  hide-details
></v-text-field>
<div :id="'roleSelectElement'"></div>
    <v-data-table
    :items="users"
    :search="search"
    :headers="[
      { text: $t('Name'), value: 'name' },
      { text: $t('Username'), value: 'username' },
      { text: $t('Email'), value: 'email' },
      { text: $t('Roles'), value: 'roles' },
      { text: 'Created at', value: 'created_at' },
      ]"
      :expand="true"
      >
        <template slot="items" slot-scope="props">
          <tr @click="props.expanded = !props.expanded">
            <td class="text-xs-right">{{ props.item.name }}</td>
            <td class="text-xs-right">{{ props.item.username }}</td>
            <td class="text-xs-right">{{ props.item.email }}</td>
            <td class="text-xs-right">{{ props.item.roles }}</td>
            <td class="text-xs-right">{{ props.item.created_at }}</td>
          </tr>
        </template>
        <template slot="expand" slot-scope="props">
          <v-card flat>
            <v-card-title primary-title>
              {{ props.item.name }}
            </v-card-title>
            <v-card-text>
              <form :id="'urolesform'+props.item.id">
                <v-select
                :items="selectRoles"
                v-model="selectedRoles[props.item.id]"
                deletable-chips
                :attach="'#roleSelectElement'"
                :label="$t('Roles')"
                multiple
                ></v-select>
                <input type="hidden" :value="csrf" name="_token">
                <input type="hidden" :value="props.item.id" name="uid">
              </form>
              <v-btn @click="changeRoles(props.item.id)" small color="green">
                <v-icon>edit</v-icon>Apply roles
              </v-btn>
              <v-btn small :to="'/profile/'+props.item.id" color="green"><v-icon>send</v-icon> Go to profile</v-btn>
              <v-btn  @click="openConfirm(props.item.id)" small color="red"><v-icon>delete_sweep</v-icon> Delete</v-btn>
            </v-card-text>
            <v-card-actions>


            </v-card-actions>
          </v-card>
        </template>
      </v-data-table>
        <form id="hiddenCSRFForm" class="d-none">
          <input type="hidden" name="_token" :value="csrf">
        </form>
        <v-dialog
  v-model="deleteDialog"
  width="500"
>
  <v-card>
    <v-card-title class="headline grey lighten-2" primary-title>
      {{ $t('Delete') }} {{ $t('user') }} {{ this.tmpid.name }}?
    </v-card-title>
    <v-card-text>
      This action can not be reverted!
    </v-card-text>
    <v-divider></v-divider>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn
        color="red"
        flat
        @click="deleteDialog = false"
      >{{ $t('No') }}</v-btn>
      <v-btn
        color="green"
        flat
        @click="deleteAction()"
      >{{ $t('Yes') }}</v-btn>
    </v-card-actions>
  </v-card>
</v-dialog>
      </div>
    </template>
<script>
   import { eventBus } from '../../eventBus.js';
  import { store } from '../../store.js';
  import VueMarkdown from 'vue-markdown'
  const $ = require('jquery');
  export default {
    props: ['baseUrl','canloadmore'],
    data(){
      return {
        tmpid: 0,
        deleteDialog:false,
        selectedRoles:[],
        search:''
      }
    },
    mounted: function () {

    },
    computed: {
      csrf: function(){
        return store.getters.getCSRF()
      },
      currentuser(){
        return store.getters.getUserById(store.state.loginId)
      },
      loggeduserid(){
        return store.state.loginId
      },
      users:function(){
        return store.state.users
      },
      selectRoles:function(){
        var sr = [];
        let that = this
        $.each( store.state.users, function( key, value ) {
          //sr.push({value:value.slug,text:value.name})
          that.selectedRoles[value.id] = that.convertRoles(value.roles)
        });
        $.each( store.state.roles, function( key, value ) {
          sr.push({value:value.slug,text:value.name})

        });
        return sr
      }
    },
    methods: {
      convertRoles(r){
        var sr = [];
        $.each( r, function( key, value ) {
          sr.push(value.split(":")[0])
        });
        return sr
      },
      openConfirm(id){
        this.tmpid = store.getters.getUserById(id)
        this.deleteDialog=true
      },
      changeRoles(id) {
        var sr = this.selectedRoles[id];
        var d = new FormData($("#urolesform"+id)[0]);
        d.append('roles',String(sr))
        $.ajax({
            url: '/internal-api/users/changeroles',
            type: 'POST',
            data:d,
            cache: false,
            contentType: false,
            processData: false,
            complete : function(res) {
              if(res.status==200){
                store.commit("setUsers",res.responseJSON.data)
              }
            }
        });
        return false;
      },
      deleteAction() {
        let that = this
        $.ajax({
            url: '/internal-api/user/delete/'+this.tmpid.id,
            type: 'POST',
            data:new FormData($("#hiddenCSRFForm")[0]),
            cache: false,
            contentType: false,
            processData: false,
            complete : function(res) {
              if(res.status==200){
                that.deleteDialog=false
                store.commit("setUsers",res.responseJSON.data)
              }
            }
        });
        return false;
      },
    },
    components : {
        VueMarkdown
    }
  }
</script>

<style lang="stylus">
.con-expand-users
  .con-btns-user
    display flex
    padding 10px
    padding-bottom 0px
    align-items center
    justify-content space-between
    .con-userx
      display flex
      align-items center
      justify-content flex-start
  .list-icon
    i
      font-size .9rem !important
</style>
