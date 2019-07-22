<?php
/**
 * REST API: SP_REST_Actions_Controller class
 *
 * @author   StorePilot
 * @category API
 * @package  StorePilot/API
 * @since    1.1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Translation controller used to provide i18n via the REST API.
 *
 * @since 1.0.0
 *
 * @see SP_REST_Posts_Controller
 */
class SP_REST_Translation_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/sp/v1';

	/**
	 * Register the routes for custom ordering.
	 */
	public function register_routes() {
    register_rest_route( $this->namespace, '/translation', array(
        array(
          'methods'             => WP_REST_Server::READABLE,
          'callback'            => array( $this, 'get_translation' ),
          'permission_callback' => array( $this, 'get_translation_permissions_check' ),
          'args'                => $this->get_params()
        ))
    );
	}

	/**
	 * Checks if a given request has access read.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_translation_permissions_check( $request ) {

    rest_send_cors_headers( null ); // Allow cors when dealing with actions

		return true;
	}

  /**
   * Retrieves a collection of posts.
   *
   * @since 1.0.0
   * @access public
   *
   * @param WP_REST_Request $request Full details about the request.
   * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
   */
  public function get_translation( $request ) {

    $q = $request->get_query_params();
    $locale = $q['locale'];

    // Change locale if given.
    if ( !empty( $locale ) ) {
			switch_to_locale($locale);
    }

    return wp_send_json([
      [ key => "Activate", value => __("Activate", "woocommerce") ],
      [ key => "Activate woocommerce", value => __("Activate woocommerce", "woocommerce") ],
      [ key => "Actions", value => __("Actions", "woocommerce") ],
      [ key => "Advanced", value => __("Advanced", "woocommerce") ],
      [ key => "Additional", value => __("Additional", "woocommerce") ],
      [ key => "Add product", value => __("Add product", "woocommerce") ],
      [ key => "Add tag", value => __("Add tag", "woocommerce") ],
      [ key => "Add category", value => __("Add category", "woocommerce") ],
      [ key => "Add order", value => __("Add order", "woocommerce") ],
      [ key => "Attribute", value => __("Attribute", "woocommerce") ],
      [ key => "Attributes", value => __("Attributes", "woocommerce") ],
      [ key => "Attributes editor", value => __("Attributes editor", "woocommerce") ],
      [ key => "All", value => __("All", "woocommerce") ],
      [ key => "Allow", value => __("Allow", "woocommerce") ],
      [ key => "Allow backorders", value => __("Allow backorders", "woocommerce") ],
      [ key => "Allow but notify customer", value => __("Allow but notify customer", "woocommerce") ],
      [ key => "Align right", value => __("Align right", "woocommerce") ],
      [ key => "Align left", value => __("Align left", "woocommerce") ],
      [ key => "Align center", value => __("Align center", "woocommerce") ],
      [ key => "Authorize", value => __("Authorize", "woocommerce") ],
      [ key => "Authorize StorePilot", value => __("Authorize StorePilot", "woocommerce") ],
      [ key => "Archives", value => __("Archives", "woocommerce") ],
      [ key => "Append", value => __("Append", "woocommerce") ],
      [ key => "Apply", value => __("Apply", "woocommerce") ],
      [ key => "Barcode", value => __("Barcode", "woocommerce") ],
      [ key => "Bulk edit", value => __("Bulk edit", "woocommerce") ],
      [ key => "Bullet list", value => __("Bullet list", "woocommerce") ],
      [ key => "Bought", value => __("Bought!", "woocommerce") ],
      [ key => "Both", value => __("Both", "woocommerce") ],
      [ key => "Bold", value => __("Bold", "woocommerce") ],
      [ key => "By", value => __("By", "woocommerce") ],
      [ key => "Cancel", value => __("Cancel", "woocommerce") ],
      [ key => "Cash", value => __("Cash", "woocommerce") ],
      [ key => "Cash out", value => __("Cash out", "woocommerce") ],
      [ key => "Catalog order by", value => __("Catalog order by", "woocommerce") ],
      [ key => "Category", value => __("Category", "woocommerce") ],
      [ key => "Category archive display", value => __("Category archive display", "woocommerce") ],
      [ key => "Category title", value => __("Category title", "woocommerce") ],
      [ key => "Category slug", value => __("Category slug", "woocommerce") ],
      [ key => "Category settings", value => __("Category settings", "woocommerce") ],
      [ key => "Category image", value => __("Category image", "woocommerce") ],
      [ key => "Categories", value => __("Categories", "woocommerce") ],
      [ key => "Class", value => __("Class", "woocommerce") ],
      [ key => "Clear", value => __("Clear", "woocommerce") ],
      [ key => "Clear sale", value => __("Clear sale", "woocommerce") ],
      [ key => "Close", value => __("Close", "woocommerce") ],
      [ key => "Created", value => __("Created", "woocommerce") ],
      [ key => "Cross-Sells", value => __("Cross-Sells", "woocommerce") ],
      [ key => "CrossSells", value => __("Cross-Sells", "woocommerce") ],
      [ key => "Custom sorting", value => __("Custom sorting", "woocommerce") ],
      [ key => "Customers", value => __("Customers", "woocommerce") ],
      [ key => "Count", value => __("Count", "woocommerce") ],
      [ key => "Copy link", value => __("Copy link", "woocommerce") ],
      [ key => "Complete", value => __("Complete", "woocommerce") ],
      [ key => "Create", value => __("Create", "woocommerce") ],
      [ key => "Date", value => __("Date", "woocommerce") ],
      [ key => "Decrease", value => __("Decrease", "woocommerce") ],
      [ key => "Deactivate", value => __("Deactivate", "woocommerce") ],
      [ key => "Delete", value => __("Delete", "woocommerce") ],
      [ key => "Delete product permanently", value => __("Delete product permanently", "woocommerce") ],
      [ key => "Delete all permanently", value => __("Delete all permanently", "woocommerce") ],
      [ key => "Delete variation", value => __("Delete variation", "woocommerce") ],
      [ key => "Delete permanently", value => __("Delete permanently", "woocommerce") ],
      [ key => "Default", value => __("Default", "woocommerce") ],
      [ key => "Description", value => __("Description", "woocommerce") ],
      [ key => "Didn`t buy", value => __("Didn`t buy", "woocommerce") ],
      [ key => "Display", value => __("Display", "woocommerce") ],
      [ key => "Display type", value => __("Display type", "woocommerce") ],
      [ key => "Do not allow", value => __("Do not allow", "woocommerce") ],
      [ key => "Downloads", value => __("Downloads", "woocommerce") ],
      [ key => "Downloadable", value => __("Downloadable", "woocommerce") ],
      [ key => "Downloadable files", value => __("Downloadable files", "woocommerce") ],
      [ key => "Download limit", value => __("Download limit", "woocommerce") ],
      [ key => "Download expiry", value => __("Download expiry", "woocommerce") ],
      [ key => "Draft", value => __("Draft", "woocommerce") ],
      [ key => "Drop attributes here", value => __("Drop attributes here", "woocommerce") ],
      [ key => "Drop product here", value => __("Drop product here", "woocommerce") ],
      [ key => "Duplicate", value => __("Duplicate", "woocommerce") ],
      [ key => "Duplicate product", value => __("Duplicate product", "woocommerce") ],
      [ key => "Duplicate tag", value => __("Duplicate tag", "woocommerce") ],
      [ key => "Duplicate category", value => __("Duplicate category", "woocommerce") ],
      [ key => "Duplicate customer", value => __("Duplicate customer", "woocommerce") ],
      [ key => "Duplicate order", value => __("Duplicate order", "woocommerce") ],
      [ key => "Edit", value => __("Edit", "woocommerce") ],
      [ key => "Email", value => __("Email", "woocommerce") ],
      [ key => "EditSale", value => __("Edit sale", "woocommerce") ],
      [ key => "Enable", value => __("Enable", "woocommerce") ],
      [ key => "Enable pretty permalinks", value => __("Enable pretty permalinks", "woocommerce") ],
      [ key => "ExternalProduct", value => __("External product", "woocommerce") ],
      [ key => "Featured", value => __("Featured", "woocommerce") ],
      [ key => "Filter", value => __("Filter", "woocommerce") ],
      [ key => "Filter categories", value => __("Filter categories", "woocommerce") ],
      [ key => "Filter search", value => __("Filter search", "woocommerce") ],
      [ key => "Fixed", value => __("Fixed", "woocommerce") ],
      [ key => "First name", value => __("First name", "woocommerce") ],
      [ key => "FirstName", value => __("First name", "woocommerce") ],
      [ key => "From", value => __("From", "woocommerce") ],
      [ key => "for", value => __("for", "woocommerce") ],
      [ key => "Gallery", value => __("Gallery", "woocommerce") ],
      [ key => "General", value => __("General", "woocommerce") ],
      [ key => "Grouped product", value => __("Grouped product", "woocommerce") ],
      [ key => "Global attributes", value => __("Global attributes", "woocommerce") ],
      [ key => "Global attribute id or 0", value => __("Global attribute id or 0", "woocommerce") ],
      [ key => "Height", value => __("Height", "woocommerce") ],
      [ key => "Invalid or duplicated SKU", value => __("Invalid or duplicated SKU", "woocommerce") ],
      [ key => "Id", value => __("Id", "woocommerce") ],
      [ key => "Italic", value => __("Italic", "woocommerce") ],
      [ key => "Items", value => __("Items", "woocommerce") ],
      [ key => "Image", value => __("Image", "woocommerce") ],
      [ key => "Insert image from gallery", value => __("Insert image from gallery", "woocommerce") ],
      [ key => "Insert video from gallery", value => __("Insert video from gallery", "woocommerce") ],
      [ key => "Insert link from gallery", value => __("Insert link from gallery", "woocommerce") ],
      [ key => "Insert line break", value => __("Insert line break", "woocommerce") ],
      [ key => "Increase", value => __("Increase", "woocommerce") ],
      [ key => "Inventory", value => __("Inventory", "woocommerce") ],
      [ key => "Install / Update", value => __("Install / Update", "woocommerce") ],
      [ key => "Key", value => __("Key", "woocommerce") ],
      [ key => "Label", value => __("Label", "woocommerce") ],
      [ key => "Last name", value => __("Last name", "woocommerce") ],
      [ key => "LastName", value => __("Last name", "woocommerce") ],
      [ key => "Length", value => __("Length", "woocommerce") ],
      [ key => "Link", value => __("Link", "woocommerce") ],
      [ key => "License key", value => __("License key", "woocommerce") ],
      [ key => "Manager", value => __("Manager", "woocommerce") ],
      [ key => "Manage stock", value => __("Manage stock", "woocommerce") ],
      [ key => "Menu order position", value => __("Menu order position", "woocommerce") ],
      [ key => "Meta", value => __("Meta", "woocommerce") ],
      [ key => "Meta data", value => __("Meta data", "woocommerce") ],
      [ key => "Minimum one option required", value => __("Minimum one option required", "woocommerce") ],
      [ key => "Missing", value => __("Missing", "woocommerce") ],
      [ key => "Might be hidden for your customers", value => __("Might be hidden for your customers", "woocommerce") ],
      [ key => "Name", value => __("Name", "woocommerce") ],
      [ key => "New", value => __("New", "woocommerce") ],
      [ key => "Number", value => __("Number", "woocommerce") ],
      [ key => "No", value => __("No", "woocommerce") ],
      [ key => "Notify", value => __("Notify", "woocommerce") ],
      [ key => "None", value => __("None", "woocommerce") ],
      [ key => "NoData", value => __("No data", "woocommerce") ],
      [ key => "No reviews yet", value => __("No reviews yet", "woocommerce") ],
      [ key => "Open", value => __("Open", "woocommerce") ],
      [ key => "Option", value => __("Option", "woocommerce") ],
      [ key => "Options", value => __("Options", "woocommerce") ],
      [ key => "Order by", value => __("Order by", "woocommerce") ],
      [ key => "Ordered list", value => __("Ordered list", "woocommerce") ],
      [ key => "or higher required", value => __("or higher required", "woocommerce") ],
      [ key => "Parent id is not 0", value => __("Parent id is not 0", "woocommerce") ],
      [ key => "Paragraph", value => __("Paragraph", "woocommerce") ],
      [ key => "Pay with card", value => __("Pay with card", "woocommerce") ],
      [ key => "Pending", value => __("Pending", "woocommerce") ],
      [ key => "PerPage", value => __("Per Page", "woocommerce") ],
      [ key => "Percentage", value => __("Percentage", "woocommerce") ],
      [ key => "PerRow", value => __("Per Row", "woocommerce") ],
      [ key => "Permalink settings", value => __("Permalink settings", "woocommerce") ],
      [ key => "Permalinks can not be 'plain'", value => __("Permalinks can not be 'plain'", "woocommerce") ],
      [ key => "Please input", value => __("Please input", "woocommerce") ],
      [ key => "Please follow these steps to see StorePilot in action", value => __("Please follow these steps to see StorePilot in action", "woocommerce") ],
      [ key => "Prepend", value => __("Prepend", "woocommerce") ],
      [ key => "Price", value => __("Price", "woocommerce") ],
      [ key => "Price & sale", value => __("Price & sale", "woocommerce") ],
      [ key => "Private", value => __("Private", "woocommerce") ],
      [ key => "Preformatted", value => __("Preformatted", "woocommerce") ],
      [ key => "Pretty permalinks not set", value => __("Pretty permalinks not set", "woocommerce") ],
      [ key => "Pretty permalinks set successfully", value => __("Pretty permalinks set successfully", "woocommerce") ],
      [ key => "Pretty permalinks is enabled, please refresh your page", value => __("Pretty permalinks is enabled, please refresh your page", "woocommerce") ],
      [ key => "Product", value => __("Product", "woocommerce") ],
      [ key => "Products", value => __("Products", "woocommerce") ],
      [ key => "Product title", value => __("Product title", "woocommerce") ],
      [ key => "Product slug", value => __("Product slug", "woocommerce") ],
      [ key => "Position", value => __("Position", "woocommerce") ],
      [ key => "Published", value => __("Published", "woocommerce") ],
      [ key => "Purchase note", value => __("Purchase note", "woocommerce") ],
      [ key => "PurchaseNote", value => __("Purchase note", "woocommerce") ],
      [ key => "Quote", value => __("Quote", "woocommerce") ],
      [ key => "Rack", value => __("Rack", "woocommerce") ],
      [ key => "Reset to 0", value => __("Reset to 0", "woocommerce") ],
      [ key => "Refresh", value => __("Refresh", "woocommerce") ],
      [ key => "Return to product manager", value => __("Return to product manager", "woocommerce") ],
      [ key => "Reviews", value => __("Reviews", "woocommerce") ],
      [ key => "renaming", value => __("renaming", "woocommerce") ],
      [ key => "Replace", value => __("Replace", "woocommerce") ],
      [ key => "Regular price", value => __("Regular price", "woocommerce") ],
      [ key => "Remove", value => __("Remove", "woocommerce") ],
      [ key => "Remove from category", value => __("Remove from category", "woocommerce") ],
      [ key => "Rename", value => __("Rename", "woocommerce") ],
      [ key => "Role", value => __("Role", "woocommerce") ],
      [ key => "Sale!", value => __("Sale!", "woocommerce") ],
      [ key => "Sale", value => __("Sale", "woocommerce") ],
      [ key => "Sale active", value => __("Sale active", "woocommerce") ],
      [ key => "Sale flash", value => __("Sale flash", "woocommerce") ],
      [ key => "Sale inactive", value => __("Sale inactive", "woocommerce") ],
      [ key => "Sale scheduled", value => __("Sale scheduled", "woocommerce") ],
      [ key => "Sale price", value => __("Sale price", "woocommerce") ],
      [ key => "Save", value => __("Save", "woocommerce") ],
      [ key => "Save global attribute before adding terms", value => __("Save global attribute before adding terms", "woocommerce") ],
      [ key => "Schedule", value => __("Schedule", "woocommerce") ],
      [ key => "Search", value => __("Search", "woocommerce") ],
      [ key => "Select", value => __("Select", "woocommerce") ],
      [ key => "Selected", value => __("Selected", "woocommerce") ],
      [ key => "Select variation", value => __("Select variation", "woocommerce") ],
      [ key => "Select global attribute", value => __("Select global attribute", "woocommerce") ],
      [ key => "Select header", value => __("Select header", "woocommerce") ],
      [ key => "Select file", value => __("Select file", "woocommerce") ],
      [ key => "Select files", value => __("Select files", "woocommerce") ],
      [ key => "Select image", value => __("Select image", "woocommerce") ],
      [ key => "Select images", value => __("Select images", "woocommerce") ],
      [ key => "Select video", value => __("Select video", "woocommerce") ],
      [ key => "Settings", value => __("Settings", "woocommerce") ],
      [ key => "Stock", value => __("In stock", "woocommerce") ],
      [ key => "Show", value => __("Show", "woocommerce") ],
      [ key => "Shop page display", value => __("Shop page display", "woocommerce") ],
      [ key => "Set a permalink which is not plain and save", value => __("Set a permalink which is not plain and save", "woocommerce") ],
      [ key => "Shop configuration", value => __("Shop configuration", "woocommerce") ],
      [ key => "Shop Settings", value => __("Shop Settings", "woocommerce") ],
      [ key => "Shortcut", value => __("Shortcut", "woocommerce") ],
      [ key => "Shortcut: delete", value => __("Shortcut: delete", "woocommerce") ],
      [ key => "Short description", value => __("Short description", "woocommerce") ],
      [ key => "ShortDescription", value => __("Short description", "woocommerce") ],
      [ key => "Shipping", value => __("Shipping", "woocommerce") ],
      [ key => "Simple product", value => __("Simple product", "woocommerce") ],
      [ key => "SKU", value => __("SKU", "woocommerce") ],
      [ key => "Slug", value => __("Slug", "woocommerce") ],
      [ key => "Sold individually", value => __("Sold individually", "woocommerce") ],
      [ key => "Sort by", value => __("Sort by", "woocommerce") ],
      [ key => "Status", value => __("Status", "woocommerce") ],
      [ key => "Start composing", value => __("Start composing...", "woocommerce") ],
      [ key => "Stock keeping unit", value => __("Stock keeping unit", "woocommerce") ],
      [ key => "Stock quantity", value => __("Stock quantity", "woocommerce") ],
      [ key => "Strikethrough", value => __("Strikethrough", "woocommerce") ],
      [ key => "Subcategories", value => __("Subcategories", "woocommerce") ],
      [ key => "Tag", value => __("Tag", "woocommerce") ],
      [ key => "Tags", value => __("Tags", "woocommerce") ],
      [ key => "Tag settings", value => __("Tag settings", "woocommerce") ],
      [ key => "Tag title", value => __("Tag title", "woocommerce") ],
      [ key => "Tag slug", value => __("Tag slug", "woocommerce") ],
      [ key => "Tax", value => __("Tax", "woocommerce") ],
      [ key => "Taxable", value => __("Taxable", "woocommerce") ],
      [ key => "Tax status", value => __("Tax status", "woocommerce") ],
      [ key => "Tax class", value => __("Tax class", "woocommerce") ],
      [ key => "Terms", value => __("Terms", "woocommerce") ],
      [ key => "Text", value => __("Text", "woocommerce") ],
      [ key => "Tax is included in prices", value => __("Tax is included in prices", "woocommerce") ],
      [ key => "Tax is excluded from prices", value => __("Tax is excluded from prices", "woocommerce") ],
      [ key => "Text color", value => __("Text color", "woocommerce") ],
      [ key => "License for StorePilot", value => __("License for StorePilot", "woocommerce") ],
      [ key => "This can not be undone!", value => __("This can not be undone!", "woocommerce") ],
      [ key => "This page", value => __("This page", "woocommerce") ],
      [ key => "This product is trashed!", value => __("This product is trashed!", "woocommerce") ],
      [ key => "- Is currently active on this machine", value => __("- Is currently active on this machine", "woocommerce") ],
      [ key => "Title", value => __("Title", "woocommerce") ],
      [ key => "Title & slug", value => __("Title & slug", "woocommerce") ],
      [ key => "To", value => __("To", "woocommerce") ],
      [ key => "Total", value => __("Total", "woocommerce") ],
      [ key => "Trash", value => __("Trash", "woocommerce") ],
      [ key => "Trash all", value => __("Trash all", "woocommerce") ],
      [ key => "Trashed", value => __("Trashed", "woocommerce") ],
      [ key => "Type", value => __("Type", "woocommerce") ],
      [ key => "Update", value => __("Update", "woocommerce") ],
      [ key => "Upsells", value => __("Upsells", "woocommerce") ],
      [ key => "Unselect all", value => __("Unselect all", "woocommerce") ],
      [ key => "Url", value => __("Url", "woocommerce") ],
      [ key => "Underline", value => __("Underline", "woocommerce") ],
      [ key => "Use selected media", value => __("Use selected media", "woocommerce") ],
      [ key => "Username", value => __("Username", "woocommerce") ],
      [ key => "Value", value => __("Value", "woocommerce") ],
      [ key => "Validate", value => __("Validate", "woocommerce") ],
      [ key => "Variation", value => __("Variation", "woocommerce") ],
      [ key => "Variations", value => __("Variations", "woocommerce") ],
      [ key => "Variable product", value => __("Variable product", "woocommerce") ],
      [ key => "View", value => __("View", "woocommerce") ],
      [ key => "Visible", value => __("Visible", "woocommerce") ],
      [ key => "Virtual", value => __("Virtual", "woocommerce") ],
      [ key => "Waiting for payment...", value => __("Waiting for payment...", "woocommerce") ],
      [ key => "Weight", value => __("Weight", "woocommerce") ],
      [ key => "Width", value => __("Width", "woocommerce") ],
      [ key => "Woocommerce settings", value => __("Woocommerce settings", "woocommerce") ],
      [ key => "Woocommerce activation succeeded", value => __("Woocommerce activation succeeded", "woocommerce") ],
      [ key => "Woocommerce activation succeeded, please refresh your page", value => __("Woocommerce activation succeeded, please refresh your page", "woocommerce") ],
      [ key => "You are autorized", value => __("You are autorized", "woocommerce") ],
      [ key => "Yes", value => __("Yes", "woocommerce") ]
    ]);
  }

  /**
   * Retrieves the query params for collections of attachments.
   *
   * @since 1.0.0
   * @access public
   *
   * @return array Query parameters for the attachment collection as an array.
   */
  public function get_params() {
    $params['status']['default'] = 'inherit';
    $params['status']['items']['enum'] = array( 'inherit', 'private', 'trash' );

    $params['locale'] = array(
      'default'           => null,
      'description'       => __( 'Language code to retrieve translation' ),
      'type'              => 'string'
    );

    return $params;
  }

}
