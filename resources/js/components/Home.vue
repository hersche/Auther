<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <v-card class="text-center">

                    <v-card-text>
                        <div class="headline text-center">Your central place for your user</div>
                        Here, you can manage your user for the following apps:
                        <div :key="item.id" v-for="item in projects">
                            <ProjectChip v-bind:item="item"></ProjectChip>
                        </div>
                    </v-card-text>
                </v-card>
            </div>
        </div>
    </div>
</template>

<script>
    import {store} from '../store.js';
    import ProjectChip from './ProjectChip';
    export default {
        components: {
            ProjectChip
        },
        computed: {
            csrf: function () {
                return store.getters.getCSRF()
            },
            currentuser() {
                return store.getters.getUserById(store.state.loginId)
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
        }
    }
</script>
