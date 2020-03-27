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
            <v-container fluid>
                <v-layout row wrap>
                    <v-flex :key="item.id" class="ml-1 mb-1" cols="4" d-flex md3 sm6
                            style="text-align: center;" v-for="item in userSearchResult" xs12>
                        <router-link :to="'/profile/'+item.id">
                            <v-card :img="item.background">
                                <v-avatar class="mx-auto"><img :src='item.avatar' class='pl-2 pt-1 pb-1'/></v-avatar>
                                <v-card-text class="mx-auto">
                                    <v-chip>{{ item.name }} ({{ item.username }})</v-chip>
                                </v-card-text>

                            </v-card>
                        </router-link>
                    </v-flex>
                </v-layout>
            </v-container>
        </div>

        <div class="text-center" v-if="projectSearchResult.length==0&userSearchResult.length==0">
            <h2>There are no projects or users like {{ $route.params.term }}</h2>
        </div>

    </div>
</template>

<script>
    import {eventBus} from '../eventBus.js';
    import {store} from '../store.js';
    import ProjectChip from './ProjectChip'

    export default {
        created() {

            // this.getContent(this.$route.params.uid);
        },
        components: {
            ProjectChip
        },
        computed: {
            csrf: function () {
                return store.getters.getCSRF()
            },
            userSearchResult() {
                return store.getters.getUsersBySearch(this.$route.params.term);
                //return store.getters.getUserById(store.state.loginId)
            },
            projectSearchResult() {
                return store.getters.getProjectsBySearch(this.$route.params.term);
            },
            loggeduserid() {
                return store.state.loginId
            },
            projects: function () {
                return store.state.projects
            },
        },
        mounted() {
            console.log('Component mounted.')
        },
        data() {
            return {
                //searchResult: [],
            }
        }
    }
</script>
