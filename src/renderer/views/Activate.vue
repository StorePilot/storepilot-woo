<template>
  <el-row>
    <el-col :xs="24" style="padding: 20px;">
      <el-card class="box-card">
        <div slot="header" class="clearfix">
          <h2>{{$sp.print['WoocommerceNotFound']}}</h2>
        </div>
        <el-alert
            v-if="activated&&!$sp.dev"
            :title="$sp.print['WoocommerceActivationSucceededPleaseRefresh']"
            type="success"
            :description="$sp.print['WoocommerceActivationSucceeded']"
            :closable="false"
            show-icon>
        </el-alert>
        <div v-else>
          <h3>Woocommerce 3.2.6 {{$sp.print['OrHigherRequired']}}</h3>
          <h4>{{$sp.print['PleaseFollowTheseStepsToActivateStorepilot']}}</h4>
          <div class="step text item">
            1. <a target="_blank" :href="$sp.server + '/wp-admin/plugin-install.php?tab=plugin-information&plugin=woocommerce'">{{$sp.print['InstallUpdate']}}</a> Woocommerce
          </div>
          <div class="step text item">
            2. <a target="_blank" :href="$sp.server + '/wp-admin/plugins.php'">{{$sp.print['Activate']}}</a> Woocommerce
          </div>
          <div class="step text item">
            3. <a target="_top" :href="loc">{{$sp.print['Refresh']}}</a> {{$sp.print['ThisPage']}}
          </div>
          <el-progress type="circle" :percentage="50"></el-progress>
        </div>
      </el-card>
    </el-col>
  </el-row>
</template>

<script>
  export default {
    props: [
      'activated',
      'authenticated'
    ],
    name: 'activate',
    data () {
      return {
        loc: document.referrer
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
      let scope = this
      if (this.$sp.dev) {
        setTimeout(() => {
          scope.$emit('auth', false)
        }, 500)
      }
    }
  }
</script>

<style>
  .step {
    margin: 20px;
  }
  .text {
    font-size: 14px;
  }
  .item {
    padding: 18px 0;
  }
</style>
