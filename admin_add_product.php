<?php

$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}


$category = $_POST['product-category'];
$name = $_POST['product-name'];
$description = $_POST['product-description'];
$price = $_POST['product-price'];


$image = $_FILES['product-image'];
$target_dir = "uploads/products/";
$ext = pathinfo($image['name'], PATHINFO_EXTENSION);
$filename = uniqid("img_") . "." . $ext;
$target_path = $target_dir . $filename;


if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

if (!move_uploaded_file($image["tmp_name"], $target_path)) {
    echo '<script>alert("Error uploading image."); window.history.back();</script>';
    exit;
}


$stmt = $conn->prepare("INSERT INTO products (name, description, price, category, image_path) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssdss", $name, $description, $price, $category, $target_path);

if ($stmt->execute()) {
    $referrer = $_SERVER['HTTP_REFERER'] ?? 'menu_admin_page.php';
    echo '<script>alert("Dish added successfully!"); window.location.href = "' . $referrer . '";</script>';
    } else {
    echo '<script>alert("Error adding the item: ' . $stmt->error . '"); window.history.back();</script>';
}

$stmt->close();
$conn->close();
?>
