<template>
  <div class="container">
      <v-card-text class="row justify-content-center">
          <v-card-title class="col-md-8">
              <v-card>
                  <v-card-title>Reset Password</v-card-title>

                  <v-card-text>
                      <form method="POST" action="" aria-label="Reset Password">

                          <input type="hidden" name="_token" :value="csrf">

                          <div class="form-group row">
                              <label for="email" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>

                              <div class="col-md-6">
                                  <input id="email" type="email" class="form-control" name="email" value="" required autofocus>
                              </div>
                          </div>

                          <div class="form-group row">
                              <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>

                              <div class="col-md-6">
                                  <input id="password" type="password" class="form-control" name="password" required>
                              </div>
                          </div>

                          <div class="form-group row">
                              <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Confirm Password</label>

                              <div class="col-md-6">
                                  <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                              </div>
                          </div>

                          <div class="form-group row mb-0">
                              <div class="col-md-6 offset-md-4">
                                  <button type="submit" class="btn btn-primary">Reset Password</button>
                              </div>
                          </div>
                      </form>
                  </v-card-text>




              </v-card>
          </div>
      </div>
  </div>
</template>


<script>
  import { eventBus } from '../../eventBus.js';
  export default {
    props: ['medias','baseUrl','user','tags'],
    data(){
      return {
        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    }

  }
</script>
