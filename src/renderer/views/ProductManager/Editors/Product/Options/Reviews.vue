<template>
  <tr>
    <td>{{$sp.print['Reviews']}}</td>
    <td>
      <prop-reviews
        :productId="product.id.value"
      ></prop-reviews>
    </td>
  </tr>
</template>

<script>
  import propReviews from '../../../../../components/properties/PropReviews'
  export default {
    props: [
      'product'
    ],
    name: 'Reviews',
    components: {
      propReviews
    }
  }
</script>
