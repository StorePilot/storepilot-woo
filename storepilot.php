<?php
/**
 * Plugin Name: StorePilot (Dev)
 * Plugin URI: https://storepilot.com
 * Description: StorePilot is a management tool specialized to suit your selling and warehouse needs.
 * Version: dev
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

  /**
   * StorePilot version -  Used for Licensing
   * @var string
   */
  define('SP_FILE', __FILE__ );
  $package = file_get_contents(plugin_dir_path(__FILE__) . './package.json');
  $version = json_decode($package, true)['version'];

  include_once(plugin_dir_path(__FILE__) . 'includes/i18n/storepilot_core_i18n.php');

  final class StorePilot extends StorePilotCoreTranslated
  {

    public $dev_client = "http://localhost:9080/";

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
      // print_r(wp_print_styles()); To see enqueued styles
      $has_woocommerce = $this->has_woocommerce() ? 'true' : 'false';
      $pretty_permalinks = get_option('permalink_structure') ? 'true' : 'false';

      // Load dev js
      wp_register_script('storepilot_dev_js', plugins_url('includes/dev/dev.js', __FILE__), array(), $this->version, true);

      // Localize variables
      wp_localize_script('storepilot_dev_js', 'hasWoocommerce', $has_woocommerce);
      wp_localize_script('storepilot_dev_js', 'prettyPermalinks', $pretty_permalinks);
      wp_localize_script('storepilot_dev_js', 'i18n', $this->translation());
      if ($this->has_woocommerce() && get_option('permalink_structure')) {
        wp_localize_script('storepilot_dev_js', 'preload', $this->preload());
      }

      // Enqueue
      wp_enqueue_script('storepilot_dev_js');

      // Styles
      wp_enqueue_style('storepilot_dev_css', plugins_url('includes/dev/dev.css', __FILE__), array(), $this->version);
      wp_enqueue_style('storepilot_css_overwrite', plugins_url('includes/style/overwrite.css', __FILE__), array(), $this->version);

      ?>
      <iframe
        allowfullscreen
        name="hotreload"
        id="hotreload"
        style="width: 100%; flex: 1 1 auto;"
        src="<?php echo $this->dev_client ?>?dev_server=<?php echo get_site_url() ?>"
        onload="if (location.hash!=='') { this.src=this.src + location.hash; }">
      </iframe>
      <style type="text/css">

      </style>
      <?php

      $this->allow_media_manager($this->dev_client);
    }

    public function environment_fill()
    {
      wp_enqueue_style('storepilot_css_fill', plugins_url('includes/style/fill.css', __FILE__), array(), $this->version);
      $this->environment();
    }

  }

  /**
   * Main instance of StorePilot
   *
   * Returns the main instance of SP to prevent the need to use globals
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
