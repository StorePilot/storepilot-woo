<?php

if (!defined('ABSPATH')) {
  exit;
}

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function excel_customer_export($filter)
{

  $cnt = 0;

  $spreadsheet = new Spreadsheet();
  $sheet = $spreadsheet->getActiveSheet();

  $sheet->setCellValue('A1', 'ID');
  $sheet->setCellValue('B1', 'First name');
  $sheet->setCellValue('C1', 'Last name');
  $sheet->setCellValue('D1', 'Username');
  $sheet->setCellValue('E1', 'Email');
  $sheet->setCellValue('F1', 'Role');
  $sheet->setCellValue('G1', 'Paying customer');
  $sheet->setCellValue('H1', 'Total orders');
  $sheet->setCellValue('I1', 'Total spent');
  $sheet->setCellValue('J1', 'Billing first name');
  $sheet->setCellValue('K1', 'Billing last name');
  $sheet->setCellValue('L1', 'Billing company');
  $sheet->setCellValue('M1', 'Billing address 1');
  $sheet->setCellValue('N1', 'Billing address 2');
  $sheet->setCellValue('O1', 'Billing city');
  $sheet->setCellValue('P1', 'Billing state');
  $sheet->setCellValue('Q1', 'Billing postal code');
  $sheet->setCellValue('R1', 'Billing country');
  $sheet->setCellValue('S1', 'Billing email');
  $sheet->setCellValue('T1', 'Billing phone');
  $sheet->setCellValue('U1', 'Shipping first name');
  $sheet->setCellValue('V1', 'Shipping last name');
  $sheet->setCellValue('W1', 'Shipping company');
  $sheet->setCellValue('X1', 'Shipping address 1');
  $sheet->setCellValue('Y1', 'Shipping address 2');
  $sheet->setCellValue('Z1', 'Shipping city');
  $sheet->setCellValue('AA1', 'Shipping state');
  $sheet->setCellValue('AB1', 'Shipping postal code');
  $sheet->setCellValue('AC1', 'Shipping country');

  $sheet->setAutoFilter($sheet->calculateWorksheetDimension());

  try {
    $filter = json_decode($filter, true);
  } catch (Exception $e) {
  }

  $args = [
    'role' => 'customer',
    'posts_per_page' => isset($filter['limit']) ? $filter['limit'] : 100
  ];
  $args['s'] = isset($filter['search_value']) ? $filter['search_value'] : '';
  $query = new WP_User_Query($args);
  $posts = $query->get_results();

  $i = 2;
  foreach ($posts as $p) {
    $cnt++;

    $post = new WC_Customer($p->ID);

    $sheet->setCellValue('A' . $i, $p->ID);
    $sheet->setCellValue('B' . $i, $post->get_first_name());
    $sheet->setCellValue('C' . $i, $post->get_last_name());
    $sheet->setCellValue('D' . $i, $post->get_username());
    $sheet->setCellValue('E' . $i, $post->get_email());
    $sheet->setCellValue('F' . $i, $post->get_role());
    $sheet->setCellValue('G' . $i, $post->is_paying_customer() ? 'true' : 'false');
    $sheet->setCellValue('H' . $i, $post->get_order_count());
    $sheet->setCellValue('I' . $i, $post->get_total_spent());

    $sheet->setCellValue('J' . $i, $post->get_billing_first_name());
    $sheet->setCellValue('K' . $i, $post->get_billing_last_name());
    $sheet->setCellValue('L' . $i, $post->get_billing_company());
    $sheet->setCellValue('M' . $i, $post->get_billing_address_1());
    $sheet->setCellValue('N' . $i, $post->get_billing_address_2());
    $sheet->setCellValue('O' . $i, $post->get_billing_city());
    $sheet->setCellValue('P' . $i, $post->get_billing_state());
    $sheet->setCellValue('Q' . $i, $post->get_billing_postcode());
    $sheet->setCellValue('R' . $i, $post->get_billing_country());
    $sheet->setCellValue('S' . $i, $post->get_billing_email());
    $sheet->setCellValue('T' . $i, $post->get_billing_phone());

    $sheet->setCellValue('U' . $i, $post->get_shipping_first_name());
    $sheet->setCellValue('V' . $i, $post->get_shipping_last_name());
    $sheet->setCellValue('W' . $i, $post->get_shipping_company());
    $sheet->setCellValue('X' . $i, $post->get_shipping_address_1());
    $sheet->setCellValue('Y' . $i, $post->get_shipping_address_2());
    $sheet->setCellValue('Z' . $i, $post->get_shipping_city());
    $sheet->setCellValue('AA' . $i, $post->get_shipping_state());
    $sheet->setCellValue('AB' . $i, $post->get_shipping_postcode());
    $sheet->setCellValue('AC' . $i, $post->get_shipping_country());

    $i++;
  }

  foreach (array_merge(range('A', 'Z'), [
    'AA', 'AB', 'AC'
  ]) as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
  }
  $sheet->getStyle("A1:AC1")->getFont()->setBold(true);
  $sheet->freezePane("A2");

  $writer = new Xlsx($spreadsheet);
  $writer->save(__DIR__ . '/temp/storepilot_customer_export.xlsx');

  return wp_send_json([
    'count' => $cnt,
    'url' => plugin_dir_url(__DIR__) . 'includes/temp/storepilot_customer_export.xlsx'
  ]);
}
