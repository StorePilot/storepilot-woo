<?php

if (!defined('ABSPATH')) {
  exit;
}

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function excel_order_export($filter)
{

  $cnt = 0;

  $spreadsheet = new Spreadsheet();
  $sheet = $spreadsheet->getActiveSheet();

  $sheet->setCellValue('A1', 'ID');
  $sheet->setCellValue('B1', 'Customer ID');
  $sheet->setCellValue('C1', 'Transaction ID');
  $sheet->setCellValue('D1', 'Status');
  $sheet->setCellValue('E1', 'Currency');
  $sheet->setCellValue('F1', 'Total');
  $sheet->setCellValue('G1', 'Discount total');
  $sheet->setCellValue('H1', 'Discount tax');
  $sheet->setCellValue('I1', 'Shipping total');
  $sheet->setCellValue('J1', 'Shipping tax');
  $sheet->setCellValue('K1', 'Cart tax');
  $sheet->setCellValue('L1', 'Total tax');
  $sheet->setCellValue('M1', 'Tax in prices');
  $sheet->setCellValue('N1', 'Payment method');
  $sheet->setCellValue('O1', 'Payment method title');
  $sheet->setCellValue('P1', 'Parent order ID');
  $sheet->setCellValue('Q1', 'Customer note');
  $sheet->setCellValue('R1', 'Billing first name');
  $sheet->setCellValue('S1', 'Billing last name');
  $sheet->setCellValue('T1', 'Billing company');
  $sheet->setCellValue('U1', 'Billing address 1');
  $sheet->setCellValue('V1', 'Billing address 2');
  $sheet->setCellValue('W1', 'Billing city');
  $sheet->setCellValue('X1', 'Billing state');
  $sheet->setCellValue('Y1', 'Billing postal code');
  $sheet->setCellValue('Z1', 'Billing country');
  $sheet->setCellValue('AA1', 'Billing email');
  $sheet->setCellValue('AB1', 'Billing phone');
  $sheet->setCellValue('AC1', 'Shipping first name');
  $sheet->setCellValue('AD1', 'Shipping last name');
  $sheet->setCellValue('AE1', 'Shipping company');
  $sheet->setCellValue('AF1', 'Shipping address 1');
  $sheet->setCellValue('AG1', 'Shipping address 2');
  $sheet->setCellValue('AH1', 'Shipping city');
  $sheet->setCellValue('AI1', 'Shipping state');
  $sheet->setCellValue('AJ1', 'Shipping postal code');
  $sheet->setCellValue('AK1', 'Shipping country');
  $sheet->setCellValue('AL1', 'Date completed');
  $sheet->setCellValue('AM1', 'Date paid');

  $sheet->setAutoFilter($sheet->calculateWorksheetDimension());

  try {
    $filter = json_decode($filter, true);
  } catch (Exception $e) {
  }

  $args = [
    'numberposts' => isset($filter['limit']) ? $filter['limit'] : 100
  ];
  $args['s'] = isset($filter['search_value']) ? $filter['search_value'] : '';
  $posts = wc_get_orders($args);

  $i = 2;
  foreach ($posts as $p) {
    $cnt++;

    $post = new WC_Order($p->get_id());

    $sheet->setCellValue('A' . $i, $p->get_id());
    $sheet->setCellValue('B' . $i, $post->get_customer_id());
    $sheet->setCellValue('C' . $i, $post->get_transaction_id());
    $sheet->setCellValue('D' . $i, $post->get_status());
    $sheet->setCellValue('E' . $i, $post->get_currency());
    $sheet->setCellValue('F' . $i, $post->get_total());
    $sheet->setCellValue('G' . $i, $post->get_discount_total());
    $sheet->setCellValue('H' . $i, $post->get_discount_tax());
    $sheet->setCellValue('I' . $i, $post->get_shipping_total());
    $sheet->setCellValue('J' . $i, $post->get_shipping_tax());

    $sheet->setCellValue('K' . $i, $post->get_cart_tax());
    $sheet->setCellValue('L' . $i, $post->get_total_tax());
    $sheet->setCellValue('M' . $i, $post->get_prices_include_tax() ? 'true' : 'false');
    $sheet->setCellValue('N' . $i, $post->get_payment_method());
    $sheet->setCellValue('O' . $i, $post->get_payment_method_title());
    $sheet->setCellValue('P' . $i, $post->get_parent_id());
    $sheet->setCellValue('Q' . $i, $post->get_customer_note());

    $sheet->setCellValue('R' . $i, $post->get_billing_first_name());
    $sheet->setCellValue('S' . $i, $post->get_billing_last_name());
    $sheet->setCellValue('T' . $i, $post->get_billing_company());
    $sheet->setCellValue('U' . $i, $post->get_billing_address_1());
    $sheet->setCellValue('V' . $i, $post->get_billing_address_2());
    $sheet->setCellValue('W' . $i, $post->get_billing_city());
    $sheet->setCellValue('X' . $i, $post->get_billing_state());
    $sheet->setCellValue('Y' . $i, $post->get_billing_postcode());
    $sheet->setCellValue('Z' . $i, $post->get_billing_country());
    $sheet->setCellValue('AA' . $i, $post->get_billing_email());
    $sheet->setCellValue('AB' . $i, $post->get_billing_phone());

    $sheet->setCellValue('AC' . $i, $post->get_shipping_first_name());
    $sheet->setCellValue('AD' . $i, $post->get_shipping_last_name());
    $sheet->setCellValue('AE' . $i, $post->get_shipping_company());
    $sheet->setCellValue('AF' . $i, $post->get_shipping_address_1());
    $sheet->setCellValue('AG' . $i, $post->get_shipping_address_2());
    $sheet->setCellValue('AH' . $i, $post->get_shipping_city());
    $sheet->setCellValue('AI' . $i, $post->get_shipping_state());
    $sheet->setCellValue('AJ' . $i, $post->get_shipping_postcode());
    $sheet->setCellValue('AK' . $i, $post->get_shipping_country());

    $sheet->setCellValue('AL' . $i, $post->get_date_completed());
    $sheet->setCellValue('AM' . $i, $post->get_date_paid());

    $i++;
  }

  foreach (array_merge(range('A', 'Z'), [
    'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM'
  ]) as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
  }
  $sheet->getStyle("A1:AM1")->getFont()->setBold(true);
  $sheet->freezePane("A2");

  $writer = new Xlsx($spreadsheet);
  $writer->save(__DIR__ . '/temp/storepilot_order_export.xlsx');

  return wp_send_json([
    'count' => $cnt,
    'url' => plugin_dir_url(__DIR__) . 'includes/temp/storepilot_order_export.xlsx'
  ]);
}
