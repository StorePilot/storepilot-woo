<?php 
if (!defined('ABSPATH')) exit;
if ( !class_exists('ShiftFields') ) {

    class ShiftFields {
        var $postTypes = array( "shift" );
        var $customFields = array(
            array(
                "name"          => "start",
                "title"         => "Start",
                "description"   => "",
                "type"          => "datetime",
                "scope"         => array( "shift" ),
                "capability"    => "edit_posts"
            ),
            array(
                "name"          => "end",
                "title"         => "End",
                "description"   => "",
                "type"          => "datetime",
                "scope"         => array( "shift" ),
                "capability"    => "edit_posts"
            )
        );

        /**
        * PHP 5 Constructor
        */
        function __construct() {
            add_action( 'admin_menu', array( $this, 'createShiftFields' ));
            add_action( 'save_post', array( $this, 'saveShiftFields' ), 1, 2 );
            add_action( 'do_meta_boxes', array( $this, 'removeDefaultCustomFields' ), 10, 3 );
            add_action( 'admin_print_styles', array( $this, 'shifts_admin_styles' ));
            add_action( 'admin_enqueue_scripts',  array ( $this, 'shifts_admin_scripts' ));
        }

        function shifts_admin_styles() {
            wp_enqueue_style( 'jquery-ui-datepicker-style' , '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
        }
          
        function shifts_admin_scripts() {
            wp_enqueue_script( 'jquery-ui-datepicker' );
        }

        function removeDefaultCustomFields( $type, $context, $post ) {
            foreach ( array( 'normal', 'advanced', 'side' ) as $context ) {
                foreach ( $this->postTypes as $postType ) {
                    remove_meta_box( 'postcustom', $postType, $context );
                }
            }
        }

        function createShiftFields() {
            if ( function_exists( 'add_meta_box' ) ) {
                foreach ( $this->postTypes as $postType ) {
                    add_meta_box( 'shift-fields', 'Shift Fields', array( $this, 'displayShiftFields' ), $postType, 'normal', 'high' );
                }
            }
        }

        function displayShiftFields() {
            global $post;
            ?>
            <div class="form-wrap">
                <?php
                wp_nonce_field( 'shift-fields', 'shift-fields_wpnonce', false, true );
                foreach ( $this->customFields as $customField ) {
                    // Check scope
                    $scope = $customField[ 'scope' ];
                    $output = false;
                    foreach ( $scope as $scopeItem ) {
                        switch ( $scopeItem ) {
                            default: {
                                if ( $post->post_type == $scopeItem )
                                    $output = true;
                                break;
                            }
                        }
                        if ( $output ) break;
                    }
                    // Check capability
                    if ( !current_user_can( $customField['capability'], $post->ID ) )
                        $output = false;
                    // Output if allowed
                    if ( $output ) { ?>
                        <div class="form-field form-required">
                            <?php
                            switch ( $customField[ 'type' ] ) {
                                case "checkbox": {
                                    // Checkbox
                                    echo '<label for="' . $customField[ 'name' ] .'" style="display:inline;"><b>' . $customField[ 'title' ] . '</b></label>nbsp;nbsp;';
                                    echo '<input type="checkbox" name="' . $customField['name'] . '" id="' . $customField['name'] . '" value="yes"';
                                    if ( get_post_meta( $post->ID, $customField['name'], true ) == "yes" )
                                        echo ' checked="checked"';
                                    echo '" style="width: auto;" />';
                                    break;
                                }
                                case "textarea":
                                case "wysiwyg": {
                                    // Text area
                                    echo '<label for="' . $customField[ 'name' ] .'"><b>' . $customField[ 'title' ] . '</b></label>';
                                    echo '<textarea name="' . $customField[ 'name' ] . '" id="' . $customField[ 'name' ] . '" columns="30" rows="3">' . htmlspecialchars( get_post_meta( $post->ID, $customField[ 'name' ], true ) ) . '</textarea>';
                                    // WYSIWYG
                                    if ( $customField[ 'type' ] == "wysiwyg" ) { ?>
                                        <script type="text/javascript">
                                            jQuery( document ).ready( function() {
                                                jQuery( "<?php echo $customField[ 'name' ]; ?>" ).addClass( "mceEditor" );
                                                if ( typeof( tinyMCE ) == "object"  typeof( tinyMCE.execCommand ) == "function" ) {
                                                    tinyMCE.execCommand( "mceAddControl", false, "<?php echo $customField[ 'name' ]; ?>" );
                                                }
                                            });
                                        </script>
                                    <?php }
                                    break;
                                }
                                case "datetime": {
                                    // Text area
                                    echo '<label for="' . $customField[ 'name' ] .'"><b>' . $customField[ 'title' ] . '</b></label>';
                                    echo '<input name="' . $customField[ 'name' ] . '" id="' . $customField[ 'name' ] . '" value="' . get_post_meta( $post->ID, $customField[ 'name' ], true ) . '"></input>';
                                    ?>
                                    <script type="text/javascript">
                                        jQuery( document ).ready( function() {
                                            jQuery( "#<?php echo $customField[ 'name' ]; ?>" ).datepicker({ dateFormat: 'DD, d MM, yy' });
                                            jQuery( "#ui-datepicker-div" ).hide();
                                        });
                                    </script>
                                    <?php
                                    break;
                                }
                                default: {
                                    // Plain text field
                                    echo '<label for="' . $customField[ 'name' ] .'"><b>' . $customField[ 'title' ] . '</b></label>';
                                    echo '<input type="text" name="' . $customField[ 'name' ] . '" id="' . $customField[ 'name' ] . '" value="' . htmlspecialchars( get_post_meta( $post->ID, $customField[ 'name' ], true ) ) . '" />';
                                    break;
                                }
                            }
                            ?>
                            <?php if ( $customField[ 'description' ] ) echo '<p>' . $customField[ 'description' ] . '</p>'; ?>
                        </div>
                    <?php
                    }
                } ?>
            </div>
            <?php
        }

        function saveShiftFields( $post_id, $post ) {
            if ( !isset( $_POST[ 'shift-fields_wpnonce' ] ) || !wp_verify_nonce( $_POST[ 'shift-fields_wpnonce' ], 'shift-fields' ) )
                return;
            if ( !current_user_can( 'edit_post', $post_id ) )
                return;
            if ( ! in_array( $post->post_type, $this->postTypes ) )
                return;
            foreach ( $this->customFields as $customField ) {
                if ( current_user_can( $customField['capability'], $post_id ) ) {
                    if ( isset( $_POST[ $customField['name'] ] ) && trim( $_POST[ $customField['name'] ] ) ) {
                        $value = $_POST[ $customField['name'] ];
                        if ( $customField['type'] == "wysiwyg" ) $value = wpautop( $value );
                        update_post_meta( $post_id, $customField[ 'name' ], $value );
                    } else {
                        delete_post_meta( $post_id, $customField[ 'name' ] );
                    }
                }
            }
        }
 
    } // End Class
 
}
 
// Instantiate the class
if ( class_exists('ShiftFields') ) {
    $myCustomFields_var = new ShiftFields();
}

add_action( 'init', function() {
	$labels = array(
		'name'                  => __('Shifts'),
		'singular_name'         => __('Shift'),
		'menu_name'             => __('Shifts'),
		'name_admin_bar'        => __('Shift'),
		'archives'              => __('Shift Archives'),
		'attributes'            => __('Shift Attributes'),
		'parent_item_colon'     => __('Parent Shift'),
		'all_items'             => __('All Shifts'),
		'add_new_item'          => __('Add Shift'),
		'add_new'               => __('Add New'),
		'new_item'              => __('New Shift'),
		'edit_item'             => __('Edit Shift'),
		'update_item'           => __('Update Shift'),
		'view_item'             => __('View Shift'),
		'view_items'            => __('View Shifts'),
		'search_items'          => __('Search Shift'),
		'not_found'             => __('Not found'),
		'not_found_in_trash'    => __('Not found in Trash'),
		'featured_image'        => __('Featured Image'),
		'set_featured_image'    => __('Set featured image'),
		'remove_featured_image' => __('Remove featured image'),
		'use_featured_image'    => __('Use as featured image'),
		'insert_into_item'      => __('Insert into item'),
		'uploaded_to_this_item' => __('Uploaded to this item'),
		'items_list'            => __('Shifts list'),
		'items_list_navigation' => __('Shifts list navigation'),
		'filter_items_list'     => __('Filter items list'),
	);
	$args = array(
		'label'                 => 'Shift',
		'description'           => 'Shifts used for X and Z reports',
		'labels'                => $labels,
		'supports'              => array( 'title', 'custom-fields' ),
		'hierarchical'          => false,
		'public'                => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-clock',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => true,
		'publicly_queryable'    => false,
		'rewrite'               => false,
		'capability_type'       => 'post',
		'show_in_rest'          => true,
	);
	register_post_type( 'shift', $args );
});

// Add to REST API
add_action( 'rest_api_init', function() {
  register_rest_field('shift', 'start', array(
    'get_callback'    => 'get_start',
    'update_callback' => 'update_shift',
    'schema' => array(
      'description' => __('Start'),
      'type'        => 'date'
    )
  ));
  register_rest_field('shift', 'end', array(
    'get_callback'    => 'get_end',
    'update_callback' => 'update_shift',
    'schema' => array(
      'description' => __('End'),
      'type'        => 'date'
    )
  ));
});

// Get
function get_start($obj)
{
  return get_post_meta($obj['id'], 'start', true);
}
function get_end($obj)
{
  return get_post_meta($obj['id'], 'end', true);
}

// Update
function update_shift($value, $post, $key)
{
  $obj = json_decode($post, true);
  $post_id = update_post_meta($obj['id'], $key, $value);
  if (false === $post_id) {
    return new WP_Error(
      'rest_shift_failed',
      __('Failed to update shift.'),
      array('status' => 500)
    );
  }
  return true;
}