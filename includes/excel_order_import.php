<?php

if (!defined('ABSPATH')) {
  exit;
}

require __DIR__ . '/../vendor/autoload.php';

function excel_order_import($file, $args)
{

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
      $order = new WC_Order();
      
      if (isset($post['id']) && !empty($post['id'])) {
        $order = wc_get_order($post['id']);
      }

      if (isset($post['customer_id'])) {
        $order->set_customer_id($post['customer_id']);
      }

      if (isset($post['transaction_id'])) {
        $order->set_transaction_id($post['transaction_id']);
      }

      if (isset($post['status'])) {
        $order->set_status($post['status']);
      }

      if (isset($post['currency'])) {
        $order->set_currency($post['currency']);
      }

      if (isset($post['total'])) {
        $post['total'] = str_replace('.', ',', $post['total']);
        $order->set_total($post['total']);
      }

      if (isset($post['discount_total'])) {
        $post['discount_total'] = str_replace('.', ',', $post['discount_total']);
        $order->set_discount_total($post['discount_total']);
      }

      if (isset($post['discount_tax'])) {
        $post['discount_tax'] = str_replace('.', ',', $post['discount_tax']);
        $order->set_discount_tax($post['discount_tax']);
      }

      if (isset($post['shipping_total'])) {
        $post['shipping_total'] = str_replace('.', ',', $post['shipping_total']);
        $order->set_shipping_total($post['shipping_total']);
      }

      if (isset($post['shipping_tax'])) {
        $post['shipping_tax'] = str_replace('.', ',', $post['shipping_tax']);
        $order->set_shipping_tax($post['shipping_tax']);
      }

      if (isset($post['cart_tax'])) {
        $post['cart_tax'] = str_replace('.', ',', $post['cart_tax']);
        $order->set_cart_tax($post['cart_tax']);
      }

      if (isset($post['tax_in_prices'])) {
        $order->set_prices_include_tax($post['tax_in_prices']);
      }

      if (isset($post['payment_method'])) {
        $order->set_payment_method($post['payment_method']);
      }

      if (isset($post['payment_method_title'])) {
        $order->set_payment_method_title($post['payment_method_title']);
      }

      if (isset($post['parent_order_id'])) {
        $order->set_parent_id($post['parent_order_id']);
      }

      if (isset($post['customer_note'])) {
        $order->set_customer_note($post['customer_note']);
      }

      if (isset($post['billing_first_name'])) {
        $order->set_billing_first_name($post['billing_first_name']);
      }

      if (isset($post['billing_last_name'])) {
        $order->set_billing_last_name($post['billing_last_name']);
      }

      if (isset($post['billing_company'])) {
        $order->set_billing_company($post['billing_company']);
      }

      if (isset($post['billing_address_1'])) {
        $order->set_billing_address_1($post['billing_address_1']);
      }

      if (isset($post['billing_address_2'])) {
        $order->set_billing_address_2($post['billing_address_2']);
      }

      if (isset($post['billing_city'])) {
        $order->set_billing_city($post['billing_city']);
      }

      if (isset($post['billing_state'])) {
        $order->set_billing_state($post['billing_state']);
      }

      if (isset($post['billing_postal_code'])) {
        $order->set_billing_postcode($post['billing_postal_code']);
      }

      if (isset($post['billing_country'])) {
        $order->set_billing_country($post['billing_country']);
      }

      if (isset($post['billing_email'])) {
        $order->set_billing_email($post['billing_email']);
      }

      if (isset($post['billing_phone'])) {
        $order->set_billing_phone($post['billing_phone']);
      }

      if (isset($post['shipping_first_name'])) {
        $order->set_shipping_first_name($post['shipping_first_name']);
      }

      if (isset($post['shipping_last_name'])) {
        $order->set_shipping_last_name($post['shipping_last_name']);
      }

      if (isset($post['shipping_company'])) {
        $order->set_shipping_company($post['shipping_company']);
      }

      if (isset($post['shipping_address_1'])) {
        $order->set_shipping_address_1($post['shipping_address_1']);
      }

      if (isset($post['shipping_address_2'])) {
        $order->set_shipping_address_2($post['shipping_address_2']);
      }

      if (isset($post['shipping_city'])) {
        $order->set_shipping_city($post['shipping_city']);
      }

      if (isset($post['shipping_state'])) {
        $order->set_shipping_state($post['shipping_state']);
      }

      if (isset($post['shipping_postal_code'])) {
        $order->set_shipping_postcode($post['shipping_postal_code']);
      }

      if (isset($post['shipping_country'])) {
        $order->set_shipping_country($post['shipping_country']);
      }

      if (isset($post['date_completed'])) {
        $order->set_date_completed($post['date_completed']);
      }

      if (isset($post['date_paid'])) {
        $order->set_date_paid($post['date_paid']);
      }
  

      $order->save();
    }

    $i++;
  }

  $file['args'] = $args;
  $file['type'] = 'order';
  $file['headers'] = $headers;
  $file['rows'] = $rows;
  return wp_send_json($file);
}
