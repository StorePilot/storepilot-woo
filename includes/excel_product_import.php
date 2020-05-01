<?php

if (!defined('ABSPATH')) {
  exit;
}

require __DIR__ . '/../vendor/autoload.php';

function excel_product_import($file, $args) {

  $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(
    $file['file']
  );
  $sheet = $spreadsheet->getActiveSheet();

  $headers = [];
  $rows = 0;
  $i = 0;
  foreach ($sheet->getRowIterator() as $row) {
    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    
    $post = [];
    $hasValue = false;
    $ii = 0;
    foreach ($cellIterator as $cell) {
      if ($i === 0) {
        $headers[] = $cell->getValue();
      } else {
        $key = str_replace(' ', '_', strtolower($headers[$ii]));
        $post[$key] = (string) $cell->getValue();
        if ($post[$key]) $hasValue = true;
      }
      $ii++;
    }
    if ($hasValue) {
      $rows++;
      $product = new WC_Product();
      if (isset($post['id']) && !empty($post['id'])) {
        $product = wc_get_product($post['id']);
      }
      if (isset($post['variation_id'])) {
        // @todo - (variations is currently not supported)
      }
      if (isset($post['name'])) {
        $product->set_name($post['name']);
      }
      if (isset($post['slug'])) {
        $product->set_slug($post['slug']);
      }
      if (isset($post['regular_price'])) {
        $post['regular_price'] = str_replace('.', ',', $post['regular_price']);
        $product->set_regular_price($post['regular_price']);
      }
      if (isset($post['sale_price'])) {
        $post['sale_price'] = str_replace('.', ',', $post['sale_price']);
        $product->set_sale_price($post['sale_price']);
      }
      if (isset($post['categories'])) {
        $post['categories'] = str_replace('.', ',', $post['categories']);
        $product->set_category_ids(explode(',', $post['categories']));
      }
      if (isset($post['tags'])) {
        $post['tags'] = str_replace('.', ',', $post['tags']);
        $product->set_tag_ids(explode(',', $post['tags']));
      }
      if (isset($post['description'])) {
        $product->set_description($post['description']);
      }
      if (isset($post['short_description'])) {
        $product->set_short_description($post['short_description']);
      }
      if (isset($post['purchase_note'])) {
        $product->set_purchase_note($post['purchase_note']);
      }
      if (isset($post['sale'])) {
        // readonly
      }
      if (isset($post['in_stock'])) {
        // readonly
      }
      if (isset($post['status'])) {
        $product->set_status($post['status']);
      }
      if (isset($post['images'])) {
        $post['images'] = str_replace('.', ',', $post['images']);
        $product->set_gallery_image_ids(explode(',', $post['images']));
      }
      if (isset($post['type'])) {
        // readonly
      }
      if (isset($post['variations'])) {
        // readonly
      }
      if (isset($post['downloadable'])) {
        if ($post['downloadable'] !== '')
          $product->set_downloadable($post['downloadable'] === 'true' ? true : false);
      }
      if (isset($post['cross-sells'])) {
        $product->set_cross_sell_ids(explode(',', $post['cross-sells']));
      }
      if (isset($post['upsells'])) {
        $product->set_upsell_ids(explode(',', $post['upsells']));
      }
      if (isset($post['featured'])) {
        if ($post['featured'] !== '')
          $product->set_featured($post['featured'] === 'true' ? true : false);
      }

      $product->save();
    }
    
    $i++;
  }

  $file['args'] = $args;
  $file['type'] = 'product';
  $file['headers'] = $headers;
  $file['rows'] = $rows;
  return wp_send_json($file);

}
