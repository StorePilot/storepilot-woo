<?php 
if (!defined('ABSPATH')) exit;

add_action( 'init', function() {
	$labels = array(
		'name'                  => __('History'),
		'singular_name'         => __('History'),
		'menu_name'             => __('History'),
		'name_admin_bar'        => __('History'),
		'archives'              => __('History Archives'),
		'attributes'            => __('History Attributes'),
		'parent_item_colon'     => __('Parent History'),
		'all_items'             => __('All History'),
		'add_new_item'          => __('Add History'),
		'add_new'               => __('Add History'),
		'new_item'              => __('New History'),
		'edit_item'             => __('Edit History'),
		'update_item'           => __('Update History'),
		'view_item'             => __('View History'),
		'view_items'            => __('View History'),
		'search_items'          => __('Search History'),
		'not_found'             => __('Not found'),
		'not_found_in_trash'    => __('Not found in Trash'),
		'featured_image'        => __('Featured Image'),
		'set_featured_image'    => __('Set featured image'),
		'remove_featured_image' => __('Remove featured image'),
		'use_featured_image'    => __('Use as featured image'),
		'insert_into_item'      => __('Insert into item'),
		'uploaded_to_this_item' => __('Uploaded to this item'),
		'items_list'            => __('History list'),
		'items_list_navigation' => __('History list navigation'),
		'filter_items_list'     => __('Filter items list'),
	);
	$args = array(
		'label'                 => 'History',
		'description'           => 'History of all actions performed on StorePilot',
		'labels'                => $labels,
		'supports'              => array( 'title', 'author', 'custom-fields' ),
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
        'rest_base'             => 'history'
	);
	register_post_type( 'history', $args );
});

// Add to REST API
add_action( 'rest_api_init', function() {
  register_rest_field('history', 'shift', array(
    'get_callback'    => 'get_shift',
    'update_callback' => 'update_history',
    'schema' => array(
      'description' => __('Shift'),
	  'type'        => 'integer',
	)
  ));
});

add_filter( 'rest_history_query', function( $args, $request ) {
	$shift   = $request->get_param( 'shift' );
    if ( ! empty( $shift ) ) {
        $args['meta_query'] = array(
            array(
                'key'     => 'shift',
                'value'   => $shift,
                'compare' => '=',
            )
        );      
    }
    return $args;
}, 10, 2 );

// Get
function get_shift($obj)
{
  return get_post_meta($obj['id'], 'shift', true);
}

// Update
function update_history($value, $post, $key)
{
  $post_id = update_post_meta($post->ID, $key, $value);
  if (false === $post_id) {
    return new WP_Error(
      'rest_history_failed',
      __('Failed to update history.'),
      array('status' => 500)
    );
  }
  return true;
}