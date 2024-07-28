<?php

$REDIS_HOST = '127.0.0.1';
$REDIS_PORT = '6379';

$redis = new Redis();
$connected = $redis->connect($REDIS_HOST, $REDIS_PORT);
$products = [];
if ( $connected  ) {
  $keys = $redis->keys('plaza_sheet_sale_product:*');
  if ($keys) {
    foreach ($keys as $key) {

      $time = $redis->hGet($key, 'time');

      if(time() > $time) {
        $product_id = explode(':', $key)[1];
        $products[] = ['product_id' => $product_id, 'time' => $time];
      }

    }

    $redis->del($keys);
    $redis->close();
  }
}

if( count($products) > 0 ) {
  foreach( $products as $product ) {
    $start = time();
    update_product_sale_price($product['product_id']);
    update_product_cache($product['product_id']);
    $elapsed = time() - $start;
    echo "updating ". $product['product_id'] ." sale price to 0 takes $elapsed seconds." . PHP_EOL;
  }
}

function update_product_sale_price( $product_id )
{
  $curl = curl_init();

  $urls = get_sheet_urls( $product_id );

  foreach( $urls as $url ){
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
    ));
    curl_exec($curl);
  }

  curl_close($curl);
}

function update_product_cache($product_id) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://afraa.shop/wp-json/plaza/v1/products/cache');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"product_id\": \"$product_id\"}");

    $headers = array();
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_exec($ch);
}

function get_sheet_urls( $product_id )
{
  return [
    "https://script.google.com/macros/s/AKfycbxJwUgED_3wmdevs4pxvIB090I5JJcO7hBLhsEnGN44SQexlI1IKXroY9hSxRvtICnMjQ/exec?spreadsheetId=162sq8ftUuV1BOR-v2idArqm1mFt04jc14JFP7VN3cCI&searchValue=$product_id&newSale=reset",
    "https://script.google.com/macros/s/AKfycbwrJL7SjCg22t5gjvwyNFryaJDY5SXO9rWfe51p-__HAMI6kVu9lDT6JGLu9C_MQLkUgw/exec?spreadsheetId=162sq8ftUuV1BOR-v2idArqm1mFt04jc14JFP7VN3cCI&searchValue=$product_id&newSale=reset",
    "https://script.google.com/macros/s/AKfycbxZTV5YfSnat4vR5-4JxjSVRnovW1Na-gHwcg53ysVYQA2Gyhtxoqa-advUpogI83sU/exec?spreadsheetId=162sq8ftUuV1BOR-v2idArqm1mFt04jc14JFP7VN3cCI&searchValue=$product_id&newSale=reset"
  ];
}

