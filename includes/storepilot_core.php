<?php
/**
 * @link https://gitlab.com/storepilot/woocommerce
 * @package StorePilot
 * @category Core
 * @author StorePilot
 */

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

if (!class_exists('StorePilotCore')) :

  /**
   * Main StorePilot Class
   *
   * @class StorePilot
   * @since 1.0.0
   */
  abstract class StorePilotCore
  {

    /**
     * The single instance of the class
     * @var StorePilot
     * @since 1.0.0
     */
    protected static $_instance = null;

    private $message = '';

    /**
     * Cloning is forbidden
     * @since 1.0.0
     */
    public function __clone()
    {
      return false;
    }

    /**
     * Unserializing instances of this class is forbidden
     * @since 1.0.0
     */
    public function __wakeup()
    {
      return false;
    }

    /**
     * StorePilot Constructor
     */
    public function __construct()
    {
      do_action('before_storepilot_constructed');
      $this->init();
      do_action('after_storepilot_constructed');
    }

    /**
     * Init StorePilot
     */
    public function init()
    {
      do_action('before_storepilot_init');

      // Init hooks
      try {
        $this->init_hooks();
      } catch (Throwable $e) {
        $this->message = $e->getMessage();
        add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
      }

      do_action('after_storepilot_init');
    }

    /**
     * Hook into actions and filters
     * @since  1.0.0
     */
    private function init_hooks()
    {
      register_activation_hook(__FILE__, array('SP_Install', 'install'));
      add_action('plugins_loaded', array($this, 'load_textdomain'));
      add_action('rest_api_init', array($this, 'add_additional_rest_fields'));
      add_filter('woocommerce_loaded', array($this, 'extend_rest_api'));
      add_filter('woocommerce_sale_flash', array($this, 'sale_flash'));
      add_filter('woocommerce_rest_is_request_to_rest_api', '__return_true');
      $this->register_settings();
    }

    public function register_settings()
    {
      $settings = [
        'storepilot_invoice_company',
        'storepilot_invoice_email',
        'storepilot_invoice_custom_text',
        'storepilot_logo',
        'storepilot_invoice_phone',
        'storepilot_invoice_address',
        'storepilot_invoice_zip',
        'storepilot_invoice_city',
        'storepilot_invoice_country',
        'storepilot_invoice_bankaccount',
        'storepilot_invoice_organization_number',
        'storepilot_invoice_logo',
        'storepilot_stripe_secret',
        'storepilot_barcode_meta',
        'storepilot_tax_state',
        'storepilot_sumup_affiliate_key',
        'storepilot_paypal_here_client_id',
        'storepilot_paypal_here_client_secret',
        'storepilot_vipps_merchant_id',
        'storepilot_vipps_client_id',
        'storepilot_vipps_client_secret',
        'storepilot_vipps_subscription_key',
        'storepilot_nets_username',
        'storepilot_nets_password'
      ];
      foreach($settings as $setting) {
        register_setting( 'general', $setting, array(
          'type' => 'string',
          'show_in_rest' => 'true'
        ));
      }
    }

    public function extend_rest_api()
    {
      /**
       * Rest Api Extensions
       */
      WC()->api->rest_api_includes();
      include_once(plugin_dir_path(__FILE__) . 'api/rest-api-extension.php');
      include_once(plugin_dir_path(__FILE__) . 'api/rest-filter.php');
      $this->api = new SP_API();
    }

    public function add_settings_pages() {
      require_once(__DIR__ . '/settings.php');
      require_once(__DIR__ . '/fields.php');
    }

    function load_textdomain() {
      $domain = 'storepilot';
      load_plugin_textdomain( $domain, false, dirname( plugin_basename( __FILE__ ) ) . '/../languages/' );
      $this->add_settings_pages();
    }

    public function sale_flash($content)
    {
      global $product;
      foreach ($product->get_meta_data() as $meta) {
        if ($meta->key == 'sp_custom_sale_flash_label' && $meta->value !== '') {
          $content = $meta->value;
        }
      }
      return '<span class="onsale">' . $content . '</span>';
    }

    /**
     * Check if woocommerce is larger or equal to 3.0.8 and is Active
     * @return bool
     */
    public function has_woocommerce() {
      // Dependencies
      $woocommerce = in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) ? true : false;
      // Set false if wc version is less than 3.0.8
      if ($woocommerce && $this->get_woocommerce_version() !== NULL) {
        $wc_version = explode(".", $this->get_woocommerce_version());
        if (intval($wc_version[0]) < 3 || (intval($wc_version[0]) == 3 && intval($wc_version[1]) < 1 && intval($wc_version[2]) < 8)) {
          $woocommerce = false;
        }
      }
      return $woocommerce;
    }

    /**
     * Get WooCommerce Version
     */
    public function get_woocommerce_version() {
      // If get_plugins() isn't available, require it
      if (!function_exists('get_plugins')) {
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
      }

      // Create the plugins folder and file variables
      $plugin_folder = get_plugins('/' . 'woocommerce');
      $plugin_file = 'woocommerce.php';

      // If the plugin version number is set, return it
      if (isset($plugin_folder[$plugin_file]['Version'])) {
        return $plugin_folder[$plugin_file]['Version'];
      } else {
        // Otherwise return null
        return NULL;
      }
    }

    /**
     * Get additional image sizes for product images
     */
    public function add_additional_rest_fields() {

      function get_price_range($object)
      {
        $price_range = [];
        $price_range['min'] = (float)$object['price'];
        $price_range['max'] = (float)$object['price'];
        $price_range['all'] = [];
        $price_range['debug'] = [];
        $product = wc_get_product($object['id']);
        if ($product->is_type('variable')) {
          foreach ($product->get_available_variations() as $variation) {
            $price = (float)$variation['display_price'];
            $price_range['debug'][] = $variation;
            $price_range['all'][] = $price;
            if ($variation['display_price'] != '' && $price < $price_range['min']) {
              $price_range['min'] = $price;
            }
            if ($variation['display_price'] != '' && $price > $price_range['max']) {
              $price_range['max'] = $price;
            }
          }
        }
        return $price_range;
      }

      function get_image_src($object)
      {
        if (isset($object['image']['id'])) {
          $object['image']['src_thumbnail'] = wp_get_attachment_image_src($object['image']['id'], 'thumbnail')[0];
          $object['image']['src_shop_thumbnail'] = wp_get_attachment_image_src($object['image']['id'], 'shop_thumbnail')[0];
          $object['image']['src_medium'] = wp_get_attachment_image_src($object['image']['id'], 'medium')[0];
          $object['image']['src_large'] = wp_get_attachment_image_src($object['image']['id'], 'large')[0];
        }
        if (isset($object['image']['src']) && $object['image']['src_thumbnail'] === null) {
          $object['image']['src_thumbnail'] = $object['image']['src'];
        }
        if (isset($object['image']['src']) && $object['image']['src_shop_thumbnail'] === null) {
          $object['image']['src_shop_thumbnail'] = $object['image']['src'];
        }
        if (isset($object['image']['src']) && $object['image']['src_medium'] === null) {
          $object['image']['src_medium'] = $object['image']['src'];
        }
        if (isset($object['image']['src']) && $object['image']['src_large'] === null) {
          $object['image']['src_large'] = $object['image']['src'];
        }
        return $object['image'];
      }

      /** Get unrendered content START **/
      /** @see - https://github.com/woocommerce/woocommerce/issues/16895 @todo - It has now been fixed for products **/
      register_rest_field('product', 'description_raw', array('get_callback' => array($this, 'get_description_raw')));
      register_rest_field('product', 'short_description_raw', array('get_callback' => array($this, 'get_short_description_raw')));
      register_rest_field('product', 'purchase_note_raw', array('get_callback' => array($this, 'get_purchase_note_raw')));
      register_rest_field('product_variation', 'description_raw', array('get_callback' => array($this, 'get_variation_description_raw')));
      register_rest_field('product_cat', 'description_raw', array('get_callback' => array($this, 'get_category_description_raw')));
      /** Get unrendered content END **/

      register_rest_field('system_status', 'prices_include_tax', array('get_callback' => array($this, 'get_prices_include_tax')));
      register_rest_field('product', 'price_range', array('get_callback' => 'get_price_range'));
      register_rest_field('product', 'images', array('get_callback' => array($this, 'get_images_src')));
      register_rest_field('product_variation', 'image', array('get_callback' => 'get_image_src'));
      register_rest_field('product_cat', 'image', array('get_callback' => 'get_image_src'));

    }

    public function get_description_raw($object) {
      if (is_array($object)) {
        $product = wc_get_product($object['id']);
      } else {
        $product = $object;
      }
      return $product->get_description();
    }

    public function get_short_description_raw($object) {
      if (is_array($object)) {
        $product = wc_get_product($object['id']);
      } else {
        $product = $object;
      }
      return $product->get_short_description();
    }

    public function get_purchase_note_raw($object) {
      if (is_array($object)) {
        $product = wc_get_product($object['id']);
      } else {
        $product = $object;
      }
      return $product->get_purchase_note();
    }

    public function get_variation_description_raw($object) {
      return $object['description'];
    }

    public function get_category_description_raw($object) {
      return category_description($object['id']);
    }

    public function get_images_src($object) {
      $updated = [];
      foreach ($object['images'] as $image) {
        $image['src_thumbnail'] = wp_get_attachment_image_src($image['id'], 'thumbnail')[0];
        $image['src_shop_thumbnail'] = wp_get_attachment_image_src($image['id'], 'shop_thumbnail')[0];
        $image['src_medium'] = wp_get_attachment_image_src($image['id'], 'medium')[0];
        $image['src_large'] = wp_get_attachment_image_src($image['id'], 'large')[0];
        if ($image['src_thumbnail'] === null) {
          $image['src_thumbnail'] = $image['src'];
        }
        if ($image['src_shop_thumbnail'] === null) {
          $image['src_shop_thumbnail'] = $image['src'];
        }
        if ($image['src_medium'] === null) {
          $image['src_medium'] = $image['src'];
        }
        if ($image['src_large'] === null) {
          $image['src_large'] = $image['src'];
        }
        $updated[] = $image;
      }
      return $updated;
    }

    public function get_prices_include_tax()
    {
      return wc_prices_include_tax();
    }

    public function admin_warning() {
  
      $message = sprintf(
        esc_html__( 'StorePilot can not load, ensure you have the latest version installed', 'storepilot' ),
        '<strong>' . esc_html__( $this->message, 'storepilot' ) . '</strong>'
      );
  
      printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
  
    }
  

  }

endif;
