<?php
session_start();

if (!isset($_SESSION['customer_logged_in']) || !isset($_SESSION['customer_id'])) {
    $referer = $_SERVER['HTTP_REFERER'] ?? 'main_client_page.php';
    echo "<script>alert('Please Log In!'); window.location.href = '$referer';</script>";
    exit;
}

$customer_id = $_SESSION['customer_id'];
$product_id = (int)($_POST['id'] ?? 0);

if ($product_id <= 0) {
    echo '<script>alert("Invalid product ID."); window.history.back();</script>';
    exit;
}

$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}


$basketQuery = $conn->prepare("SELECT basket_id FROM baskets WHERE customer_id = ? AND is_ordered = 0 LIMIT 1");
$basketQuery->bind_param("i", $customer_id);
$basketQuery->execute();
$basketResult = $basketQuery->get_result();

if ($basketResult->num_rows > 0) {
    $basket = $basketResult->fetch_assoc();
    $basket_id = $basket['basket_id'];
} else {

    $createBasket = $conn->prepare("INSERT INTO baskets (customer_id, created_at, is_ordered) VALUES (?, NOW(), 0)");
    $createBasket->bind_param("i", $customer_id);
    $createBasket->execute();
    $basket_id = $createBasket->insert_id;
    $createBasket->close();
}
$basketQuery->close();


$checkProduct = $conn->prepare("SELECT id, quantity FROM basket_products WHERE basket_id = ? AND product_id = ?");
$checkProduct->bind_param("ii", $basket_id, $product_id);
$checkProduct->execute();
$result = $checkProduct->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $newQuantity = $row['quantity'] + 1;


    $update = $conn->prepare("UPDATE basket_products SET quantity = ? WHERE id = ?");
    $update->bind_param("ii", $newQuantity, $row['id']);
    $update->execute();
    $update->close();
} else {

    $insert = $conn->prepare("INSERT INTO basket_products (basket_id, product_id, quantity) VALUES (?, ?, 1)");
    $insert->bind_param("ii", $basket_id, $product_id);
    $insert->execute();
    $insert->close();
}
$checkProduct->close();

$conn->close();

$referer = $_SERVER['HTTP_REFERER'] ?? 'main_client_page.php';
    echo "<script>alert('Dish added to cart!'); window.location.href = '$referer';</script>";
?>
