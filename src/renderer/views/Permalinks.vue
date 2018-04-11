<template>
  <el-row>
    <el-col :xs="24" class="page-padding">
      <el-card class="box-card">
        <div slot="header" class="clearfix">
          <h2>{{$sp.print['PrettyPermalinksNotSet']}}</h2>
        </div>
        <el-alert
            v-if="permalinks&&!$sp.dev"
            :title="$sp.print['PrettyPermalinksIsEnabledRefreshYourPage']"
            type="success"
            :description="$sp.print['PrettyPermalinksSetSuccessfully']"
            :closable="false"
            show-icon>
        </el-alert>
        <div v-else>
          <h3>{{$sp.print['PermalinksCanNotBePlain']}}</h3>
          <h4>{{$sp.print['PleaseFollowTheseStepsToActivateStorepilot']}}</h4>
          <div class="step text item">
            1. <a target="_blank" :href="$sp.server + '/wp-admin/options-permalink.php'">{{$sp.print['Open']}} </a> {{$sp.print['PermalinkSettings']}}
          </div>
          <div class="step text item">
            2. {{$sp.print['SetAPermalinkWhichIsNotPlainAndSave']}}
          </div>
          <div class="step text item">
            3. <a target="_top" :href="loc">{{$sp.print['Refresh']}}</a> {{$sp.print['ThisPage']}}
          </div>
          <el-progress type="circle" :percentage="75"></el-progress>
        </div>
      </el-card>
    </el-col>
  </el-row>
</template>

<script>
  export default {
    props: [
      'permalinks'
    ],
    name: 'permalinks',
    data () {
      return {
        loc: document.referrer
      }
    },
    created () {
      let scope = this
      if (this.$sp.dev) {
        setTimeout(() => {
          scope.$emit('permalinks', false)
        }, 500)
      }
    }
  }
</script>
