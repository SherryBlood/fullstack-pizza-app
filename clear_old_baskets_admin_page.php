<?php
$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");


$conn->query("
  DELETE FROM basket_customizations
  WHERE basket_id IN (
    SELECT basket_id FROM baskets
    WHERE is_ordered = 0 AND created_at < NOW() - INTERVAL 90 DAY
  )
");

$conn->query("
  DELETE FROM basket_products
  WHERE basket_id IN (
    SELECT basket_id FROM baskets
    WHERE is_ordered = 0 AND created_at < NOW() - INTERVAL 90 DAY
  )
");

$conn->query("
  DELETE FROM basket_sets
  WHERE basket_id IN (
    SELECT basket_id FROM baskets
    WHERE is_ordered = 0 AND created_at < NOW() - INTERVAL 90 DAY
  )
");

$conn->query("
  DELETE FROM baskets
  WHERE is_ordered = 0 AND created_at < NOW() - INTERVAL 90 DAY
");

$referrer = $_SERVER['HTTP_REFERER'] ?? 'orders_admin_page.php';
echo '<script>alert("Old carts cleared!"); window.location.href = "' . $referrer . '";</script>';
?>
