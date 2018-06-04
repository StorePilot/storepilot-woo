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
   * @version SP_VERSION_REPLACE
   */
  abstract class StorePilotCore
  {

    /**
     * Product Name (ID) - Used for Licensing
     * @var string
     */
    public $product_name = 'StorePilot';

    /**
     * Master Url - Used for Licensing
     * @var string
     */
    public $master_url = 'https://storepilot.com';

    /**
     * Require authentication through SSL -  Used for Licensing
     * @var bool
     */
    public $require_ssl = false;

    /**
     * StorePilot version -  Used for Licensing
     * @var string
     */
    public $version = '1.0.0';

    /**
     * Beta -  Used for Licensing
     * @var bool
     */
    public $beta = false;

    /**
     * Author -  Used for Licensing
     * @var bool
     */
    public $author = 'StorePilot AS';

    /**
     * The single instance of the class
     * @var StorePilot
     * @since 1.0.0
     */
    protected static $_instance = null;

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
    public function __construct($version = '1.0.0')
    {
      $this->version = $version;
      do_action('before_storepilot_constructed');
      if ($this->has_woocommerce() && get_option('permalink_structure')) {
        $this->includes();
      }
      $this->init();
      do_action('after_storepilot_constructed');
    }

    /**
     * Include required core files used in admin and on the frontend
     */
    public function includes()
    {
      /**
       * Licensing
       */
      include_once(plugin_dir_path(__FILE__) . '../licensing/licensing.php');
      global $sp_license;
      $this->licensing = $sp_license = new SP_LICENSING(
          $this->product_name,
          $this->master_url,
          $this->version,
          $this->beta,
          $this->require_ssl,
          $this->author
      );
    }

    /**
     * Init StorePilot
     */
    public function init()
    {
      do_action('before_storepilot_init');

      // Init hooks
      $this->init_hooks();

      do_action('after_storepilot_init');
    }

    /**
     * Hook into actions and filters
     * @since  1.0.0
     */
    private function init_hooks()
    {
      register_activation_hook(__FILE__, array('SP_Install', 'install'));
      add_action('rest_api_init', array($this, 'add_additional_rest_fields'));
      add_filter('woocommerce_loaded', array($this, 'extend_rest_api'));
      add_filter('woocommerce_sale_flash', array($this, 'sale_flash'));
    }

    public function extend_rest_api()
    {
      /**
       * Rest Api Extensions
       */
      include_once(plugin_dir_path(__FILE__) . 'api/rest-api-extension.php');
      include_once(plugin_dir_path(__FILE__) . 'api/rest-filter.php');
      $this->api = new SP_API();
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
    function has_woocommerce()
    {
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
    function get_woocommerce_version()
    {
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
    function add_additional_rest_fields()
    {

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

      register_rest_field('product', 'price_range', array('get_callback' => 'get_price_range'));
      register_rest_field('product', 'cross_sell', array('get_callback' => array($this, 'get_cross_sell')));
      register_rest_field('product', 'upsell', array('get_callback' => array($this, 'get_upsell')));
      register_rest_field('product', 'images', array('get_callback' => array($this, 'get_images_src')));
      register_rest_field('product_variation', 'image', array('get_callback' => 'get_image_src'));
      register_rest_field('product_cat', 'image', array('get_callback' => 'get_image_src'));

    }

    function get_description_raw($object)
    {
      if (is_array($object)) {
        $product = wc_get_product($object['id']);
      } else {
        $product = $object;
      }
      return $product->get_description();
    }

    function get_short_description_raw($object)
    {
      if (is_array($object)) {
        $product = wc_get_product($object['id']);
      } else {
        $product = $object;
      }
      return $product->get_short_description();
    }

    function get_purchase_note_raw($object)
    {
      if (is_array($object)) {
        $product = wc_get_product($object['id']);
      } else {
        $product = $object;
      }
      return $product->get_purchase_note();
    }

    function get_variation_description_raw($object)
    {
      return $object['description'];
    }

    function get_category_description_raw($object)
    {
      return category_description($object['id']);
    }

    function get_images_src($object)
    {
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

  }

endif;
