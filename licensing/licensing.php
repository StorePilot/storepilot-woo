<?php
/**
 * StorePilot Licensing Functionality
 *
 * Handles Licensing
 *
 * @author   StorePilot
 * @category Licensing
 * @package  StorePilot/Licensing
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SP_LICENSING {
  
  /**
	 * Setup class
	 * @since 1.0.0
	 */
	public function __construct($product_name, $master_url, $version, $beta, $require_ssl, $author) {
		$this->license_init();
	  $this->product_name = $product_name;
	  $this->master_url = $master_url;
		$this->version = $version;
	  $this->beta = $beta;
	  $this->require_ssl = $require_ssl;
	  $this->author = $author;
	}

	/**
	 * Init Licensing
	 * @since 1.0.0
	 */
	private function license_init() {

		$this->license_includes();
		
	  add_action( 'admin_init', array( $this, 'storepilot_plugin_updater' ), 0 );
	  add_action( 'admin_init', array( $this, 'storepilot_register_option' ) );
	  add_action( 'admin_notices', array( $this, 'storepilot_admin_notices' ) );

	}

	/**
	 * Include Licensing classes
	 * @since 1.0.0
	 */
	private function license_includes() {
	  
	  include_once( dirname( __FILE__ ) . '/updater.php' );

	}

	/**
	 * Activate Updater
   * @since 1.0.0
	 */
	function storepilot_plugin_updater() {

		// retrieve our license key from the DB
		$license_key = trim( get_option( 'storepilot_license_key' ) );

		// setup the updater
		new SP_PluginUpdater( $this->master_url, SP_FILE, array(
				'version' 	=> $this->version,
				'license' 	=> $license_key,
				'item_name' => $this->product_name,
				'author'  	=> $this->author,
				'beta'		  => $this->beta
			)
		);

	}

	/**
	 * Store License in Wordpress
   * @since 1.0.0
	 */
	function storepilot_register_option() {
		// creates our settings in the options table
    add_option('storepilot_license_key', '' );
    add_option('storepilot_license_status', '' );
	}

	/**
	 * Sanitize License
   * @since 1.0.0
	 */
	function storepilot_sanitize_license( $new ) {
		$old = get_option( 'storepilot_license_key' );
		if( $old && $old != $new ) {
			delete_option( 'storepilot_license_status' ); // new license has been entered, so must reactivate
		}
		return $new;
	}

	/**
	 * Activate License
   * @since 1.0.0
	 */
	function storepilot_activate_license($license = '') {

    $license = trim( $license );

    // data to send in our API request
    $api_params = array(
      'edd_action' => 'activate_license',
      'license'    => $license,
      'item_name'  => urlencode( $this->product_name ), // the name of our product in EDD
      'url'        => home_url()
    );

    // Call the custom API
    $response = wp_remote_post( $this->master_url, array( 'timeout' => 15, 'sslverify' => $this->require_ssl, 'body' => $api_params ) );

    // make sure the response came back okay
    if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

      if ( is_wp_error( $response ) ) {
        $message = $response->get_error_message();
      } else {
        $message = __( 'An error occurred, please try again.' );
      }

    } else {

      $license_data = json_decode( wp_remote_retrieve_body( $response ) );

      if ( isset($license_data->success) && false === $license_data->success ) {

        switch( $license_data->error ) {

          case 'expired' :

            $message = sprintf(
              __( 'Your license key expired on %s.' ),
              date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
            );
            break;

          case 'revoked' :

            $message = __( 'Your license key has been disabled.' );
            break;

          case 'missing' :

            $message = __( 'Invalid license.' );
            break;

          case 'invalid' :
          case 'site_inactive' :

            $message = __( 'Your license is not active for this URL.' );
            break;

          case 'item_name_mismatch' :

            $message = sprintf( __( 'This appears to be an invalid license key for %s.' ), $this->product_name );
            break;

          case 'no_activations_left':

            $message = __( 'Your license key has reached its activation limit.' );
            break;

          default :

            $message = __( 'An error occurred, please try again.' );
            break;
        }

      }

    }

    update_option( 'storepilot_license_key', $license );
    // Check if anything passed on a message constituting a failure
    if ( ! empty( $message ) ) {
      return [
	        'error' => true,
          'success' => false,
          'message' => $message
      ];
    } else {
      $success = false;
      if (isset($license_data->license) && $license_data->license == 'valid') {
	      $success = true;
      }
      update_option( 'storepilot_license_status', $success ? 'valid' : 'invalid' );
      return [
	        'error' => false,
          'success' => $success,
          'message' => isset($license_data->license) ? $license_data->license : 'invalid' // 'valid' / 'invalid'
      ];
    }
	}

	/**
	 * Deactivate License
   * @since 1.0.0
	 */
	function storepilot_deactivate_license($license = '') {

    // retrieve the license from the database
    $license = trim( $license );

    // data to send in our API request
    $api_params = array(
      'edd_action' => 'deactivate_license',
      'license'    => $license,
      'item_name'  => urlencode( $this->product_name ), // the name of our product in EDD
      'url'        => home_url()
    );

    // Call the custom API
    $response = wp_remote_post( $this->master_url, array( 'timeout' => 15, 'sslverify' => $this->require_ssl, 'body' => $api_params ) );

    // make sure the response came back okay
    if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

      if ( is_wp_error( $response ) ) {
        $message = $response->get_error_message();
      } else {
        $message = __( 'An error occurred, please try again.' );
      }
	    return [
	      'error' => true,
		    'success' => false,
		    'message' => $message
	    ];

    } else {

	    // decode the license data
	    $license_data = json_decode( wp_remote_retrieve_body( $response ) );
	    $success = false;
	    if( isset($license_data->license) && $license_data->license == 'deactivated' ) {
        delete_option( 'storepilot_license_status' );
        delete_option( 'storepilot_license_key' );
	      $success = true;
	    }
	    return [
	      'error' => false,
		    'success' => $success,
		    'message' => isset($license_data->license) ? $license_data->license : 'failed' // 'deactivated' / 'failed'
	    ];

    }
	}

	/**
	 * Get License
	 * @since 1.0.0
	 */
	function get_license() {
		return get_option( 'storepilot_license_key' );
	}

	/**
	 * Get License Status
	 * @since 1.0.0
	 */
	function get_license_status() {
		return get_option( 'storepilot_license_status' );
	}

	/**
	 * Validate License
	 * @since 1.0.0
	 */
	function storepilot_check_license($license = '') {

		global $wp_version;

		$license = trim( $license );

		$api_params = array(
			'edd_action' => 'check_license',
			'license' => $license,
			'item_name' => urlencode( $this->product_name ),
			'url'       => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( $this->master_url, array( 'timeout' => 15, 'sslverify' => $this->require_ssl, 'body' => $api_params ) );

		if ( is_wp_error( $response ) ) {
			return [
				'error' => true,
				'success' => false,
				'message' => $response
			];
		} else {
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			return [
				'error' => false,
				'success' => true,
				'message' => isset($license_data->license) ? $license_data->license : 'invalid' // 'valid' / 'invalid'
			];
		}

	}

	/**
	 * Activation Notice
	 * @since 1.0.0
	 */
	function storepilot_admin_notices() {
		if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {

			switch( $_GET['sl_activation'] ) {

				case 'false':
					$message = urldecode( $_GET['message'] );
					?>
                  <div class="error">
                    <p><?php echo $message; ?></p>
                  </div>
					<?php
					break;

				case 'true':
					$message = urldecode( $_GET['message'] );
					?>
                  <div class="success">
                    <p><?php echo $message; ?></p>
                  </div>
					<?php
					break;
				default:
					break;

			}
		}
	}

}