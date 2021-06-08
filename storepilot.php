<?php
/**
 * Plugin Name: StorePilot
 * Plugin URI: https://storepilot.com
 * Description: StorePilot is a product management application, making WooCoomerce available from the Desktop.
 * Version: 1.2.9
 * Author: StorePilot AS
 * Requires at least: 4.4
 * Tested up to: 5.7
 *
 * Text Domain: storepilot
 * Domain Path: /languages
 *
 * Copyright: © 2018 - 2021 StorePilot AS
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.html
 *
 * @link https://storepilot.com
 * @package StorePilot
 * @category Management
 * @author StorePilot AS
 */

if (defined('ABSPATH') && !class_exists('StorePilot')) {

	include_once(plugin_dir_path(__FILE__) . 'includes/storepilot_core.php');

  /**
   * StorePilot version -  Used for Licensing
   * @var string
   */
  define('SP_FILE', __FILE__ );

  final class StorePilot extends StorePilotCore
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
    public static function instance()
    {
      if (is_null(self::$_instance)) {
        self::$_instance = new self();
      }
      return self::$_instance;
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
  function SP()
  {
    return StorePilot::instance();
  }

  // Fire Constructor
  $SP = SP();

} else {
  exit; // Exit if accessed directly or SP already defined
}
