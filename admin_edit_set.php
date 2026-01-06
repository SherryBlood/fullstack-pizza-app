<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli("localhost", "root", "1234", "pizza_db");
    $conn->set_charset("utf8");

    if ($conn->connect_error) {
        die("Connection Error: " . $conn->connect_error);
    }

    $product_id = (int)$_POST['set-id'];
    $name = trim($_POST['set-name-edit']);
    $description = trim($_POST['set-description-edit']);
    $price = (float)$_POST['set-price-edit'];

    if (isset($_FILES['set-image-edit']) && $_FILES['set-image-edit']['error'] === 0) {
        $image = $_FILES['set-image-edit'];
        $imagePath = 'uploads/' . basename($image['name']);


        move_uploaded_file($image['tmp_name'], $imagePath);

        $sql = "UPDATE sets SET name=?, description=?, price=?, image_path=? WHERE set_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsi", $name, $description, $price, $imagePath, $product_id);
    } else {
        $sql = "UPDATE sets SET name=?, description=?, price=? WHERE set_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdi", $name, $description, $price, $product_id);
    }

    if ($stmt->execute()) {
        $referrer = $_SERVER['HTTP_REFERER'] ?? 'menu_admin_page.php';
        echo '<script>alert("Set updated successfully!"); window.location.href = "' . $referrer . '";</script>';
    } else {
        echo '<script>alert("Error updating set: ' . $stmt->error . '"); window.history.back();</script>';
    }

    $stmt->close();
    $conn->close();
} else {
    echo '<script>alert("Invalid request."); window.history.back();</script>';
}
?>

