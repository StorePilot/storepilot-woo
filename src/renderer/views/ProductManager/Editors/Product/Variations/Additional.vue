<template>
  <tbody>
    <h3>{{$sp.print['Additional']}}</h3>
    <!--tr>
      <td>Meta Data</td>
      <td>
        <prop-meta
            :prop="variation.meta_data"
            :product="variation">
        </prop-meta>
      </td>
    </tr-->
    <tr>
      <td></td>
      <td>
        <el-button
          style="display: block; margin-top: 20px;"
          v-if="variations.children.length && variation.id.value !== null"
          @click="$emit('deleteVariation')">
          {{$sp.print['DeleteVariation']}}
        </el-button>
      </td>
    </tr>
  </tbody>
</template>

<script>
  // import propMeta from '../../../../../components/properties/PropMeta'
  export default {
    props: [
      'variation',
      'variations'
    ],
    name: 'Additional',
    components: {
      // propMeta
    }
  }
</script>
