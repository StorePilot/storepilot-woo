<?php

if (!defined('ABSPATH')) {
  exit;
}

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function export_excel($type, $filter) {

  try {
    $filter = json_decode($filter, true);
  } catch(Exception $e) {}

  $cnt = 0;

  $spreadsheet = new Spreadsheet();
  $sheet = $spreadsheet->getActiveSheet();

  if (!$type || $type === 'products') {

    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'Name');
    $sheet->setCellValue('C1', 'Slug');
    $sheet->setCellValue('D1', 'Regular price');
    $sheet->setCellValue('E1', 'Sale price');
    $sheet->setCellValue('F1', 'Categories');
    $sheet->setCellValue('G1', 'Tags');
    $sheet->setCellValue('H1', 'Description');
    $sheet->setCellValue('I1', 'Short description');
    $sheet->setCellValue('J1', 'Purchase note');
    $sheet->setCellValue('K1', 'Sale');
    $sheet->setCellValue('L1', 'In stock');
    $sheet->setCellValue('M1', 'Status');
    $sheet->setCellValue('N1', 'Images');
    $sheet->setCellValue('O1', 'Type');
    $sheet->setCellValue('P1', 'Variations');
    $sheet->setCellValue('Q1', 'Downloadable');
    $sheet->setCellValue('R1', 'Cross-sells');
    $sheet->setCellValue('S1', 'Upsells');
    $sheet->setCellValue('T1', 'Featured');
    $sheet->setCellValue('U1', 'Variation ID');

    $sheet->setAutoFilter($sheet->calculateWorksheetDimension());

    $query = new WP_Query([
      'post_type' => 'product',
      'posts_per_page' => isset($filter['limit']) ? $filter['limit'] : 100
    ]);
    $posts = $query->posts;

    $i = 2;
    foreach ($posts as $p) {
      $cnt++;

      $post = wc_get_product($p->ID);

      $sheet->setCellValue('A' . $i, $p->ID);
      $sheet->setCellValue('B' . $i, $post->get_title());
      $sheet->setCellValue('C' . $i, $post->get_slug());
      $sheet->setCellValue('D' . $i, $post->get_regular_price());
      $sheet->setCellValue('E' . $i, $post->get_sale_price());

      $terms = get_the_terms($p->ID, 'product_cat');
      $arr = [];
      foreach ($terms as $term) {
        $arr[] = $term->term_id;
      }
      $sheet->setCellValue('F' . $i, implode(', ', $arr));

      $terms = get_the_terms($p->ID, 'product_tag');
      $arr = [];
      foreach ($terms as $term) {
        $arr[] = $term->term_id;
      }
      $sheet->setCellValue('G' . $i, implode(', ', $arr));

      $sheet->setCellValue('H' . $i, $post->get_data()['description']);
      $sheet->setCellValue('I' . $i, $post->get_data()['short_description']);

      $sheet->setCellValue('J' . $i, $post->get_purchase_note());

      $sheet->setCellValue('K' . $i, $post->is_on_sale() ? 'true' : 'false');
      $sheet->setCellValue('L' . $i, $post->is_in_stock() ? 'true' : 'false');
      $sheet->setCellValue('M' . $i, $post->get_status());

      $attachment_ids = $post->get_gallery_image_ids();
      $sheet->setCellValue('N' . $i, implode(', ', $attachment_ids));

      $sheet->setCellValue('O' . $i, $post->get_type());

      $children_ids = $post->get_children();
      $sheet->setCellValue('P' . $i, implode(', ', $children_ids));

      $sheet->setCellValue('Q' . $i, $post->is_downloadable() ? 'true' : 'false');
      
      $cross_sell_ids = $post->get_cross_sell_ids();
      $sheet->setCellValue('R' . $i, implode(', ', $cross_sell_ids));

      $upsell_ids = $post->get_upsell_ids();
      $sheet->setCellValue('S' . $i, implode(', ', $upsell_ids));
      $sheet->setCellValue('T' . $i, $post->is_featured() ? 'true' : 'false');

      // Variation ID
      // $sheet->setCellValue('U' . $i, implode(', ', $variation_id));
      $i++;

    }
  }

  foreach (range('A', 'U') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
  }
  $sheet->getStyle("A1:U1")->getFont()->setBold(true);
  $sheet->freezePane("A2");

  $writer = new Xlsx($spreadsheet);
  $writer->save(__DIR__ . '/temp/products.xlsx');

  return wp_send_json(['url' => plugin_dir_url(__DIR__) . 'includes/temp/products.xlsx']);

}

// If files is older than one hour, cleanup
$dir = __DIR__ . "/temp/";
foreach (glob($dir . "*") as $file) {
  if (time() - filectime($file) > 3600) {
    unlink($file);
  }
}
