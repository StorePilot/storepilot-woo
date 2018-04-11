<?php
/**
 * REST API: SP_REST_Actions_Controller class
 *
 * @author   StorePilot
 * @category API
 * @package  StorePilot/API
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Action controller used for custom actions via the REST API.
 *
 * @since 1.0.0
 *
 * @see SP_REST_Posts_Controller
 */
class SP_REST_Actions_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/sp/v1';

	/**
	 * Register the routes for custom ordering.
	 */
	public function register_routes() {
    register_rest_route( $this->namespace, '/actions/order_product', array(
        array(
          'methods'             => WP_REST_Server::CREATABLE,
          'callback'            => array( $this, 'update_order' ),
          'permission_callback' => array( $this, 'update_product_permissions_check' ),
          'args'                => $this->get_ordering_collection_params()
        ))
    );
    register_rest_route( $this->namespace, '/actions/license', array(
        array(
          'methods'             => WP_REST_Server::READABLE,
          'callback'            => array( $this, 'get_license' ),
          'permission_callback' => array( $this, 'get_license_permissions_check' ),
          'args'                => array()
        ),
        array(
          'methods'             => WP_REST_Server::CREATABLE,
          'callback'            => array( $this, 'post_license' ),
          'permission_callback' => array( $this, 'post_license_permissions_check' ),
          'args'                => $this->get_license_collection_params()
        )
      )
    );
	}

	/**
	 * Checks if a given request has access to update posts.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function update_product_permissions_check( $request ) {

		$post_type = get_post_type_object( 'product' );

		if ( 'edit' === $request['context'] && ! current_user_can( $post_type->cap->edit_posts ) ) {
			return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to edit posts in this post type.' ), array( 'status' => rest_authorization_required_code() ) );
		}
    rest_send_cors_headers( null ); // Allow cors when dealing with actions

		return true;
	}

  /**
   * Checks if a given request has access to read posts.
   *
   * @since 1.0.0
   * @access public
   *
   * @param  WP_REST_Request $request Full details about the request.
   * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
   */
  public function get_license_permissions_check( $request ) {

    if ( ! current_user_can('edit_posts' ) ) {
      return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to read license code.' ), array( 'status' => rest_authorization_required_code() ) );
    }
    rest_send_cors_headers( null ); // Allow cors when dealing with actions

    return true;
  }

  /**
   * Checks if a given request has access to update posts.
   *
   * @since 1.0.0
   * @access public
   *
   * @param  WP_REST_Request $request Full details about the request.
   * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
   */
  public function post_license_permissions_check( $request ) {

    if ( ! current_user_can('manage_options' ) ) {
      return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to update license code.' ), array( 'status' => rest_authorization_required_code() ) );
    }
    rest_send_cors_headers( null ); // Allow cors when dealing with actions

    return true;
  }

  /**
   * Retrieves a collection of posts.
   *
   * @since 1.0.0
   * @access public
   *
   * @param WP_REST_Request $request Full details about the request.
   * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
   */
  public function update_order( $request ) {

    $q = $request->get_query_params();
    $id = $q['product_id'];
    $pre_id = $q['pre_id'];
    $next_id = $q['next_id'];

    // Ensure a search string is set in case the orderby is set to 'relevance'.
    if ( empty( $id ) ) {
      return new WP_Error( 'rest_missing_product_id', __( 'You need to define a product_id to be sorted.' ), array( 'status' => 400 ) );
    }

    // Ensure a search string is set in case the orderby is set to 'relevance'.
    if ( empty( $pre_id ) ) {
      return new WP_Error( 'rest_missing_prev_id', __( 'You need to define a product_id above product to be sorted.' ), array( 'status' => 400 ) );
    }

    // Ensure a search string is set in case the orderby is set to 'relevance'.
    if ( empty( $next_id ) ) {
      return new WP_Error( 'rest_missing_next_id', __( 'You need to define a product_id below product to be sorted.' ), array( 'status' => 400 ) );
    }

    $response = $this->custom_order_product($id, $pre_id, $next_id);

    return $response;
  }

  /**
   * Retrieves license code
   *
   * @since 1.0.0
   * @access public
   *
   * @param WP_REST_Request $request Full details about the request.
   * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
   */
  public function get_license( $request ) {

    global $sp_license;
    return wp_send_json( array(
      'license' => $sp_license->get_license(),
      'status' => $sp_license->get_license_status()
    ));

  }

  /**
   * Updates license code
   *
   * @since 1.0.0
   * @access public
   *
   * @param WP_REST_Request $request Full details about the request.
   * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
   */
  public function post_license( $request ) {

    $axios = $request->get_params(); // Expects axios request configuration
    $license_code = isset($axios['license']) ? $axios['license'] : '';
    $method = isset($axios['method']) ? $axios['method'] : '';

    global $sp_license;

    switch($method) {
      case 'validate':
        $response = $sp_license->storepilot_check_license($license_code);
        break;
      case 'activate':
        $response = $sp_license->storepilot_activate_license($license_code);
        break;
      case 'deactivate':
        $response = $sp_license->storepilot_deactivate_license($license_code);
        break;
      default:
        $response = null;
    }

    return wp_send_json($response);
  }

  /**
   * Retrieves the query params for collections of attachments.
   *
   * @since 1.0.0
   * @access public
   *
   * @return array Query parameters for the attachment collection as an array.
   */
  public function get_ordering_collection_params() {
    $params['status']['default'] = 'inherit';
    $params['status']['items']['enum'] = array( 'inherit', 'private', 'trash' );

    $params['product_id'] = array(
      'default'           => null,
      'description'       => __( 'Product to be ordered' ),
      'type'              => 'string'
    );

    $params['prev_id'] = array(
      'default'           => null,
      'description'       => __( 'Product above new position' ),
      'type'              => 'string'
    );

    $params['next_id'] = array(
      'default'           => null,
      'description'       => __( 'Product below new position' ),
      'type'              => 'string'
    );

    return $params;
  }

  /**
   * Retrieves the query params for collections of attachments.
   *
   * @since 1.0.0
   * @access public
   *
   * @return array Query parameters for the attachment collection as an array.
   */
  public function get_license_collection_params() {
    $params['status']['default'] = 'inherit';
    $params['status']['items']['enum'] = array( 'inherit', 'private', 'trash' );

    $params['license'] = array(
      'default'           => null,
      'description'       => __( 'License code' ),
      'type'              => 'string'
    );

    $params['method'] = array(
      'default'           => null,
      'description'       => __( 'Action to perform' ),
      'type'              => 'string'
    );

    return $params;
  }

	/**
	 * Ajax request handling for product ordering.
	 *
	 * Based on Simple Page Ordering by 10up (https://wordpress.org/plugins/simple-page-ordering/).
	 */
	public static function custom_order_product($id, $prev, $next) {
		global $wpdb;

		$sorting_id  = absint( $id );
		$previd      = absint( $prev !== 'false' ? $prev : 0 );
		$nextid      = absint( $next !== 'false' ? $next : 0 );
		$menu_orders = wp_list_pluck( $wpdb->get_results( "SELECT ID, menu_order FROM {$wpdb->posts} WHERE post_type = 'product' ORDER BY menu_order ASC, post_title ASC" ), 'menu_order', 'ID' );
		$index       = 0;

		foreach ( $menu_orders as $id => $menu_order ) {
			$id = absint( $id );

			if ( $sorting_id === $id ) {
				continue;
			}
			if ( $nextid === $id ) {
				$index ++;
			}
			$index ++;
			$menu_orders[ $id ] = $index;
			$wpdb->update( $wpdb->posts, array( 'menu_order' => $index ), array( 'ID' => $id ) );

			/**
			 * When a single product has gotten it's ordering updated.
			 * $id The product ID
			 * $index The new menu order
			 */
			do_action( 'storepilot_after_single_product_ordering', $id, $index );
		}

		if ( isset( $menu_orders[ $previd ] ) ) {
			$menu_orders[ $sorting_id ] = $menu_orders[ $previd ] + 1;
		} elseif ( isset( $menu_orders[ $nextid ] ) ) {
			$menu_orders[ $sorting_id ] = $menu_orders[ $nextid ] - 1;
		} else {
			$menu_orders[ $sorting_id ] = 0;
		}

		$wpdb->update( $wpdb->posts, array( 'menu_order' => $menu_orders[ $sorting_id ] ), array( 'ID' => $sorting_id ) );

		do_action( 'storepilot_after_product_ordering' );
		wp_send_json( $menu_orders );
	}

}
