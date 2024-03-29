 import Vue from 'vue'
 import Vuex from 'vuex'
 Vue.use(Vuex)
 const $ = require("jquery");
 const axios = require('axios');

 //hacky part to guarantee the login. the variables should not be global...
 let jwt_token = localStorage.getItem('jwt_token');
 if(findGetParameter('token')!=null){
  jwt_token = findGetParameter('token')
  localStorage.setItem('jwt_token',findGetParameter('token'))
 }
 let CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
 if(jwt_token!=undefined&&jwt_token!=''){
   $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': CSRF,'Authorization':'Bearer '+jwt_token }});
   axios.defaults.headers.common = { 'X-CSRF-TOKEN': CSRF,'Authorization':'Bearer '+jwt_token }
 } else {
   $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': CSRF }});
   axios.defaults.headers.common = { 'X-CSRF-TOKEN': CSRF };
 }
 // end of hacky part

 export const store = new Vuex.Store({
      state: {
        loginId:0,
        users:[],
        roles:[],
        tokens:[],
        projects:[],
        twofactor:undefined,
        notifications:[],
        CSRF:""
      },
      getters: {
        getUsersBySearch: (state) => (term) => {
          var sr = []
          state.users.forEach(function(el,i){
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
          state.projects.forEach( function(el, i){
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
          }
          return u
        },
        getProjectByClientId: (state) => (id) => {
          id = Number(id)
          var u;
          if(state.projects!=[]){
            console.log(state.users)
            u = state.projects.find(u => u.client_id == id)
          }
          if(u==undefined){
            u={id:0,name:"None",admin:false,avatar:'/img/404/avatar.png',background:'/img/404/background.png'}
          }
          return u
        },
        getCSRF: (state) => () => {
            axios.get("/internal-api/refresh-csrf",{})
                .then(function (response) {
                    store.commit("setCSRF",response.data.csrf)
                })
                .catch(function (error) {
                    console.log(error);
                })
          return state.CSRF
        },
        receiveUsers: (state) => () => {
          axios.get("/internal-api/users",{})
          .then(function (response) {
           store.commit("setUsers",JSON.parse(response.request.response).data)
           let u = store.getters.getUserById(store.state.loginId)
           if(u.redirect!=undefined&&u.redirect!=""&&window.location.href!=u.redirect
            ){
             window.location.href = u.redirect+"?token="+localStorage.getItem('jwt_token');
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
          state.users.forEach(function(el, i){
            if(el.you){
              state.loginId=el.id;
              store.getters.receiveRoles();
              store.getters.receiveNotifications();
            }
          })
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
          let jwt_token = localStorage.getItem('jwt_token');
          if(jwt_token!=undefined&&jwt_token!=''){
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': CSRF,'Authorization':'Bearer '+jwt_token }});
            axios.defaults.headers.common = { 'X-CSRF-TOKEN': CSRF,'Authorization':'Bearer '+jwt_token }
          } else {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': CSRF }});
            axios.defaults.headers.common = { 'X-CSRF-TOKEN': CSRF };
          }
          var csrfElement = document.getElementById('csrf-token')
          csrfElement.setAttribute('content',CSRF)
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


function findGetParameter(parameterName) {
    var result = null
    if(location.hash.indexOf(parameterName+"=")>-1){
	     result = location.hash.substr(location.hash.indexOf(parameterName+"=")+(parameterName.length+1))
    }
    return result;
}
