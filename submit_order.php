<?php
session_start();
date_default_timezone_set('Europe/Kyiv');

$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}

if (!isset($_SESSION['customer_id']) || $_SESSION['customer_logged_in'] !== true) {
    die("Unauthorized");
}

$customer_id = (int)$_SESSION['customer_id'];
$type_delivery = $_POST['delivery_type'] ?? null;

if (!in_array($type_delivery, ['self_pickup', 'address_pickup'])) {
    die("Invalid delivery method.");
}


$basketStmt = $conn->prepare("SELECT basket_id FROM baskets WHERE customer_id = ? AND is_ordered = 0 LIMIT 1");
$basketStmt->bind_param("i", $customer_id);
$basketStmt->execute();
$basketRes = $basketStmt->get_result();

if ($basketRes->num_rows === 0) {
    die("Кошик порожній");
}

$basket_id = $basketRes->fetch_assoc()['basket_id'];


$total_price = 0;


$productRes = $conn->query("
    SELECT p.price, p.promotion_id, bp.quantity, 
           pr.discount_percentage, pr.start_date, pr.end_date
    FROM basket_products bp
    JOIN products p ON bp.product_id = p.product_id
    LEFT JOIN promotions pr ON p.promotion_id = pr.promotion_id
    WHERE bp.basket_id = $basket_id
");

while ($row = $productRes->fetch_assoc()) {
    $price = $row['price'];
    $quantity = $row['quantity'];
    $today = date('Y-m-d');

    if ($row['promotion_id'] && $row['start_date'] <= $today && $row['end_date'] >= $today) {
        $price *= (1 - $row['discount_percentage'] / 100);
    }

    $total_price += $price * $quantity;
}


$setRes = $conn->query("SELECT s.price, bs.quantity FROM basket_sets bs JOIN sets s ON bs.set_id = s.set_id WHERE bs.basket_id = $basket_id");
while ($row = $setRes->fetch_assoc()) {
    $total_price += $row['price'] * $row['quantity'];
}


$customRes = $conn->query("SELECT pc.price, bc.quantity FROM basket_customizations bc JOIN pizza_customizations pc ON bc.customization_id = pc.customization_id WHERE bc.basket_id = $basket_id");
while ($row = $customRes->fetch_assoc()) {
    $total_price += $row['price'] * $row['quantity'];
}


$order_datetime = $_POST['ready_time'] ?? null;
$now = new DateTime();
$minTime = clone $now;
$minTime->modify('+40 minutes');

if (!empty($_POST['ready_time'])) {
    $submitted = DateTime::createFromFormat('Y-m-d\TH:i', $_POST['ready_time']);
    if (!$submitted) {
        die("<script>alert('Invalid date format.'); window.history.back();</script>");
    }


    if ($submitted < $minTime) {
        die("<script>alert('Delivery time must be at least 40 minutes from now.'); window.history.back();</script>");
    }


    $hour = (int)$submitted->format('H');
    if ($hour < 8 || $hour >= 22) {
        die("<script>alert('Orders are only accepted between 08:00 and 22:00.'); window.history.back();</script>");
    }

    $order_datetime = $submitted->format('Y-m-d H:i:s');
} else {

    $defaultTime = clone $minTime;
    if ((int)$defaultTime->format('H') < 8) {
        $defaultTime->setTime(8, 0);
    } elseif ((int)$defaultTime->format('H') >= 22) {
        $defaultTime->modify('+1 day')->setTime(8, 0);
    }
    $order_datetime = $defaultTime->format('Y-m-d H:i:s');
}




$orderStmt = $conn->prepare("
    INSERT INTO orders (order_datetime, total_price, customer_id, type_delivery, order_status, basket_id)
    VALUES (?, ?, ?, ?, 'getting_ready', ?)
");
$orderStmt->bind_param("sdisi", $order_datetime, $total_price, $customer_id, $type_delivery, $basket_id);
$orderStmt->execute();

$order_id = $conn->insert_id;




$productRes->data_seek(0);
$productDetails = $conn->query("
    SELECT p.product_id, p.name, p.description, p.image_path, p.price, p.promotion_id, bp.quantity,
           pr.discount_percentage, pr.start_date, pr.end_date
    FROM basket_products bp
    JOIN products p ON bp.product_id = p.product_id
    LEFT JOIN promotions pr ON p.promotion_id = pr.promotion_id
    WHERE bp.basket_id = $basket_id
");

while ($row = $productDetails->fetch_assoc()) {
    $price = $row['price'];
    $quantity = $row['quantity'];
    $today = date('Y-m-d');

    if ($row['promotion_id'] && $row['start_date'] <= $today && $row['end_date'] >= $today) {
        $price *= (1 - $row['discount_percentage'] / 100);
    }

    $stmt = $conn->prepare("
        INSERT INTO order_items (order_id, item_type, item_reference_id, name, description, image_path, quantity, unit_price)
        VALUES (?, 'product', ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iissssd", $order_id, $row['product_id'], $row['name'], $row['description'], $row['image_path'], $quantity, $price);
    $stmt->execute();
    $stmt->close();
}


$setDetails = $conn->query("
    SELECT s.set_id, s.name, s.description, s.image_path, s.price, bs.quantity
    FROM basket_sets bs
    JOIN sets s ON bs.set_id = s.set_id
    WHERE bs.basket_id = $basket_id
");

while ($row = $setDetails->fetch_assoc()) {
    $stmt = $conn->prepare("
        INSERT INTO order_items (order_id, item_type, item_reference_id, name, description, image_path, quantity, unit_price)
        VALUES (?, 'set', ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iissssd", $order_id, $row['set_id'], $row['name'], $row['description'], $row['image_path'], $row['quantity'], $row['price']);
    $stmt->execute();
    $stmt->close();
}


$customDetails = $conn->query("
    SELECT bc.customization_id, bc.quantity, pc.price, c.name AS crust_name
    FROM basket_customizations bc
    JOIN pizza_customizations pc ON bc.customization_id = pc.customization_id
    JOIN crusts c ON pc.crust_id = c.crust_id
    WHERE bc.basket_id = $basket_id
");

while ($row = $customDetails->fetch_assoc()) {
    $name = "Кастомна піца";
    $description = "Бортик: " . $row['crust_name'] . "||customization_id=" . $row['customization_id'];

    $img = "uploads/custom_pizza.jpg";
    $stmt = $conn->prepare("
        INSERT INTO order_items (order_id, item_type, item_reference_id, name, description, image_path, quantity, unit_price)
        VALUES (?, 'custom', ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iissssd", $order_id, $row['customization_id'], $name, $description, $img, $row['quantity'], $row['price']);
    $stmt->execute();
    $stmt->close();
}

$orderStmt->close();


$now = date("Y-m-d H:i:s");
$updateBasket = $conn->prepare("UPDATE baskets SET created_at = ?, is_ordered = 1 WHERE basket_id = ?");
$updateBasket->bind_param("si", $now, $basket_id);
$updateBasket->execute();
$updateBasket->close();


header("Location: client_basket.php?success=1");
exit;
?>
