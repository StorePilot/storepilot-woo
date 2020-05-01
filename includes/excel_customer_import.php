<?php

if (!defined('ABSPATH')) {
  exit;
}

require __DIR__ . '/../vendor/autoload.php';

function excel_customer_import($file, $args)
{
$test = [];
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
    if (
      $hasValue &&(
        (isset($post['email']) && $post['email'] && isset($post['username']) && $post['username']) ||
        (isset($post['id']) && $post['id'])
      )
    ) {
      $rows++;

      
      if (isset($post['id']) && !empty($post['id'])) {
        $customer = new WC_Customer($post['id']);
      } else {
        $user_id = wc_create_new_customer(
          $post['email'],
          $post['username'],
          wp_generate_password()
        );
        $customer = new WC_Customer($user_id);
        $customer->set_role('customer');
      }

      if (isset($post['first_name'])) {
        $customer->set_first_name($post['first_name']);
      }

      if (isset($post['last_name'])) {
        $customer->set_last_name($post['last_name']);
      }

      if (isset($post['username'])) {
        $customer->set_username($post['username']);
      }

      if (isset($post['email'])) {
        $customer->set_email($post['email']);
      }

      if (isset($post['role'])) {
        $customer->set_role($post['role']);
      }

      if (isset($post['paying_customer'])) {
        // readonly
      }

      if (isset($post['total_orders'])) {
        // readonly;
      }

      if (isset($post['total_spent'])) {
        // readonly
      }

      if (isset($post['billing_first_name'])) {
        $customer->set_billing_first_name($post['billing_first_name']);
      }

      if (isset($post['billing_last_name'])) {
        $customer->set_billing_last_name($post['billing_last_name']);
      }

      if (isset($post['billing_company'])) {
        $customer->set_billing_company($post['billing_company']);
      }

      if (isset($post['billing_address_1'])) {
        $customer->set_billing_address_1($post['billing_address_1']);
      }

      if (isset($post['billing_address_2'])) {
        $customer->set_billing_address_2($post['billing_address_2']);
      }

      if (isset($post['billing_city'])) {
        $customer->set_billing_city($post['billing_city']);
      }

      if (isset($post['billing_state'])) {
        $customer->set_billing_state($post['billing_state']);
      }

      if (isset($post['billing_postal_code'])) {
        $customer->set_billing_postcode($post['billing_postal_code']);
      }

      if (isset($post['billing_country'])) {
        $customer->set_billing_country($post['billing_country']);
      }

      if (isset($post['billing_email'])) {
        $customer->set_billing_email($post['billing_email']);
      }

      if (isset($post['billing_phone'])) {
        $customer->set_billing_phone($post['billing_phone']);
      }

      if (isset($post['shipping_first_name'])) {
        $customer->set_shipping_first_name($post['shipping_first_name']);
      }

      if (isset($post['shipping_last_name'])) {
        $customer->set_shipping_last_name($post['shipping_last_name']);
      }

      if (isset($post['shipping_company'])) {
        $customer->set_shipping_company($post['shipping_company']);
      }

      if (isset($post['shipping_address_1'])) {
        $customer->set_shipping_address_1($post['shipping_address_1']);
      }

      if (isset($post['shipping_address_2'])) {
        $customer->set_shipping_address_2($post['shipping_address_2']);
      }

      if (isset($post['shipping_city'])) {
        $customer->set_shipping_city($post['shipping_city']);
      }

      if (isset($post['shipping_state'])) {
        $customer->set_shipping_state($post['shipping_state']);
      }

      if (isset($post['shipping_postal_code'])) {
        $customer->set_shipping_postcode($post['shipping_postal_code']);
      }

      if (isset($post['shipping_country'])) {
        $customer->set_shipping_country($post['shipping_country']);
      }

      $customer->save();
      $test[] = $post;
    }

    $i++;
  }

  $file['args'] = $args;
  $file['type'] = 'customer';
  $file['headers'] = $headers;
  $file['rows'] = $rows;
  $file['test'] = $test;
  return wp_send_json($file);
}
