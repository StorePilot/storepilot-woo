<?php
/**
 * REST API: SP_REST_Actions_Controller class
 *
 * @author   StorePilot
 * @category API
 * @package  StorePilot/API
 * @since    1.1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Translation controller used to provide i18n via the REST API.
 *
 * @since 1.0.0
 *
 * @see SP_REST_Posts_Controller
 */
class SP_REST_Translation_Controller {

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
    register_rest_route( $this->namespace, '/translation', array(
        array(
          'methods'             => WP_REST_Server::READABLE,
          'callback'            => array( $this, 'get_translation' ),
          'permission_callback' => array( $this, 'get_translation_permissions_check' ),
          'args'                => $this->get_params()
        ))
    );
	}

	/**
	 * Checks if a given request has access read.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_translation_permissions_check( $request ) {

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
  public function get_translation( $request ) {

    $q = $request->get_query_params();
    $locale = $q['locale'];

    // Change locale if given.
    if ( !empty( $locale ) ) {
			switch_to_locale($locale);
		}

    return wp_send_json([
			[
				key => "test",
				value => __("test", "storepilot")
			],
			[
				key => "test2",
				value => __("test2", "storepilot")
			],
			[
				key => "Price",
				value => __("Price", "woocommerce")
			]
		]);
  }

  /**
   * Retrieves the query params for collections of attachments.
   *
   * @since 1.0.0
   * @access public
   *
   * @return array Query parameters for the attachment collection as an array.
   */
  public function get_params() {
    $params['status']['default'] = 'inherit';
    $params['status']['items']['enum'] = array( 'inherit', 'private', 'trash' );

    $params['locale'] = array(
      'default'           => null,
      'description'       => __( 'Language code to retrieve translation' ),
      'type'              => 'string'
    );

    return $params;
  }

}
