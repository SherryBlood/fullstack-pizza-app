<?php
session_start();

if (!isset($_SESSION['customer_logged_in']) || !isset($_SESSION['customer_id'])) {
    $referer = $_SERVER['HTTP_REFERER'] ?? 'main_client_page.php';
    echo "<script>alert('Please Log In!'); window.location.href = '$referer';</script>";
    exit;
}

$customer_id = $_SESSION['customer_id'];

$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}


$base_id = 1;
$stmt = $conn->prepare("SELECT price FROM pizza_base WHERE base_id = ?");
$stmt->bind_param("i", $base_id);
$stmt->execute();
$stmt->bind_result($base_price);
$stmt->fetch();
$stmt->close();

$total_price = floatval($base_price);


$crust_id = isset($_POST['client-basket-crust']) ? intval($_POST['client-basket-crust']) : 0;
$crust_price = 0;
$stmt = $conn->prepare("SELECT additional_price FROM crusts WHERE crust_id = ?");
$stmt->bind_param("i", $crust_id);
$stmt->execute();
$stmt->bind_result($crust_price);
$stmt->fetch();
$stmt->close();

$total_price += floatval($crust_price);


$selected_ingredients = [];
foreach ($_POST as $key => $value) {
    if (strpos($key, 'ingredient') === 0) {
        $ingredient_id = intval(str_replace('ingredient', '', $key));
        $selected_ingredients[] = $ingredient_id;
        $total_price += floatval($value);
    }
}


$stmt = $conn->prepare("INSERT INTO pizza_customizations (crust_id, price, base_id) VALUES (?, ?, ?)");
$stmt->bind_param("idi", $crust_id, $total_price, $base_id);
$stmt->execute();
$customization_id = $conn->insert_id;
$stmt->close();


if (!empty($selected_ingredients)) {
    $stmt = $conn->prepare("INSERT INTO pizza_ingredients (customization_id, ingredient_id) VALUES (?, ?)");
    foreach ($selected_ingredients as $ingredient_id) {
        $stmt->bind_param("ii", $customization_id, $ingredient_id);
        $stmt->execute();
    }
    $stmt->close();
}


$stmt = $conn->prepare("SELECT basket_id FROM baskets WHERE customer_id = ? AND is_ordered = 0");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$basket = $result->fetch_assoc();
$stmt->close();

if (!$basket) {
    $stmt = $conn->prepare("INSERT INTO baskets (customer_id, created_at, is_ordered) VALUES (?, NOW(), 0)");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $basket_id = $conn->insert_id;
    $stmt->close();
} else {
    $basket_id = $basket['basket_id'];
}


$stmt = $conn->prepare("INSERT INTO basket_customizations (basket_id, customization_id, quantity) VALUES (?, ?, 1)");
$stmt->bind_param("ii", $basket_id, $customization_id);
$stmt->execute();
$stmt->close();

$conn->close();

$referer = $_SERVER['HTTP_REFERER'] ?? 'main_client_page.php';
echo "<script>alert('Your pizza has been added to the cart!'); window.location.href = '$referer';</script>";
?>
