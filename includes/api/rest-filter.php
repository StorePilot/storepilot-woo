<?php
/**
 * StorePilot REST API Filter Extension
 *
 * @author   StorePilot
 * @category API
 * @package  StorePilot/API
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'rest_api_init', 'wc_rest_api_filter_support' );

function wc_rest_api_filter_support() {
	foreach ( get_post_types( array( 'show_in_rest' => true ), 'objects' ) as $post_type ) {
		add_filter( 'woocommerce_rest_' . $post_type->name . '_object_query', 'wc_rest_api_filter_support_addon', 10, 2 );
	}
}

/**
 * @param array $args The query arguments
 * @param WP_REST_Request $request Full details about the request
 * @return array $args
 **/
function wc_rest_api_filter_support_addon( $args, $request ) {
	if ( empty( $request['filter'] ) || ! is_array( $request['filter'] ) ) {
		return $args;
	}
	$filter = $request['filter'];
	global $wp;
	$vars = apply_filters( 'woocommerce_rest_query_vars', $wp->public_query_vars );
	function allow_meta_query( $valid_vars ) {
		$valid_vars = array_merge( $valid_vars, array( 'meta_query', 'meta_key', 'meta_value', 'meta_compare' ) );
		return $valid_vars;
	}
	$vars = allow_meta_query( $vars );
	foreach ( $vars as $var ) {
		if ( isset( $filter[ $var ] ) ) {
			$args[ $var ] = $filter[ $var ];
		}
	}
	return $args;
}
