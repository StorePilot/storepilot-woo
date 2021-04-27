<?php 
if (!defined('ABSPATH')) exit;

add_action( 'init', function() {
	$labels = array(
		'name'                  => __('Shifts'),
		'singular_name'         => __('Shift'),
		'menu_name'             => __('Shifts'),
		'name_admin_bar'        => __('Shift'),
		'archives'              => __('Shift Archives'),
		'attributes'            => __('Shift Attributes'),
		'parent_item_colon'     => __('Parent Shift'),
		'all_items'             => __('All Shifts'),
		'add_new_item'          => __('Add Shift'),
		'add_new'               => __('Add New'),
		'new_item'              => __('New Shift'),
		'edit_item'             => __('Edit Shift'),
		'update_item'           => __('Update Shift'),
		'view_item'             => __('View Shift'),
		'view_items'            => __('View Shifts'),
		'search_items'          => __('Search Shift'),
		'not_found'             => __('Not found'),
		'not_found_in_trash'    => __('Not found in Trash'),
		'featured_image'        => __('Featured Image'),
		'set_featured_image'    => __('Set featured image'),
		'remove_featured_image' => __('Remove featured image'),
		'use_featured_image'    => __('Use as featured image'),
		'insert_into_item'      => __('Insert into item'),
		'uploaded_to_this_item' => __('Uploaded to this item'),
		'items_list'            => __('Shifts list'),
		'items_list_navigation' => __('Shifts list navigation'),
		'filter_items_list'     => __('Filter items list'),
	);
	$args = array(
		'label'                 => 'Shift',
		'description'           => 'Shifts used for X and Z reports',
		'labels'                => $labels,
		'supports'              => array( 'title', 'custom-fields' ),
		'hierarchical'          => false,
		'public'                => false,
		'show_ui'               => false,
		'show_in_menu'          => false,
		'show_in_admin_bar'     => false,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => true,
		'publicly_queryable'    => false,
		'rewrite'               => false,
		'capability_type'       => 'post',
        'show_in_rest'          => true,
        'rest_base'             => 'shifts'
	);
	register_post_type( 'shift', $args );
});

// Add to REST API
add_action( 'rest_api_init', function() {
  register_rest_field('shift', 'start', array(
    'get_callback'    => 'get_start',
    'update_callback' => 'update_shift',
    'schema' => array(
      'description' => __('Start'),
      'type'        => 'date'
    )
  ));
  register_rest_field('shift', 'end', array(
    'get_callback'    => 'get_end',
    'update_callback' => 'update_shift',
    'schema' => array(
      'description' => __('End'),
      'type'        => 'date'
    )
  ));
});

// Get
function get_start($obj)
{
  return get_post_meta($obj['id'], 'start', true);
}
function get_end($obj)
{
  return get_post_meta($obj['id'], 'end', true);
}

// Update
function update_shift($value, $post, $key)
{
  $post_id = update_post_meta($post->ID, $key, $value);
  if (false === $post_id) {
    return new WP_Error(
      'rest_shift_failed',
      __('Failed to update shift.'),
      array('status' => 500)
    );
  }
  return true;
}