<template>
  <div>
    <el-table
        v-loading="prop.loading"
        :data="downloadsIndexed"
        style="width: 100%; margin-top: 20px;">
      <el-table-column
          prop="name"
          :label="$sp.print['Name']">
      </el-table-column>
      <el-table-column
          type="index"
          width="100"
          :label="$sp.print['Url']">
        <template slot-scope="scope">
          <el-button
              @click="open(prop.value[scope.$index].file)"
              size="small">
            {{$sp.print['Open']}}
          </el-button>
        </template>
      </el-table-column>
      <el-table-column
          type="index"
          width="100"
          :label="$sp.print['Actions']">
        <template slot-scope="scope">
          <el-button
              @click="prop.value.splice(scope.$index, 1); autosave ? prop.save() : ''"
              size="small">
            {{$sp.print['Remove']}}
          </el-button>
        </template>
      </el-table-column>
    </el-table>
    <div style="display: block; width: 100%; height: 20px;"></div>
    <prop-media
        style="width: 100%;"
        :title="$sp.print['SelectFiles']"
        library=""
        :image="false"
        :multiple="true"
        :autosave="autosave"
        :prop="prop"
    ></prop-media>
  </div>
</template>

<script>
  import propMedia from './PropMedia'
  export default {
    props: [
      'prop',
      'autosave'
    ],
    name: 'prop-downloads',
    components: {
      propMedia
    },
    methods: {
      open (url) {
        window.open(url, '_blank')
      }
    },
    computed: {
      downloadsIndexed () {
        let indexed = JSON.parse(JSON.stringify(this.prop.value))
        let i = 0
        indexed.forEach(val => {
          val.index = i++
        })
        return indexed
      }
    }
  }
</script>
