<template>
  <div>
    <v-dialog v-model="deleteDialog" class="mt-2" max-width="290">
      <v-card>
        <v-card-title class="headline">Delete project?</v-card-title>
        <v-card-text>
          Shure?
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
          color="green darken-1"
          flat="flat"
          @click="deleteDialog = false"
          >
          Disagree
        </v-btn>
        <v-btn
          color="green darken-1"
          flat="flat"
          @click="deleteAction()"
          >
          <v-icon>delete</v-icon> {{ $t('Delete') }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>


  <v-dialog v-model="editDialog" class="mt-2" max-width="80%">
    <v-card>
      <v-card-title class="headline">Edit project</v-card-title>
      <v-card-text>
        
        <form id="peform" v-if="currentEditProject!=undefined">
          <input type="hidden" name="_token" :value="csrf">
          <input type="hidden" name="pid" :value="currentEditProject.id">
          <v-text-field
            :label="$t('Title')"
            name="title"
            :value="currentEditProject.title"
            required
            ></v-text-field>
            <MarkdownCreator :key="currentEditProject.id" :theText="currentEditProject.description" theId="description" :theTitle="$t('Description')" ></MarkdownCreator>
            <v-text-field
              :label="$t('URL')"
              name="url"
              :value="currentEditProject.url"
              required
            ></v-text-field>
            <div class="form-group">
                <label>{{ $t("Avatar") }}</label>
            <Cropper :key="currentEditProject.id" v-bind:theurl="currentEditProject.avatar" v-bind:width="180" v-bind:height="180" type="circle" name="avatar" ></Cropper>
          </div>
          <div class="form-group">
              <label>{{ $t("Background") }}</label>
                  <Cropper :key="currentEditProject.id" v-bind:theurl="currentEditProject.background" v-bind:width="800" v-bind:height="394" type="square" name="background" ></Cropper>
          </div>
            <v-text-field
              :label="$t('Direct login url')"
              name="direct_login_url"
              :value="currentEditProject.direct_login_url"
              ></v-text-field>
              <v-text-field
                :label="$t('Version')"
                name="version"
                :value="currentEditProject.version"
                ></v-text-field>
            </form>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn
        color="green darken-1"
        flat="flat"
        @click="editDialog = false"
        >
        Disagree
      </v-btn>
      <v-btn
        color="green darken-1"
        flat="flat"
        @click="editAction()"
        >
        <v-icon>edit</v-icon> {{ $t('Edit') }}
      </v-btn>
    </v-card-actions>
  </v-card>
</v-dialog>
    
<v-dialog v-model="createDialog" class="mt-2" max-width="80%">
  <v-card>
    <v-card-title class="headline">Create project</v-card-title>
    <v-card-text>
<form id="pform">
  <input type="hidden" name="_token" :value="csrf">
  <v-text-field
    :label="$t('Title')"
    name="title"
    required
    ></v-text-field>
    
    {{ theClient }}
    
    <v-select
     v-model="theClient"
     :items="clients"
     label="OAuth-Client"
     item-text="name"
     item-value="id"
     prepend-icon="map"
     single-line
   ></v-select>
    <MarkdownCreator theText="" theId="description" :theTitle="$t('Description')" ></MarkdownCreator>
    <v-text-field
      :label="$t('URL')"
      name="url"
      required
    ></v-text-field>
    <div class="form-group">
        <label>{{ $t("Avatar") }}</label>
    <Cropper  v-bind:width="180" v-bind:height="180" type="circle" name="avatar" ></Cropper>
  </div>
  <div class="form-group">
      <label>{{ $t("Background") }}</label>
          <Cropper v-bind:width="800" v-bind:height="394" type="square" name="background" ></Cropper>
  </div>
    <v-text-field
      :label="$t('Direct login url')"
      name="direct_login_url"
      ></v-text-field>
      <v-text-field
        :label="$t('Version')"
        name="version"
        ></v-text-field>
    </form>
  </v-card-text>
  <v-card-actions>
    <v-spacer></v-spacer>
    <v-btn
    color="green darken-1"
    flat="flat"
    @click="createDialog = false"
    >
    Disagree
  </v-btn>
  <v-btn
    color="green darken-1"
    flat="flat"
    @click="submitAction()"
    >
    <v-icon>save</v-icon> {{ $t('Save') }}
  </v-btn>
</v-card-actions>
</v-card>
</v-dialog>
    
    <h1>Projects</h1>
    <div>
      <v-btn @click="openCreateDialog();"color="green" ><v-icon>save</v-icon>{{ $t('Create project') }}</v-btn>
    </div>
    
    <v-data-table
  :headers="headers"
  :items="projects"
  class="elevation-1"
>
  <template v-slot:items="props">
    <td>{{ props.item.title }}</td>
    <td class="">{{ props.item.url }}</td>
    <td class="">{{ props.item.direct_login_url }}</td>
    <td class="">{{ props.item.version }}</td>
    <td class=""><v-btn icon @click="openEditDialog(props.item)" ><v-icon>edit</v-icon></v-btn></td>
    <td class="">   <v-btn icon @click="openConfirm(props.item.id)"><v-icon>delete</v-icon></v-btn></td>
  </template>
</v-data-table>
  </div>
</template>
<script>
  import { eventBus } from '../../eventBus.js';
  import { store } from '../../store.js';
  import MarkdownCreator from '../MarkdownCreator'
  import VueMarkdown from 'vue-markdown'
  const axios = require("axios");
  const $ = require("jquery");
  import Cropper from '../cropp'
  export default {
    props: ['baseUrl','mixconfig'],
    mounted(){
      axios.get('/oauth/clients')
              .then(response => {
                var tmpArr = [{id:0,name:"None"}];
                response.data.forEach(function(client,key2){
                  tmpArr.push(client)
                });
                  this.clients = tmpArr;
              });
    },
    data(){
      return {
        tmpid: 0,
        clients:[],
        theClient:0,
        editDialog:false,
        createDialog:false,
        currentEditProject:undefined,
        deleteDialog:false,
        headers: [
  {
    text: 'Title',
    align: 'left',
    value: 'title'
  },
  { text: 'Url', value: 'url' },
  { text: 'Direct url', value: 'direct_login_url' },
  { text: 'Version', value: 'version' },
  { text: 'Edit', value: '' },
  { text: 'Delete', value: '' }
],
      }
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
      projects:function(){
        return store.state.projects
      },
    },
    methods: {
      openConfirm(id){
        this.tmpid = id
        this.deleteDialog = true
      },
      openCreateDialog(){
        this.createDialog = true
      },
      openEditDialog(d){
        this.currentEditProject = store.getters.getProjectById(d.id)
        console.log("the proj",this.currentEditProject)
        this.editDialog = true
      },
      deleteAction() {
        var d = new FormData($("#logoutForm")[0])
        d.append('pid',this.tmpid)    
        let that = this
        $.ajax({
            url: '/internal-api/project/delete',
            type: 'POST',
            data:d,
            cache: false,
            contentType: false,
            processData: false,
            complete : function(res) {
              if(res.status==200){
                that.tmpid = 0
                that.deleteDialog = false
                store.commit("setProjects",res.responseJSON.data)
              }
            }
        });
        return false;
      },
      editAction() {
        console.log("edit-data",$("#peform")[0])
        let that = this
        var d = new FormData($("#peform")[0])
        d.delete("avatarFile")
        d.delete("backgroundFile")
        $.ajax({
            url: '/internal-api/project',
            type: 'POST',
            data:d,
            cache: false,
            contentType: false,
            processData: false,
            complete : function(res) {
              if(res.status==200){
                //eventBus.$emit('userEdited','');
                that.editDialog=false;
                that.currentEditProject=undefined;
                store.commit("setProjects",res.responseJSON.data)
              }
            }
        });
        return false;
      },  
      submitAction() {
        let that = this
        var d = new FormData($("#pform")[0])
        d.delete("avatarFile")
        d.delete("backgroundFile")
        d.append("client_id",this.theClient)
        $.ajax({
            url: '/internal-api/project/create',
            type: 'POST',
            data:d,
            cache: false,
            contentType: false,
            processData: false,
            complete : function(res) {
              if(res.status==200){
                that.createDialog=false
                store.commit("setProjects",res.responseJSON.data)
                //eventBus.$emit('userEdited','');
              }
            }
        });
        return false;
      },
      
    },
    components : {
        VueMarkdown,
        MarkdownCreator,
        Cropper
    }
  }
</script>

<style>
.v-dialog__content--active{
  z-index: 999999 !important;
}
</style>

