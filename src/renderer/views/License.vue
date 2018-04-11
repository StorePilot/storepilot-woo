<template>
  <el-row v-loading="!activated||!authenticated">
    <el-col class="sp-validate">
      <h2>{{$sp.print['Activate']}} StorePilot</h2>
      <div style="margin: 40px auto">
        <el-button plain @click="fetch">{{$sp.print['Refresh']}}</el-button>
        <el-button plain type="primary" @click="validate">{{$sp.print['Validate']}}</el-button>
        <el-button plain type="warning" @click="deactivate">{{$sp.print['Deactivate']}}</el-button>
        <el-button plain type="success" @click="activate">{{$sp.print['Activate']}}</el-button>
      </div>
      <el-input
        v-loading="loading"
        :placeholder="$sp.print['LicenseKey']"
        v-model="license"></el-input>
      <br>
      <el-alert
        style="margin-top: 50px"
        v-if="message !== ''"
        :title="message"
        :closable="false"
        type="info">
      </el-alert>
    </el-col>
  </el-row>
</template>

<script>
  export default {
    name: 'license',
    props: [
      'activated',
      'authenticated'
    ],
    data () {
      return {
        license: '',
        message: '',
        loading: false
      }
    },
    created () {
      if (this.authenticated) {
        this.fetch()
      }
    },
    watch: {
      'authenticated' (valid) {
        if (valid) {
          this.fetch()
        }
      }
    },
    methods: {
      fetch () {
        this.loading = true
        this.$sp.data.license.get('sp').then(response => {
          this.license = String(response.data.license)
          this.loading = false
        })
      },
      activate () {
        this.loading = true
        this.$sp.data.license.post('sp', {
          method: 'activate',
          license: this.license
        }).then(response => {
          this.message = response.data.message
          this.loading = false
        })
      },
      deactivate () {
        this.loading = true
        this.$sp.data.license.post('sp', {
          method: 'deactivate',
          license: this.license
        }).then(response => {
          this.message = response.data.message
          this.loading = false
        })
      },
      validate () {
        this.loading = true
        this.$sp.data.license.post('sp', {
          method: 'validate',
          license: this.license
        }).then(response => {
          this.message = response.data.message
          this.loading = false
        })
      }
    }
  }
</script>

<style>
  .sp-validate {
    margin: 50px auto;
    max-width: 500px;
    float: none!important;
  }
</style>
