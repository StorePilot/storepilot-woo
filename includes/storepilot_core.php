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

    /**
     * @note - Functions below may be added to its own file in includes/api
     * folder later, as its extending the api functionality
     */
    function get_cross_sell($object)
    {
      $updated = [];
      if (isset($object['cross_sell_ids']) && is_array($object['cross_sell_ids'])) {
        foreach ($object['cross_sell_ids'] as $id) {
          $prod = wc_get_product($id);
          if ($prod) {
            $product = $this->get_product_data($prod);
            $updated[] = $product;
          }
        }
      }
      return $updated;
    }

    function get_upsell($object)
    {
      $updated = [];
      if (isset($object['upsell_ids']) && is_array($object['upsell_ids'])) {
        foreach ($object['upsell_ids'] as $id) {
          $prod = wc_get_product($id);
          if ($prod) {
            $product = $this->get_product_data($prod);
            $updated[] = $product;
          }
        }
      }
      return $updated;
    }

    protected function get_downloads($product)
    {
      $downloads = array();
      if ($product->is_downloadable()) {
        foreach ($product->get_downloads() as $file_id => $file) {
          $downloads[] = array(
              'id' => $file_id, // MD5 hash.
              'name' => $file['name'],
              'file' => $file['file'],
          );
        }
      }
      return $downloads;
    }

    protected function get_taxonomy_terms($product, $taxonomy = 'cat')
    {
      $terms = array();

      foreach (wc_get_object_terms($product->get_id(), 'product_' . $taxonomy) as $term) {
        $terms[] = array(
            'id' => $term->term_id,
            'name' => $term->name,
            'slug' => $term->slug,
        );
      }

      return $terms;
    }

    protected function get_images($product)
    {
      $images = array();
      $attachment_ids = array();

      // Add featured image.
      if (has_post_thumbnail($product->get_id())) {
        $attachment_ids[] = $product->get_image_id();
      }

      // Add gallery images.
      $attachment_ids = array_merge($attachment_ids, $product->get_gallery_image_ids());

      // Build image data.
      foreach ($attachment_ids as $position => $attachment_id) {
        $attachment_post = get_post($attachment_id);
        if (is_null($attachment_post)) {
          continue;
        }

        $attachment = wp_get_attachment_image_src($attachment_id, 'full');
        if (!is_array($attachment)) {
          continue;
        }

        $images[] = array(
            'id' => (int)$attachment_id,
            'date_created' => wc_rest_prepare_date_response($attachment_post->post_date_gmt),
            'date_modified' => wc_rest_prepare_date_response($attachment_post->post_modified_gmt),
            'src' => current($attachment),
            'name' => get_the_title($attachment_id),
            'alt' => get_post_meta($attachment_id, '_wp_attachment_image_alt', true),
            'position' => (int)$position,
        );
      }

      // Set a placeholder image if the product has no images set.
      if (empty($images)) {
        $images[] = array(
            'id' => 0,
            'date_created' => wc_rest_prepare_date_response(current_time('mysql')), // Default to now.
            'date_modified' => wc_rest_prepare_date_response(current_time('mysql')),
            'src' => wc_placeholder_img_src(),
            'name' => __('Placeholder', 'storepilot'),
            'alt' => __('Placeholder', 'storepilot'),
            'position' => 0,
        );
      }

      return ['images' => $images];
    }

    protected function get_attribute_taxonomy_label($name)
    {
      $tax = get_taxonomy($name);
      $labels = get_taxonomy_labels($tax);

      return $labels->singular_name;
    }

    protected function get_default_attributes($product)
    {
      $default = array();

      if ($product->is_type('variable')) {
        foreach (array_filter((array)$product->get_default_attributes(), 'strlen') as $key => $value) {
          if (0 === strpos($key, 'pa_')) {
            $default[] = array(
                'id' => wc_attribute_taxonomy_id_by_name($key),
                'name' => $this->get_attribute_taxonomy_label($key),
                'option' => $value,
            );
          } else {
            $default[] = array(
                'id' => 0,
                'name' => str_replace('pa_', '', $key),
                'option' => $value,
            );
          }
        }
      }

      return $default;
    }

    protected function get_attribute_options($product_id, $attribute)
    {
      if (isset($attribute['is_taxonomy']) && $attribute['is_taxonomy']) {
        return wc_get_product_terms($product_id, $attribute['name'], array('fields' => 'names'));
      } elseif (isset($attribute['value'])) {
        return array_map('trim', explode('|', $attribute['value']));
      }

      return array();
    }

    protected function get_attributes($product)
    {
      $attributes = array();

      if ($product->is_type('variation')) {
        // Variation attributes.
        foreach ($product->get_variation_attributes() as $attribute_name => $attribute) {
          $name = str_replace('attribute_', '', $attribute_name);

          if (!$attribute) {
            continue;
          }

          // Taxonomy-based attributes are prefixed with `pa_`, otherwise simply `attribute_`.
          if (0 === strpos($attribute_name, 'attribute_pa_')) {
            $option_term = get_term_by('slug', $attribute, $name);
            $attributes[] = array(
                'id' => wc_attribute_taxonomy_id_by_name($name),
                'name' => $this->get_attribute_taxonomy_label($name),
                'option' => $option_term && !is_wp_error($option_term) ? $option_term->name : $attribute,
            );
          } else {
            $attributes[] = array(
                'id' => 0,
                'name' => $name,
                'option' => $attribute,
            );
          }
        }
      } else {
        foreach ($product->get_attributes() as $attribute) {
          if ($attribute['is_taxonomy']) {
            $attributes[] = array(
                'id' => wc_attribute_taxonomy_id_by_name($attribute['name']),
                'name' => $this->get_attribute_taxonomy_label($attribute['name']),
                'position' => (int)$attribute['position'],
                'visible' => (bool)$attribute['is_visible'],
                'variation' => (bool)$attribute['is_variation'],
                'options' => $this->get_attribute_options($product->get_id(), $attribute),
            );
          } else {
            $attributes[] = array(
                'id' => 0,
                'name' => $attribute['name'],
                'position' => (int)$attribute['position'],
                'visible' => (bool)$attribute['is_visible'],
                'variation' => (bool)$attribute['is_variation'],
                'options' => $this->get_attribute_options($product->get_id(), $attribute),
            );
          }
        }
      }

      return $attributes;
    }

    public function get_product_data($product)
    {
      $data = array(
          'id' => $product->get_id(),
          'name' => $product->get_name(),
          'slug' => $product->get_slug(),
          'permalink' => $product->get_permalink(),
          'date_created' => wc_rest_prepare_date_response($product->get_date_created(), false),
          'date_created_gmt' => wc_rest_prepare_date_response($product->get_date_created()),
          'date_modified' => wc_rest_prepare_date_response($product->get_date_modified(), false),
          'date_modified_gmt' => wc_rest_prepare_date_response($product->get_date_modified()),
          'type' => $product->get_type(),
          'status' => $product->get_status(),
          'featured' => $product->is_featured(),
          'catalog_visibility' => $product->get_catalog_visibility(),
          'description' => wpautop(do_shortcode($product->get_description())),
          'description_raw' => $this->get_description_raw($product),
          'short_description' => apply_filters('woocommerce_short_description', $product->get_short_description()),
          'short_description_raw' => $this->get_short_description_raw($product),
          'sku' => $product->get_sku(),
          'price' => $product->get_price(),
          'regular_price' => $product->get_regular_price(),
          'sale_price' => $product->get_sale_price() ? $product->get_sale_price() : '',
          'date_on_sale_from' => wc_rest_prepare_date_response($product->get_date_on_sale_from(), false),
          'date_on_sale_from_gmt' => wc_rest_prepare_date_response($product->get_date_on_sale_from()),
          'date_on_sale_to' => wc_rest_prepare_date_response($product->get_date_on_sale_to(), false),
          'date_on_sale_to_gmt' => wc_rest_prepare_date_response($product->get_date_on_sale_to()),
          'price_html' => $product->get_price_html(),
          'on_sale' => $product->is_on_sale(),
          'purchasable' => $product->is_purchasable(),
          'total_sales' => $product->get_total_sales(),
          'virtual' => $product->is_virtual(),
          'downloadable' => $product->is_downloadable(),
          'downloads' => $this->get_downloads($product),
          'download_limit' => $product->get_download_limit(),
          'download_expiry' => $product->get_download_expiry(),
          'external_url' => $product->is_type('external') ? $product->get_product_url() : '',
          'button_text' => $product->is_type('external') ? $product->get_button_text() : '',
          'tax_status' => $product->get_tax_status(),
          'tax_class' => $product->get_tax_class(),
          'manage_stock' => $product->managing_stock(),
          'stock_quantity' => $product->get_stock_quantity(),
          'in_stock' => $product->is_in_stock(),
          'backorders' => $product->get_backorders(),
          'backorders_allowed' => $product->backorders_allowed(),
          'backordered' => $product->is_on_backorder(),
          'sold_individually' => $product->is_sold_individually(),
          'weight' => $product->get_weight(),
          'dimensions' => array(
              'length' => $product->get_length(),
              'width' => $product->get_width(),
              'height' => $product->get_height(),
          ),
          'shipping_required' => $product->needs_shipping(),
          'shipping_taxable' => $product->is_shipping_taxable(),
          'shipping_class' => $product->get_shipping_class(),
          'shipping_class_id' => $product->get_shipping_class_id(),
          'reviews_allowed' => $product->get_reviews_allowed(),
          'average_rating' => wc_format_decimal($product->get_average_rating(), 2),
          'rating_count' => $product->get_rating_count(),
          'related_ids' => array_map('absint', array_values(wc_get_related_products($product->get_id()))),
          'upsell_ids' => array_map('absint', $product->get_upsell_ids()),
          'cross_sell_ids' => array_map('absint', $product->get_cross_sell_ids()),
          'parent_id' => $product->get_parent_id(),
          'purchase_note' => wpautop(do_shortcode(wp_kses_post($product->get_purchase_note()))),
          'purchase_note_raw' => $this->get_purchase_note_raw($product),
          'categories' => $this->get_taxonomy_terms($product),
          'tags' => $this->get_taxonomy_terms($product, 'tag'),
          'images' => $this->get_images_src($this->get_images($product)),
          'attributes' => $this->get_attributes($product),
          'default_attributes' => $this->get_default_attributes($product),
          'variations' => array(),
          'grouped_products' => array(),
          'menu_order' => $product->get_menu_order(),
          'meta_data' => $product->get_meta_data(),
      );

      return $data;
    }

    public function get_product_category_data($category)
    {
      $image = null;
      $image_id = get_term_meta($category->cat_ID, 'thumbnail_id');
      if ($image_id) {
        $attachment = get_post($image_id);
        $image = array(
            'id' => (int)$image_id,
            'date_created' => wc_rest_prepare_date_response($attachment->post_date_gmt),
            'date_modified' => wc_rest_prepare_date_response($attachment->post_modified_gmt),
            'src' => wp_get_attachment_url($image_id),
            'title' => get_the_title($attachment),
            'alt' => get_post_meta($image_id, '_wp_attachment_image_alt', true),
        );
      }
      $display = get_term_meta($category->cat_ID, 'display_type', true);
      $data = array(
          'id' => (int)$category->cat_ID,
          'count' => (int)$category->count,
          'slug' => $category->slug,
          'permalink' => get_term_link($category->cat_ID, 'product_cat'),
          'description' => $category->description,
          'description_raw' => $this->get_category_description_raw(['id' => $category->cat_ID]),
          'display' => $display ? $display : 'default',
          'image' => $image,
          'menu_order' => (int)get_term_meta($category->cat_ID, 'order', true),
          'name' => $category->name,
          'parent' => (int)$category->parent
      );
      return $data;
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

    function get_product_cat_header($object)
    {
      $val = get_option('term_product_cat_header_' . $object['id']);
      if ($val === 'null') {
        $val = '';
      }
      return apply_filters('the_content', $val ? $val : '');
    }

    function get_product_cat_footer($object)
    {
      $val = get_option('term_product_cat_footer_' . $object['id']);
      if ($val === 'null') {
        $val = '';
      }
      return apply_filters('the_content', $val ? $val : '');
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
