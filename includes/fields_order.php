<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

$post_type_order = 'shop_order'; 
$values_order = [
  ['bax', 'Bax', 'BAX-Numer'],
  ['operator', 'Operator', 'John Doe']
];

// Show input boxes
add_action('add_meta_boxes', function () use ($post_type_order, $values_order, $slug, $name) {

  add_meta_box($slug, $name, function () use ($values_order) {
    wp_nonce_field('storepilot_nonce', 'nonce');
    foreach ($values_order as $val) {
      $value = get_post_meta(get_the_ID(), $val[0], true);
      $id = isset($val[0]) ? $val[0] : '';
      $label = isset($val[1]) ? $val[1] : '';
      $placeholder = isset($val[2]) ? $val[2] : '';
      ?>

      <div style="margin-bottom: 10px">
        <label><?php echo $label; ?></label><br>
        <input type="text" id="<?php echo $id; ?>" name="<?php echo $id; ?>" placeholder="<?php echo $placeholder; ?>" value="<?php echo $value; ?>" />
      </div>

<?php
    }
  }, $post_type_order, 'side', 'default');
});

// Update on save
add_action('save_post', function ($id) use ($values_order) {
  if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'storepilot_nonce')) {
    foreach ($values_order as $val) {
      $name = $val[0];
      if (isset($_POST[$name])) {
        add_post_meta($id, $name, $_POST[$name]);
        update_post_meta($id, $name, $_POST[$name]);
      }
    }
  }
});


// Add to REST API
add_action( 'rest_api_init', 'register_order_fields' );
function register_order_fields() {
  register_rest_field('shop_order', 'bax', array(
    'get_callback'    => 'get_bax',
    'update_callback' => 'update_bax',
    'schema' => array(
      'description' => __('BAX'),
      'type'        => 'string'
    )
  ));
  register_rest_field('shop_order', 'operator', array(
    'get_callback'    => 'get_operator',
    'update_callback' => 'update_operator',
    'schema' => array(
      'description' => __('Operator'),
      'type'        => 'string'
    )
  ));
}

// Get
function get_bax($obj)
{
  return get_post_meta($obj['id'], 'bax', true);
}
function get_operator($obj)
{
  return get_post_meta($obj['id'], 'operator', true);
}

// Update
function update_bax($value, $post, $key)
{
  $obj = json_decode($post, true);
  $post_id = update_post_meta($obj['id'], $key, $value);
  if (false === $post_id) {
    return new WP_Error(
      'rest_order_bax_failed',
      __('Failed to update order bax.'),
      array('status' => 500)
    );
  }
  return true;
}
function update_operator($value, $post, $key)
{
  $obj = json_decode($post, true);
  $post_id = update_post_meta($obj['id'], $key, $value);
  if (false === $post_id) {
    return new WP_Error(
      'rest_order_operator_failed',
      __('Failed to update order operator.'),
      array('status' => 500)
    );
  }
  return true;
}