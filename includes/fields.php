<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

$slug = 'storepilot_metabox'; // Unique identifier for this meta box
$name = 'StorePilot'; // Title for this meta box
$post_type = 'product'; // At which post type it should appear
$values = [
  ['barcode', 'Barcode', 'EAN / GTIN'],
  ['rack', 'Rack', 'A0-103']
];

// Show input boxes
add_action('add_meta_boxes', function () use ($slug, $name, $values, $post_type) {
  add_meta_box($slug, $name, function () use ($values) {
    wp_nonce_field('storepilot_nonce', 'nonce');
    foreach ($values as $val) {
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
  }, $post_type, 'side', 'default');
});

// Update on save
add_action('save_post', function ($id) use ($values) {
  if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'storepilot_nonce')) {
    foreach ($values as $val) {
      $name = $val[0];
      if (isset($_POST[$name])) {
        add_post_meta($id, $name, $_POST[$name]);
        update_post_meta($id, $name, $_POST[$name]);
      }
    }
  }
});

// Add to REST API
add_action( 'rest_api_init', 'register_post_fields' );
function register_post_fields() {
  register_rest_field('product', 'barcode', array(
    'get_callback'    => 'get_barcode',
    'update_callback' => 'update_barcode',
    'schema' => array(
      'description' => __('Barcode EAN / GTIN'),
      'type'        => 'string'
    )
  ));
  register_rest_field('product', 'rack', array(
    'get_callback'    => 'get_rack',
    'update_callback' => 'update_rack',
    'schema' => array(
      'description' => __('Rack number'),
      'type'        => 'string'
    )
  ));
}

// Get
function get_barcode($obj)
{
  return get_post_meta($obj['id'], 'barcode', true);
}
function get_rack($obj)
{
  return get_post_meta($obj['id'], 'rack', true);
}

// Update
function update_barcode($value, $post, $key)
{
  $obj = json_decode($post, true);
  $post_id = update_post_meta($obj['id'], $key, $value);
  if (false === $post_id) {
    return new WP_Error(
      'rest_product_barcode_failed',
      __('Failed to update product barcode.'),
      array('status' => 500)
    );
  }
  return true;
}
function update_rack($value, $post, $key)
{
  $obj = json_decode($post, true);
  $post_id = update_post_meta($obj['id'], $key, $value);
  if (false === $post_id) {
    return new WP_Error(
      'rest_product_rack_failed',
      __('Failed to update product rack.'),
      array('status' => 500)
    );
  }
  return true;
}