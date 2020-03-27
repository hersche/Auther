<template>
<div>
    <v-label>{{ $t(theTitle) }}</v-label>
    <v-tabs dark v-model="active">
        <v-tab key="0" ripple>
            {{ $t('Edit') }}
        </v-tab>
        <v-tab key="1" ripple>
            {{ $t('Preview') }}
        </v-tab>
        <v-tab-item key="0">
            <div>
                <v-textarea
                    :hint="$t('You can use markdown and preview it!')"
                    :id="theId"
                    :name="theId"
                    :placeholder="tmpHint"
                    v-model="tmpText"
                ></v-textarea>
            </div>
        </v-tab-item>
        <v-tab-item key="1">
            <div>
                <VueMarkdown :source="tmpText" v-if="tmpText!=''"></VueMarkdown>
                <h1 v-if="tmpText==''||tmpText==null">No text for preview</h1>
            </div>
        </v-tab-item>
    </v-tabs>
</div>
</template>

<script>
    import VueMarkdown from 'vue-markdown'

    export default {
        props: ['theText', 'theId', 'theTitle', 'theHint'],
        components: {
            VueMarkdown
        },
        mounted() {
            this.tmpText = this.theText
            if (this.theHint != undefined) {
                this.tmpHint = this.theHint
            }
        },
        data() {
            return {
                tmpText: '',
                tmpHint: '',
                active: 0
            }
        }
    }
</script>
