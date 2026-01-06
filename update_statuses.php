<?php
$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

$now = date("Y-m-d H:i:s");

$conn->query("
    UPDATE orders
    SET order_status = 'wait_customer'
    WHERE order_status = 'getting_ready'
      AND type_delivery = 'self_pickup'
      AND order_datetime <= '$now'
");

$conn->query("
    UPDATE orders
    SET order_status = 'issue_courier'
    WHERE order_status = 'getting_ready'
      AND type_delivery = 'address_pickup'
      AND order_datetime <= '$now'
");

echo "updated";
