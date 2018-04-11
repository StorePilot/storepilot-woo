<template>
  <table
    cellspacing="0"
    v-if="product!==null"
    class="sp-options">
    <tbody>
    <tr>
      <td>
        <h3>{{$sp.print['ShortDescription']}}</h3>
        <prop-textarea
          :raw="product.short_description_raw"
          :prop="product.short_description"
          :autosave="$sp.data.autosave"
        ></prop-textarea>
      </td>
    </tr>
    <tr>
      <td>
        <h3>{{$sp.print['Description']}}</h3>
        <prop-textarea
          :raw="product.description_raw"
          :prop="product.description"
          :autosave="$sp.data.autosave"
        ></prop-textarea>
      </td>
    </tr>
    <tr>
      <td>
        <h3>{{$sp.print['PurchaseNote']}}</h3>
        <prop-textarea
          :raw="product.purchase_note_raw"
          :prop="product.purchase_note"
          :autosave="$sp.data.autosave"
        ></prop-textarea>
      </td>
    </tr>
    </tbody>
  </table>
</template>

<script>
  import propTextarea from '../../../../../components/properties/PropTextarea'
  export default {
    props: [
      'product'
    ],
    name: 'Description',
    components: {
      propTextarea
    }
  }
</script>
