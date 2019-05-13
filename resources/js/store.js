 import Vue from 'vue'
 import Vuex from 'vuex'
 Vue.use(Vuex)
 const $ = require("jquery");
 const axios = require('axios');
 export const store = new Vuex.Store({
      state: {
        loginId:Number($("#loggedUserId").attr("content")),
        users:[],
        roles:[],
        tokens:[],
        projects:[],
        twofactor:undefined,
        notifications:[],
        CSRF:document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      getters: {
        getUsersBySearch: (state) => (term) => {
          var sr = []
          $.each(state.users, function(i, el){
            // Only public users or exactly known usernames should be found for privacy
            // Additional, private users are almost empty, but this is for friend-requests
            if(el.username==term){
                sr.push(el)
            }
            if(el.public){
              if(el.username.indexOf(term)>-1){
                if(sr.indexOf(el)==-1){
                  sr.push(el)
                }
              } else if(el.name.indexOf(term)>-1){
                if(sr.indexOf(el)==-1){
                  sr.push(el)
                }
              } else if(el.bio.indexOf(term)>-1){
                if(sr.indexOf(el)==-1){
                  sr.push(el)
                }
              }
            }
            });
            return sr
          },
        getProjectsBySearch: (state) => (term) => {
          var sr = []
          $.each(state.projects, function(i, el){
            if(el.title.indexOf(term)>-1){
              if(sr.indexOf(el)==-1){
                sr.push(el)
              }
            } else if(el.description.indexOf(term)>-1){
              if(sr.indexOf(el)==-1){
                sr.push(el)
              }
            }
          });
          return sr
        },
        getUserByUsername: (state) => (name) => {
          //return undefined
          var u;
          if(state.users!=[]){
            u = state.users.find(u => u.username == name)
          }
          return u
        },
        getUserById: (state) => (id) => {
          id = Number(id)
          //return undefined
          var u;
          if(state.users!=[]){
            console.log(state.users)
            u = state.users.find(u => u.id == id)
          }
          if(u==undefined){
            u={id:0,name:"None",admin:false,avatar:'/img/404/avatar.png',background:'/img/404/background.png'}
            //u=new User(0,"None","/img/404/avatar.png","/img/404/background.png","None-profile",{},"",false)
          }
          return u
        },
        getProjectById: (state) => (id) => {
          id = Number(id)
          //return undefined
          var u;
          if(state.projects!=[]){
            console.log(state.users)
            u = state.projects.find(u => u.id == id)
          }
          if(u==undefined){
            u={id:0,name:"None",admin:false,avatar:'/img/404/avatar.png',background:'/img/404/background.png'}
            //u=new User(0,"None","/img/404/avatar.png","/img/404/background.png","None-profile",{},"",false)
          }
          return u
        },
        getProjectByClientId: (state) => (id) => {
          id = Number(id)
          //return undefined
          var u;
          if(state.projects!=[]){
            console.log(state.users)
            u = state.projects.find(u => u.client_id == id)
          }
          if(u==undefined){
            u={id:0,name:"None",admin:false,avatar:'/img/404/avatar.png',background:'/img/404/background.png'}
            //u=new User(0,"None","/img/404/avatar.png","/img/404/background.png","None-profile",{},"",false)
          }
          return u
        },
        getCSRF: (state) => () => {
          let that = this
          $.getJSON('/internal-api/refresh-csrf').done(function(data){
            if(data.csrf != store.state.CSRF){
              store.commit("setCSRF",data.csrf)
            }
          });
          return state.CSRF
        },
        receiveUsers: (state) => () => {
          axios.get("/internal-api/users",{})  
          .then(function (response) {
            store.commit("setUsers",JSON.parse(response.request.response).data)
           console.log(JSON.parse(response.request.response).data);
           let u = store.getters.getUserById(store.state.loginId)
           if(u.redirect!=undefined&&u.redirect!=""&&window.location.href!=u.redirect
            ){
             window.location.href = u.redirect;
            }
           return response.data
         })
         .catch(function (error) {
           console.log(error);
         })
       },
       receiveRoles: (state) => () => {
         axios.get("/internal-api/role",{})  
         .then(function (response) {
           store.state.roles = JSON.parse(response.request.response)
          console.log(JSON.parse(response.request.response));
          return response.data
        })
        .catch(function (error) {
          console.log(error);
        })
      },
      receiveNotifications: (state) => () => {
        axios.get("/internal-api/notifications",{})  
        .then(function (response) {
         store.state.notifications = JSON.parse(response.request.response)
         console.log(JSON.parse(response.request.response));
         return response.data
       })
       .catch(function (error) {
         console.log(error);
       })
     },
      receiveProjects: (state) => () => {
        axios.get("/internal-api/project",{})  
        .then(function (response) {
          store.state.projects = JSON.parse(response.request.response).data
         console.log("receive projects",JSON.parse(response.request.response));
         return response.data
       })
       .catch(function (error) {
         console.log(error);
       })
      }
      },
      mutations: {
        setUsers(state,users){
          state.users = users
        },
        addUser(state,user){
          state.users.push(user)
        },
        setTokens(state,tokens){
          state.tokens = tokens
        },
        setNotifications(state,notifications){
          state.notifications = notifications
        },
        setCSRF(state,CSRF){
          $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': CSRF }});
          $('meta[name="csrf-token"]').attr('content',CSRF)
          state.CSRF = CSRF;
        },
        setLoginId(state,loginId){
          state.loginId = loginId
        },
        setTwofactor(state,two){
          state.twofactor = two
        },
        setProjects(state,projects){
          state.projects = projects
        },
        setRoles(state,roles){
          state.roles = roles
        },
      }
    }) 