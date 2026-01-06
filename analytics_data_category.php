<?php
$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}

$data = [];


$sql_products = "
  SELECT p.category, SUM(oi.quantity) AS total
  FROM order_items oi
  JOIN products p ON oi.item_reference_id = p.product_id
  JOIN orders o ON oi.order_id = o.order_id
  WHERE oi.item_type = 'product' 
    AND o.order_datetime >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
  GROUP BY p.category
";
$result = $conn->query($sql_products);
while ($row = $result->fetch_assoc()) {
    $data[$row['category']] = (int)$row['total'];
}


$sql_sets = "
  SELECT SUM(oi.quantity) AS total
  FROM order_items oi
  JOIN sets s ON oi.item_reference_id = s.set_id
  JOIN orders o ON oi.order_id = o.order_id
  WHERE oi.item_type = 'set'
    AND o.order_datetime >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
";
$result = $conn->query($sql_sets);
if ($row = $result->fetch_assoc()) {
    $data['set'] = (int)$row['total'];
}


$sql_custom = "
  SELECT SUM(oi.quantity) AS total
  FROM order_items oi
  JOIN orders o ON oi.order_id = o.order_id
  WHERE oi.item_type = 'custom'
    AND o.order_datetime >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
";
$result = $conn->query($sql_custom);
if ($row = $result->fetch_assoc()) {
    $data['custom'] = (int)$row['total'];
}

header('Content-Type: application/json');
echo json_encode($data);
?>
