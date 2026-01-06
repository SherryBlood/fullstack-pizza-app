<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli("localhost", "root", "1234", "pizza_db");
    $conn->set_charset("utf8");

    if ($conn->connect_error) {
        die("Connection Error: " . $conn->connect_error);
    }

    $product_id = (int)$_POST['product-id'];
    $category = $_POST['product-category-edit'];
    $name = trim($_POST['product-name-edit']);
    $description = trim($_POST['product-description-edit']);
    $price = (float)$_POST['product-price-edit'];


    if (isset($_FILES['product-image-edit']) && $_FILES['product-image-edit']['error'] === 0) {
        $image = $_FILES['product-image-edit'];
        $imagePath = 'images/' . basename($image['name']);

        move_uploaded_file($image['tmp_name'], $imagePath);

        $sql = "UPDATE products SET category=?, name=?, description=?, price=?, image_path=? WHERE product_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $category, $name, $description, $price, $imagePath, $product_id);
    } else {
        $sql = "UPDATE products SET category=?, name=?, description=?, price=? WHERE product_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdi", $category, $name, $description, $price, $product_id);
    }

    if ($stmt->execute()) {
    $referrer = $_SERVER['HTTP_REFERER'] ?? 'menu_admin_page.php';
    echo '<script>alert("Dish updated successfully!"); window.location.href = "' . $referrer . '";</script>';
    } else {
        echo '<script>alert("Error updating dish: ' . $stmt->error . '"); window.history.back();</script>';
    }

    $stmt->close();
    $conn->close();
} else {
    echo '<script>alert("Invalid request."); window.history.back();</script>';
}
?>
