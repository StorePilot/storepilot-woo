<?php
/**
 * WooCommerce REST API Extended controller
 *
 * Handles requests to the StorePilot RestApi
 *
 * @author   StorePilot
 * @category API
 * @package  StorePilot/API
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SP_API extends WC_API {

	/**
	 * Setup class.
	 * @since 1.0.0
	 */
	function __construct() {

		$this->rest_api_init();

	}

	/**
	 * Init SP REST API.
	 * @since 1.0.0
	 */
	function rest_api_init() {

		$this->rest_api_includes();
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ), 10 );

	}

	/**
	 * Include REST API classes.
	 *
	 * @since 1.0.0
	 */
	function rest_api_includes() {

		// REST API v1 controllers
		include_once( dirname( __FILE__ ) . '/controllers/class-sp-rest-sync-controller.php' );
		include_once( dirname( __FILE__ ) . '/controllers/class-sp-rest-actions-controller.php' );
		include_once( dirname( __FILE__ ) . '/controllers/class-sp-rest-translation-controller.php' );
		include_once( dirname( __FILE__ ) . '/controllers/class-sp-rest-attachments-controller.php' );

	}

	/**
	 * Register REST API routes.
	 * @since 1.0.0
	 */
	function register_rest_routes() {

		$controllers = array(

			// REST API v1 controllers
			'SP_REST_Sync_Controller',
			'SP_REST_Actions_Controller',
			'SP_REST_Translation_Controller',
			'SP_REST_Attachments_Controller'

		);

		foreach ( $controllers as $controller ) {
			$this->$controller = new $controller();
			$this->$controller->register_routes();
		}

	}

}