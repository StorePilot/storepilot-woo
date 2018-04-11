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
    public $version = 'SP_VERSION_REPLACE';

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
    public function __construct($version = 'SP_VERSION_REPLACE')
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
     * Init StorePilot - @note - Called in storepilot_production or storepilot_development after $SP->environment is set
     */
    public function init()
    {
      do_action('before_storepilot_init');

      // Set up localisation
      $this->load_plugin_textdomain();

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
      add_action('admin_menu', array($this, 'add_to_admin_menu'));
      add_action('product_cat_add_form_fields', array($this, 'add_to_product_category'));
      add_action('admin_menu', array($this, 'remove_admin_menus'), 100);
      add_action('admin_bar_menu', array($this, 'remove_admin_bar'), 100);
      add_action('rest_api_init', array($this, 'add_additional_rest_fields'));
      add_action('wp_before_admin_bar_render', array($this, 'sp_builder_admin_bar_link'));
      if (!is_admin()) {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
      } else if (!(isset($_GET['page']) && $_GET['page'] === 'storepilot')) {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
      }
      add_action('woocommerce_before_shop_loop', array($this, 'add_product_category_header'));
      add_action('woocommerce_before_shop_loop_item', array($this, 'add_edit_link'));
      add_action('woocommerce_after_shop_loop', array($this, 'add_product_category_footer'));
      add_filter('woocommerce_loaded', array($this, 'extend_rest_api'));
      add_filter('woocommerce_sale_flash', array($this, 'sale_flash'));
      global $pagenow;
      if (isset($pagenow) && $pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == 'storepilot') {
        add_action('admin_init', array($this, 'deregister'));
      }
    }

    public function remove_admin_bar()
    {
      if (isset($_GET['page']) && strrpos($_GET['page'], 'storepilot_fill') !== false) {
        show_admin_bar(false);
        global $wp_admin_bar;
        forEach($wp_admin_bar->get_nodes() as $node) {
          $wp_admin_bar->remove_node($node->id);
        }
      }
    }

    public function remove_admin_menus()
    {
      if (isset($_GET['page']) && strrpos($_GET['page'], 'storepilot_fill') !== false) {
        global $menu;
        $menu = array();
      }
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

    public function getCats()
    {
      $cats = get_categories([
          'taxonomy' => 'product_cat',
          'posts_per_page' => 1000,
          'hide_empty' => false
      ]);
      $c = [];
      foreach ($cats as $cat) {
        $c[] = $this->get_product_category_data($cat);
      }
      return $c;
    }

    public function preload()
    {
      $prods = get_posts([
          'post_type' => 'product',
          'orderby' => 'menu_order',
          'posts_per_page' => 100
      ]);
      $p = [];
      foreach ($prods as $prod) {
        $p[] = $this->get_product_data(wc_get_product($prod->ID));
      }
      return json_encode([
          'categories' => $this->getCats(),
          'products' => $p
      ]);
    }

    public function enqueue_scripts()
    {
      wp_enqueue_style('storepilot_css_frontend', plugins_url('style/frontend.css', __FILE__), array(), $this->version);
      if (current_user_can('editor') || current_user_can('administrator')) {
        wp_enqueue_script('storepilot_js_frontend', plugins_url('js/frontend.js', __FILE__), array(), $this->version, true);
        $uri = get_admin_url() . 'admin.php?page=storepilot_fill#/manager/product';
        wp_localize_script('storepilot_js_frontend', 'url', $uri);
      }
    }

    public function add_edit_link()
    {
      if (current_user_can('editor') || current_user_can('administrator')) {
        $uri = get_admin_url() . 'admin.php?page=storepilot_fill#/manager/product/' . get_the_ID();
        echo "<button onclick='storepilot_modal_trigger(\"$uri\")' class='sp-edit-product'><span class='ab-icon'></span>StorePilot</button>";
      }
    }

    public function add_product_category_header()
    {
      $obj = get_queried_object();
      if (isset($obj->term_id)) {
        $term = get_term($obj->term_id);
        if (isset($term->term_id)) {
          $header = get_option('term_product_cat_header_' . $term->term_id);
          echo '<div class="sp-product-cat-header">' . apply_filters('the_content', $header) . '</div>';
        }
      }
    }

    public function add_product_category_footer()
    {
      $obj = get_queried_object();
      if (isset($obj->term_id)) {
        $term = get_term($obj->term_id);
        if (isset($term->term_id)) {
          $footer = get_option('term_product_cat_footer_' . $term->term_id);
          echo '<div class="sp-product-cat-footer">' . apply_filters('the_content', $footer) . '</div>';
        }
      }
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
     * Load Localisation files
     *
     * Note: the first-loaded translation file overrides any following ones if the same translation is present.
     *
     * Locales found in:
     *      - WP_LANG_DIR/storepilot/storepilot-LOCALE.mo
     *      - WP_LANG_DIR/plugins/storepilot-LOCALE.mo
     */
    public function load_plugin_textdomain()
    {
      $domain = 'storepilot';
      $locale = is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale();
      if ( $loaded = load_textdomain( $domain, WP_LANG_DIR . $domain . '/' . $domain . '-' . $locale . '.mo' ) ) {
        return $loaded;
      } else {
        load_textdomain( $domain, plugin_dir_path(__FILE__) . '/i18n/languages/' . $domain . '-' . $locale . '.mo' );
      }
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
     * Enable media manager
     */
    function allow_media_manager($allow_origin = '')
    {
      wp_enqueue_media();
      ?>
      <script>
        window.onload = function () {
          var type = ''
          var multiple = false
          var title = 'Select image'
          var buttonText = 'Use selected media'
          var library = 'image'

          function receiveMessage(event) {
            let json
            try {
              json = JSON.parse(event.data)
            } catch (e) {
              json = null
            }
            if (
              // Check request for media
            json !== null && (json.type === 'sp_get_media') && (
              // Check if origin is allowed
              (event.origin + '/').split("?")[0] === '<?php echo preg_replace('/\?.*/', '', (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/'); ?>' ||
              (event.origin + '/') === '<?php echo $allow_origin; ?>'
            )) {
              type = json.type
              title = json.title
              multiple = json.multiple
              buttonText = json.buttonText
              library = json.library

              // configuration of the media manager new instance
              wp.media.frames.gk_frame = wp.media({
                title: title,
                multiple: multiple,
                library: {
                  type: library
                },
                button: {
                  text: buttonText
                }
              })

              // On Selection
              let onSelection = function () {
                let media = wp.media.frames.gk_frame.state().get('selection')
                let response = JSON.stringify({
                  type: type.replace('get', 'send'),
                  media: media,
                  multiple: multiple
                })
                event.source.postMessage(response, event.origin)
              }

              // On Close
              let onClose = function () {
                setTimeout(function () {
                  let response = JSON.stringify({
                    type: type.replace('get', 'send'),
                    media: null,
                    multiple: multiple
                  })
                  event.source.postMessage(response, event.origin)
                })
              }

              // closing event for media manger
              wp.media.frames.gk_frame.on('close', onClose)
              // image selection event
              wp.media.frames.gk_frame.on('select', onSelection)
              // showing media manager
              wp.media.frames.gk_frame.open()
            }
          }

          window.addEventListener('message', receiveMessage, false)
        }
      </script>
      <?php
    }

    /**
     * Deregister default wp admin styles and scripts
     */
    function deregister()
    {
      // STYLES
      wp_deregister_style('wp-pointer');
      wp_deregister_style('ie');
      wp_deregister_style('wp-auth-check');
      wp_deregister_style('woocommerce_admin_menu_styles');
      wp_deregister_style('imgareaselect');
      wp_deregister_style('woocommerce_admin_styles');
      wp_deregister_style('wp-jquery-ui-dialog');
      wp_deregister_style('jquery-chosen');
      /*
         Needed for media manager
         wp_deregister_style( 'mediaelement' );
         wp_deregister_style( 'wp-mediaelement' );
         wp_deregister_style( 'media-views' );
      /*
        Needed for menu
        wp_deregister_style( 'dashicons' );
        wp_deregister_style( 'admin-bar' );
        wp_deregister_style( 'common' );
        wp_deregister_style( 'forms' );
        wp_deregister_style( 'admin-menu' );
        wp_deregister_style( 'dashboard' );
        wp_deregister_style( 'list-tables' );
        wp_deregister_style( 'edit' );
        wp_deregister_style( 'revisions' );
        wp_deregister_style( 'media' );
        wp_deregister_style( 'themes' );
        wp_deregister_style( 'about' );
        wp_deregister_style( 'nav-menus' );
        wp_deregister_style( 'widgets' );
        wp_deregister_style( 'site-icon' );
        wp_deregister_style( 'l10n' );
        wp_deregister_style( 'wp-admin' );
        wp_deregister_style( 'buttons' );
        wp_deregister_style( 'colors' );
      */
      // SCRIPTS
      wp_deregister_script('jquery_ui');
      wp_deregister_script('jcrop');
      wp_deregister_script('swfobject');
      wp_deregister_script('swfupload');
      wp_deregister_script('swfupload-degrade');
      wp_deregister_script('swfupload-queue');
      wp_deregister_script('swfupload-handlers');
      wp_deregister_script('thickbox');
      wp_deregister_script('sack');
      wp_deregister_script('quicktags');
      wp_deregister_script('tiny_mce');
      wp_deregister_script('autosave');
      wp_deregister_script('wp-ajax-response');
      wp_deregister_script('wp-lists');
      wp_deregister_script('editorremov');
      wp_deregister_script('editor-functions');
      wp_deregister_script('ajaxcat');
      wp_deregister_script('admin-categories');
      wp_deregister_script('admin-tags');
      wp_deregister_script('admin-custom-fields');
      wp_deregister_script('password-strength-meter');
      wp_deregister_script('admin-comments');
      wp_deregister_script('admin-users');
      wp_deregister_script('admin-forms');
      wp_deregister_script('xfn');
      wp_deregister_script('upload');
      wp_deregister_script('postbox');
      wp_deregister_script('slug');
      wp_deregister_script('post');
      wp_deregister_script('page');
      wp_deregister_script('link');
      wp_deregister_script('comment');
      wp_deregister_script('comment-reply');
      wp_deregister_script('admin-gallery');
      wp_deregister_script('media-upload');
      wp_deregister_script('admin-widgets');
      wp_deregister_script('word-count');
      wp_deregister_script('theme-preview');
      wp_deregister_script('plupload-all');
      wp_deregister_script('plupload-html4');
      wp_deregister_script('plupload-flash');
      wp_deregister_script('plupload-silverlight');
      /*
        Needed for media selector to work
        wp_deregister_script( 'plupload' );
        wp_deregister_script( 'json2' );
        wp_deregister_script( 'plupload-html5' );
      /*
        Needed for menu toggling
        wp_deregister_script( 'common' );
      */
    }

    /**
     * Setup Plugin Navigation
     */
    function add_to_product_category()
    {
      ?>
      <div class="form-field">
        <label for="product-cat-header"><?php echo __('Header', 'storepilot'); ?></label>
        <textarea rows="5" cols="40" name="product-cat-header" id="product-cat-header"></textarea>
        <p><?php echo __('Category Header Content', 'storepilot'); ?></p>
      </div>
      <div class="form-field">
        <label for="product-cat-footer"><?php echo __('Footer', 'storepilot'); ?></label>
        <textarea rows="5" cols="40" name="product-cat-footer" id="product-cat-footer"></textarea>
        <p><?php echo __('Category Footer Content', 'storepilot'); ?></p>
      </div>
      <?php
    }

    /**
     * Setup Plugin Navigation
     */
    function add_to_admin_menu()
    {

      // Menu Icon
      $icon = plugins_url('assets/menu.png', __FILE__);

      // StorePilot Plugin Home
      add_menu_page(
          'StorePilot',
          'StorePilot',
          'manage_options',
          'storepilot',
          array($this, 'environment'),
          $icon,
          58
      );

      // Install Woocommerce
      if (!$this->has_woocommerce()) {
        add_submenu_page(
            'storepilot',
            __('Install', 'storepilot'),
            __('Install', 'storepilot'),
            'install_plugins',
            'storepilot#activate',
            array($this, 'environment')
        );
      }

      // Activate Pretty Permalinks
      if (!get_option('permalink_structure')) {
        add_submenu_page(
            'storepilot',
            __('Setup', 'storepilot'),
            __('Setup', 'storepilot'),
            'manage_options',
            'storepilot#permalinks',
            array($this, 'environment')
        );
      }

      if ($this->has_woocommerce() && get_option('permalink_structure')) {
        // Products Manager
        add_submenu_page(
            'storepilot',
            __('Manager', 'storepilot'),
            __('Manager', 'storepilot'),
            'manage_options',
            'storepilot#/manager/product',
            array($this, 'environment')
        );
        // License Activation / Deactivation
        add_submenu_page(
            'storepilot',
            __('License', 'storepilot'),
            __('License', 'storepilot'),
            'manage_options',
            'storepilot#/license',
            array($this, 'environment')
        );
        add_submenu_page(
            null,
            __('Editor', 'storepilot'),
            __('Editor', 'storepilot'),
            'manage_options',
            'storepilot_fill',
            array($this, 'environment_fill')
        );
      }

    }

    // Append StorePilot to wp-topbar
    function sp_builder_admin_bar_link()
    {
      if (!(isset($_GET['page']) && $_GET['page'] === 'storepilot')) {
        global $wp_admin_bar;
        global $wp_query;
        global $product;
        if (!$this->has_woocommerce() || !current_user_can('manage_woocommerce')) return;
        $id = is_product() ? $product->get_id() : (is_admin() && get_post_type() === 'product' ? get_the_ID() : null);
        $cat_id = is_product_category() ?
            $wp_query->get_queried_object()->term_id :
            (is_admin() && isset($_GET['taxonomy']) && isset($_GET['tag_ID']) && $_GET['taxonomy'] === 'product_cat' ? $_GET['tag_ID'] : null);
        $tag_id = is_product_tag() ?
            $wp_query->get_queried_object()->term_id :
            (is_admin() && isset($_GET['taxonomy']) && isset($_GET['tag_ID']) && $_GET['taxonomy'] === 'product_tag' ? $_GET['tag_ID'] : null);
        if ($id !== null) {
          $uri = get_admin_url() . 'admin.php?page=storepilot_fill#/manager/product/' . $id;
        } else if ($cat_id !== null) {
          $uri = get_admin_url() . 'admin.php?page=storepilot_fill#/manager/category/' . $cat_id;
        } else if ($tag_id !== null) {
          $uri = get_admin_url() . 'admin.php?page=storepilot_fill#/manager/tag/' . $tag_id;
        } else {
          $uri = get_admin_url() . 'admin.php?page=storepilot_fill#/manager/product';
        }
        $wp_admin_bar->add_menu(array(
            'id' => 'storepilot',
            'title' => '<span class="ab-icon"></span>StorePilot',
            'meta' => array('onclick' => 'storepilot_modal_trigger("' . $uri . '")'),
            'href' => '#'
        ));
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

      function set_product_cat_header($value, $attr, $str, $request)
      {
        if ($value === 'null') {
          $value = '';
        }
        $cat_id = (int)$request['id'];
        update_option('term_product_cat_header_' . $cat_id, $value);
        $attr->header = apply_filters('the_content', $value);
        return $attr;
      }

      function set_product_cat_footer($value, $attr, $str, $request)
      {
        if ($value === 'null') {
          $value = '';
        }
        $cat_id = (int)$request['id'];
        update_option('term_product_cat_footer_' . $cat_id, $value);
        $attr->footer = apply_filters('the_content', $value);
        return $attr;
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

      function get_product_category_permalink($object)
      {
        return get_term_link($object['slug'], 'product_cat');
      }

      function get_product_tag_permalink($object)
      {
        return get_term_link($object['slug'], 'product_tag');
      }

      /** Get unrendered content START **/
      /** @see - https://github.com/woocommerce/woocommerce/issues/16895 @todo - It has now been fixed for products **/
      register_rest_field('product', 'description_raw', array('get_callback' => array($this, 'get_description_raw')));
      register_rest_field('product', 'short_description_raw', array('get_callback' => array($this, 'get_short_description_raw')));
      register_rest_field('product', 'purchase_note_raw', array('get_callback' => array($this, 'get_purchase_note_raw')));
      register_rest_field('product_variation', 'description_raw', array('get_callback' => array($this, 'get_variation_description_raw')));
      register_rest_field('product_cat', 'description_raw', array('get_callback' => array($this, 'get_category_description_raw')));
      register_rest_field('product_cat', 'header_raw', array('get_callback' => array($this, 'get_product_cat_header_raw')));
      register_rest_field('product_cat', 'footer_raw', array('get_callback' => array($this, 'get_product_cat_footer_raw')));
      /** Get unrendered content END **/

      register_rest_field('product', 'price_range', array('get_callback' => 'get_price_range'));
      register_rest_field('product', 'cross_sell', array('get_callback' => array($this, 'get_cross_sell')));
      register_rest_field('product', 'upsell', array('get_callback' => array($this, 'get_upsell')));
      register_rest_field('product', 'images', array('get_callback' => array($this, 'get_images_src')));
      register_rest_field('product_variation', 'image', array('get_callback' => 'get_image_src'));
      register_rest_field('product_cat', 'image', array('get_callback' => 'get_image_src'));
      register_rest_field('product_cat', 'permalink', array('get_callback' => 'get_product_category_permalink'));
      register_rest_field('product_cat', 'header', array(
          'get_callback' => array($this, 'get_product_cat_header'),
          'update_callback' => 'set_product_cat_header'
      ));
      register_rest_field('product_cat', 'footer', array(
          'get_callback' => array($this, 'get_product_cat_footer'),
          'update_callback' => 'set_product_cat_footer'
      ));
      register_rest_field('product_tag', 'permalink', array('get_callback' => 'get_product_tag_permalink'));

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
          'footer' => $this->get_product_cat_footer(['id' => $category->cat_ID]),
          'footer_raw' => $this->get_product_cat_footer_raw(['id' => $category->cat_ID]),
          'header' => $this->get_product_cat_header(['id' => $category->cat_ID]),
          'header_raw' => $this->get_product_cat_header_raw(['id' => $category->cat_ID]),
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

    function get_product_cat_header_raw($object)
    {
      $val = get_option('term_product_cat_header_' . $object['id']);
      if ($val === 'null') {
        $val = '';
      }
      return $val ? $val : '';
    }

    function get_product_cat_footer_raw($object)
    {
      $val = get_option('term_product_cat_footer_' . $object['id']);
      if ($val === 'null') {
        $val = '';
      }
      return $val ? $val : '';
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
