<template>
    <div>
        <vue-croppie
            :boundary="{ width: (Number(width)+20), height: (Number(height)+20) }"
            :enableExif="true"
            :enableOrientation="true"
            :enableResize="false"
            :viewport="{ width: Number(width), height: Number(height), type: type }"
            @result="result"
            @update="update"
            ref="croppieRef">
        </vue-croppie>

        <input :id="name+'Base'" :name="name" :value="croppedBase64" type="hidden"/>
        <v-btn @click="croppedBase64=null; $refs.croppieRef.refresh();" icon small title="Set no picture">
            <v-icon>delete</v-icon>
        </v-btn>
        <!-- Rotate angle is Number -->
        <v-btn @click="rotate(90,$event)" icon small>
            <v-icon>rotate_left</v-icon>
        </v-btn>
        <v-btn @click="rotate(-90,$event)" icon small>
            <v-icon>rotate_right</v-icon>
        </v-btn>
        <input :id="name+'Upload'" :name="name+'File'" @change="change()" accept=".png,.jpg,.jpeg" type="file">
    </div>
</template>

<script>
    export default {
        props: ['baseUrl', 'height', 'width', 'name', 'type', 'theurl'],
        mounted() {
            // this.$refs.croppieRef.refresh();
            let that = this
            if (this.theurl == undefined) {
                this.$refs.croppieRef.bind({
                    url: '/img/404/avatar.png'
                }).then(function () {
                    that.$refs.croppieRef.setZoom(0)
                })
            } else {
                if (this.theurl != '') {
                    this.$refs.croppieRef.bind({
                        url: this.theurl
                    }).then(function () {
                        that.$refs.croppieRef.setZoom(0)
                    })

                }
            }
            let options = {
                type: 'base64',
                size: 'viewport',
                format: 'png'
            }
            this.$refs.croppieRef.result(options, (output) => {
                this.croppedBase64 = output;
            });
            //defaultInitialZoom = !isNaN(parseInt(this.$refs.croppieRef.options.initialZoom)) ? this.$refs.croppieRef.options.initialZoom : Math.max((boundaryData.width / imgData.width), (boundaryData.height / imgData.height));
        },
        methods: {
            change() {
                var reader = new FileReader();
                let that = this;
                reader.onload = function (e) {
                    console.log("avatar-change!!!!", that.$refs.croppieRef)
                    that.$refs.croppieRef.bind({
                        url: e.target.result,
                    }).then(function () {
                        that.$refs.croppieRef.setZoom(0)
                    });
                }
                reader.readAsDataURL(document.getElementById(this.name + "Upload").files[0]);
            },
            // CALBACK USAGE
            result(output) {
                console.log("result", this.name)
                this.croppedBase64 = output;
            },
            update(val) {
                let options = {
                    type: 'base64',
                    size: 'viewport',
                    format: 'png'
                }
                this.$refs.croppieRef.result(options, (output) => {
                    this.croppedBase64 = output;
                });
            },

            rotate(rotationAngle, event) {
                // Rotates the image
                if (event) event.preventDefault()
                this.$refs.croppieRef.rotate(rotationAngle);
            },
        },
        data() {
            return {
                croppedBase64: null,
            }
        }
    }
</script>
