<?php
/**
 * REST API: SP_REST_Sync_Controller class
 *
 * @author   StorePilot
 * @category API
 * @package  StorePilot/API
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SP_REST_Sync_Controller {

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
    register_rest_route( $this->namespace, '/sync/any', array(
        array(
          'methods'             => WP_REST_Server::CREATABLE,
          'callback'            => array( $this, 'handler' ),
          'permission_callback' => array( $this, 'permissions_check' ),
          'args'                => $this->get_ordering_collection_params()
        ))
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
	public function permissions_check( $request ) {

		$post_type = get_post_type_object( 'product' );

		if ( 'edit' === $request['context'] && ! current_user_can( $post_type->cap->edit_posts ) ) {
			return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to edit posts in this post type.' ), array( 'status' => rest_authorization_required_code() ) );
		}
    rest_send_cors_headers( null ); // Allow cors when dealing with sync

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
  public function handler( $request ) {

    $q = $request->get_query_params();
    $action = $q['action'];
    $content = $q['content'];
    $machine = $q['machine'];
    $fingerprint = $q['fingerprint'];

		if ($action === 'capabilities') {
			return $this->capabilities($content, $machine, $fingerprint);
		}

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

    $params['action'] = array(
      'default'           => null,
      'description'       => __( 'Type of function to execute' ),
      'type'              => 'string'
    );

    $params['fingerprint'] = array(
      'default'           => null,
      'description'       => __( 'Machine UUID' ),
      'type'              => 'string'
    );

    $params['machine'] = array(
      'default'           => null,
      'description'       => __( 'Machine name' ),
      'type'              => 'string'
    );

    $params['content'] = array(
      'default'           => null,
      'description'       => __( 'Data to process' ),
      'type'              => 'string'
    );

    return $params;
  }

	public static function capabilities($content, $machine, $fingerprint) {
    
    $capabilities = json_decode(get_option('storepilot_capabilities'), true);
    if (!$capabilities) {
      $capabilities = [
        'default' => [
          'dashboard' => true,
          'products' => true,
          'customers' => true,
          'orders' => true,
          'pos' => true,
          'settings' => true
        ],
        'machines' => []
      ];
    }
    // Convert default to valid true / false
    foreach($capabilities['default'] as $key => $val) {
      if ($val === 'true') {
        $capabilities['default'][$key] = true;
      } else if ($val === 'false') {
        $capabilities['default'][$key] = false;
      }
    }
    if ($fingerprint) {
      $t = [];
      // Convert machine to valid true / false
      foreach($capabilities['machines'][$fingerprint]['capabilities'] as $key => $val) {
        if ($val === 'true') {
          $capabilities['machines'][$fingerprint]['capabilities'][$key] = true;
        } else if ($val === 'false') {
          $capabilities['machines'][$fingerprint]['capabilities'][$key] = false;
        }
      }
      if (!$capabilities['machines'][$fingerprint]) {
        $capabilities['machines'][$fingerprint] = [
          'capabilities' => $capabilities['default']
        ];
      }
      $capabilities['machines'][$fingerprint]['machine'] = $machine;
      update_option('storepilot_capabilities', json_encode($capabilities));
      wp_send_json( $capabilities['machines'][$fingerprint] );
    } else {
      update_option('storepilot_capabilities', json_encode($capabilities));
      wp_send_json( $capabilities['default'] );
    }
	}

}
