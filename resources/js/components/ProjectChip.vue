<template>
<div>
  <v-chip color="primary" text-color="white">
    <v-avatar class="green darken-4" v-if="item.avatar!=''"><img :src="item.avatar" alt="test" /></v-avatar>
    <a :href="item.url">  {{ item.title }}</a>
    <a :href="item.direct_login_url" :title="$t('Direct')+' '+$t('login')"><v-icon right>lock</v-icon></a>
    <a  :title="$t('Infobox')"><v-icon @click="dialog=true" right>info</v-icon></a>
  </v-chip>
  
  <v-dialog v-model="dialog">
  <v-card >
    
  <v-card-text class="text-center" :style="'background-image:url('+item.background+'); background-position: center; background-size: cover;'">
    <div> <img class="text-center" v-if="item.avatar!=''" :src='item.avatar' /></div>
    <v-chip class="text-center" disabled color="blue" text-color="white"><v-subheader dark>{{ item.title }}</v-subheader></v-chip>
  </v-card-text>

    <v-card-text>
      <VueMarkdown :source="item.description"></VueMarkdown>
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
        :href="item.url"
      >
        {{ $t('Visit') }} {{ $t('page') }}
      </v-btn>

      <v-btn
        color="green darken-1"
        flat="flat"
        :href="item.direct_login_url"
      >
        {{ $t('Direct') }} {{ $t('login') }}
      </v-btn>
    </v-card-actions>
  </v-card>
</v-dialog>
</div>
</template>

<script>
import VueMarkdown from 'vue-markdown'
  export default {
    props: ['item'],
    components : {
        VueMarkdown,
    },
    data:()=>({
      dialog:false,
    })
  }
  </script>