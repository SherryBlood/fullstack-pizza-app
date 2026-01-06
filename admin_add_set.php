<?php
$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}

$name = $_POST['set-name'];
$description = $_POST['set-description'];
$price = $_POST['set-price'];


$imageName = basename($_FILES["set-image"]["name"]);
$targetDir = "images/";
$targetFile = $targetDir . $imageName;

if (move_uploaded_file($_FILES["set-image"]["tmp_name"], $targetFile)) {

    $stmt = $conn->prepare("INSERT INTO sets (name, description, price, image_path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $name, $description, $price, $targetFile);

    if ($stmt->execute()) {
    $referrer = $_SERVER['HTTP_REFERER'] ?? 'menu_admin_page.php';
    echo '<script>alert("Set added successfully!"); window.location.href = "' . $referrer . '";</script>';
    } else {
        echo '<script>alert("Error adding set: ' . $stmt->error . '"); window.history.back();</script>';
    }

    $stmt->close();
} else {
    echo '<script>alert("Error uploading image.."); window.history.back();</script>';
}

$conn->close();
?>
