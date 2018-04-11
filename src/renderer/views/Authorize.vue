<template>
  <el-row>
    <el-col v-if="authorized" :xs="24" class="page-padding">
      <h3>{{$sp.print['YouAreAuthorized']}}</h3>
      <el-button @click="$router.push('/')">{{$sp.print['ReturnToProductManager']}}</el-button>
    </el-col>
    <!-- Only required in dev mode, doesnt need translation -->
    <el-col v-else :xs="24" style="padding: 20px;">
      <h3 v-if="iframe">Authorize Woocommerce</h3>
      <span v-if="iframe"><strong>Note:</strong> This is only required in dev mode</span>
      <h4>{{msg}}</h4>
      <ol v-if="solutions.length">
        <li v-for="solution in solutions">
          {{solution}}
        </li>
      </ol>
      <div v-if="solutions.length">
        <h4>You can also try authenticate manually by pasting woocommerce credentials bellow</h4>
        <el-input placeholder="consumer_key" type="text" v-model="key"></el-input>
        <div style="margin-bottom: 40px;"></div>
        <el-input placeholder="consumer_secret" type="text" v-model="secret"></el-input>
        <div style="margin-bottom: 40px;"></div>
        <el-button @click="auth">Authenticate</el-button>
      </div>
      <div style="margin-bottom: 40px;"></div>
      <iframe @load="check" @error="check" v-if="iframe" frameBorder="0" style="width: 100%; height: 750px;" :src="iframeSource"></iframe>
      <div style="margin-bottom: 40px;"></div>
    </el-col>
  </el-row>
</template>

<script>
  import axios from 'axios'
  export default {
    name: 'authenticate',
    props: [
      'authorized',
      'authenticated'
    ],
    data () {
      return {
        iframeSource: '',
        msg: '',
        key: '',
        secret: '',
        iframe: true,
        solutions: [],
        query: '',
        token: Math.random().toString(36).replace(/[^a-z]+/g, '').substr(0, 14)
      }
    },
    watch: {
      authenticated (valid) {
        if (valid) {
          this.$router.push('/')
        }
      }
    },
    created () {
      this.query = 'app_name=StorePilot&scope=read_write&user_id=1&return_url=' +
          encodeURIComponent(location.href + '?token=' + this.token) +
          '&callback_url=' + encodeURIComponent('https://output.one/proxy?redirect_token=' +
          this.token)
      let scope = this
      let token = ''
      if (location.href.indexOf('token=') !== -1) {
        let startIndex = location.href.indexOf('token=')
        token = location.href.substring(startIndex + 6)
        axios.get('https://output.one/proxy/tokens/' + token + '.php').then(response => {
          scope.key = response.data.consumer_key
          scope.secret = response.data.consumer_secret
          scope.auth(scope.key, scope.secret)
        })
      } else if (location.href.indexOf('success=1') !== -1) {
        this.msg = 'Authentication SUCCEED! Please refresh your browser.'
        this.iframe = false
      } else if (location.href.indexOf('success=0') !== -1) {
        this.msg = 'Authentication FAILED! Possible fixes:'
        this.solutions = [
          'Check your internet connection',
          'Check that Authentication proxy is up and running: https://output.one/proxy/test.php',
          'Check that your WooCommerce version is higher or equal to 3.0.8',
          'Check that you got the latest version of StorePilot (run git pull & npm update in dev environment)',
          'Check that WooCommerce Rest Api is enabled'
        ]
        this.iframe = false
      } else if (this.$route.query.consumer_key && this.$route.query.consumer_secret) {
        scope.auth(scope.$route.query.consumer_key, scope.$route.query.consumer_secret)
      }
      this.iframeSource = this.$sp.server + '/wc-auth/v1/authorize?' + this.query
    },
    methods: {
      check () {
        let scope = this
        axios.post('https://proxy.output.one/tokens/' + scope.token + '.php').then(response => {
          scope.key = response.data.consumer_key
          scope.secret = response.data.consumer_secret
          scope.auth(scope.key, scope.secret, false)
        }).catch(e => {
          console.error(e)
        })
      },
      auth (key = this.key, secret = this.secret, iframe = true) {
        this.$pap.controller.config({
          base: this.$sp.server.replace('&success=1&user_id=1', ''),
          config: {
            key: key,
            secret: secret
          }
        })
        if (iframe) {
          top.location.href = (this.$sp.server.replace('&success=1&user_id=1', '') + '/wp-admin/admin.php?page=storepilot')
        } else {
          this.$router.push('/')
        }
      }
    }
  }
</script>
