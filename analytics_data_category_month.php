<?php
$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}

$data = [];


$sql_products = "
  SELECT 
    DATE(o.order_datetime) AS order_date, 
    p.category AS category, 
    SUM(oi.quantity) AS total
  FROM order_items oi
  JOIN products p ON oi.item_reference_id = p.product_id
  JOIN orders o ON oi.order_id = o.order_id
  WHERE oi.item_type = 'product' 
    AND o.order_datetime BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND LAST_DAY(CURDATE())
  GROUP BY order_date, p.category
";

$result = $conn->query($sql_products);
while ($row = $result->fetch_assoc()) {
    $date = $row['order_date'];
    $category = $row['category'];
    $quantity = (int)$row['total'];

    if (!isset($data[$category])) {
        $data[$category] = [];
    }
    $data[$category][$date] = $quantity;
}


$sql_sets = "
  SELECT 
    DATE(o.order_datetime) AS order_date, 
    'set' AS category, 
    SUM(oi.quantity) AS total
  FROM order_items oi
  JOIN sets s ON oi.item_reference_id = s.set_id
  JOIN orders o ON oi.order_id = o.order_id
  WHERE oi.item_type = 'set'
    AND o.order_datetime BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND LAST_DAY(CURDATE())
  GROUP BY order_date
";

$result = $conn->query($sql_sets);
while ($row = $result->fetch_assoc()) {
    $date = $row['order_date'];
    $quantity = (int)$row['total'];

    if (!isset($data['set'])) {
        $data['set'] = [];
    }
    $data['set'][$date] = $quantity;
}


$sql_custom = "
  SELECT 
    DATE(o.order_datetime) AS order_date, 
    'custom' AS category, 
    SUM(oi.quantity) AS total
  FROM order_items oi
  JOIN pizza_customizations pc ON oi.item_reference_id = pc.customization_id
  JOIN orders o ON oi.order_id = o.order_id
  WHERE oi.item_type = 'custom'
    AND o.order_datetime BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND LAST_DAY(CURDATE())
  GROUP BY order_date
";

$result = $conn->query($sql_custom);
while ($row = $result->fetch_assoc()) {
    $date = $row['order_date'];
    $quantity = (int)$row['total'];

    if (!isset($data['custom'])) {
        $data['custom'] = [];
    }
    $data['custom'][$date] = $quantity;
}

header('Content-Type: application/json');
echo json_encode($data);
?>
