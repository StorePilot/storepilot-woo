<?php
/**
 * Plugin Name: StorePilot
 * Plugin URI: https://storepilot.com
 * Description: StorePilot is a management tool specialized to suit your selling and warehouse needs.
 * Version: SP_VERSION_REPLACE
 * Author: StorePilot AS
 * Author URI: https://storepilot.com
 * Requires at least: 4.4
 * Tested up to: 4.9
 *
 * Text Domain: storepilot
 * Domain Path: /includes/languages/
 *
 * Copyright: © 2018 StorePilot AS
 * License: StorePilot
 * License URI: https://storepilot.com/license/
 *
 * @link https://storepilot.com
 * @package StorePilot
 * @category Management
 * @author StorePilot AS
 */

if (defined('ABSPATH') && !class_exists('StorePilot')) {

  define('SP_FILE', __FILE__ );
	$version = get_file_data( __FILE__ , array('Version'), 'plugin')[0];

  include_once(plugin_dir_path(__FILE__) . 'includes/i18n/storepilot_core_i18n.php');

  final class StorePilot extends StorePilotCoreTranslated
  {

    /**
     * Main StorePilot Instance
     *
     * Ensures only one instance of StorePilot is loaded or can be loaded
     *
     * @since 1.0.0
     * @static
     * @see SP()
     * @return StorePilot - Main instance
     */
    public static function instance($version)
    {
      if (is_null(self::$_instance)) {
        self::$_instance = new self($version);
      }
      return self::$_instance;
    }

    public function environment()
    {
      // Dependencies
      $has_woocommerce = $this->has_woocommerce() ? 'true' : 'false';
      $pretty_permalinks = get_option('permalink_structure') ? 'true' : 'false';

      // Wordpress system information
      echo '<div id="storepilot-system" permalinks="' . $pretty_permalinks . '" woocommerce="' . $has_woocommerce . '"></div>';

      // Selector
      echo '<div id="storepilot"></div>';

      // Styles
      wp_enqueue_style('storepilot_css', plugins_url('app/styles.css', __FILE__), array(), $this->version);
      wp_enqueue_style('storepilot_css_overwrite', plugins_url('includes/style/overwrite.css', __FILE__), array(), $this->version);

      // Javascript
      wp_enqueue_script('storepilot_js', plugins_url('app/web.js', __FILE__), array(), $this->version, true);

      wp_localize_script('storepilot_js', 'wcApiSettings', array(
          'root' => esc_url_raw(rest_url()),
          'nonce' => wp_create_nonce('wp_rest')
      ));
      wp_localize_script('storepilot_js', 'i18n', $this->translation());
      if ($this->has_woocommerce() && get_option('permalink_structure')) {
        wp_localize_script('storepilot_js', 'preload', $this->preload());
      }

      // Enqueue app
      wp_enqueue_script('storepilot_js', plugins_url('app/web.js', __FILE__), array(), $this->version, true);

      $this->allow_media_manager();
    }

    public function environment_fill()
    {
      wp_enqueue_style('storepilot_css_fill', plugins_url('includes/style/fill.css', __FILE__), array(), $this->version);
      $this->environment();
    }

  }

  /**
   * Main instance of StorePilot.
   *
   * Returns the main instance of SP to prevent the need to use globals.
   *
   * @since  1.0.0
   * @return StorePilot
   */
  function SP($version)
  {
    return StorePilot::instance($version);
  }

  // Fire Constructor
  $SP = SP($version);

} else {
  exit; // Exit if accessed directly or SP already defined
}
