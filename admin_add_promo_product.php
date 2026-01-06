<?php
session_start();
$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $discount = (float) $_POST['product-price-add-promo'];
    $start_date = $_POST['product-start-date-add-promo'];
    $end_date = $_POST['product-end-date-add-promo'];
    $product_id = (int) $_POST['product-id-for-promo'];


    $today = date("Y-m-d");
    $conn->query("DELETE FROM promotions WHERE end_date < '$today'");


    $old_promo_result = $conn->query("SELECT promotion_id FROM products WHERE product_id = $product_id AND promotion_id IS NOT NULL");
    if ($old_promo_result && $old_promo_result->num_rows > 0) {
        $row = $old_promo_result->fetch_assoc();
        $old_promo_id = (int) $row['promotion_id'];


        $conn->query("UPDATE products SET promotion_id = NULL WHERE product_id = $product_id");


        $conn->query("DELETE FROM promotions WHERE promotion_id = $old_promo_id");
    }


    $stmt = $conn->prepare("INSERT INTO promotions (discount_percentage, start_date, end_date) VALUES (?, ?, ?)");
    $stmt->bind_param("dss", $discount, $start_date, $end_date);
    if ($stmt->execute()) {
        $promotion_id = $stmt->insert_id;


        $update_stmt = $conn->prepare("UPDATE products SET promotion_id = ? WHERE product_id = ?");
        $update_stmt->bind_param("ii", $promotion_id, $product_id);
        if ($update_stmt->execute()) {


            $conn->query("
                DELETE FROM promotions 
                WHERE promotion_id NOT IN (
                    SELECT DISTINCT promotion_id FROM products WHERE promotion_id IS NOT NULL
                )
            ");

            $referrer = $_SERVER['HTTP_REFERER'] ?? 'menu_admin_page.php'; 
            echo '<script>alert("Promotion added successfully!"); window.location.href = "' . $referrer . '";</script>';
        } else {
            echo '<script>alert("Error updating product: ' . $update_stmt->error . '"); window.history.back();</script>';
        }
        $update_stmt->close();
    } else {
        echo '<script>alert("Error adding promotion: ' . $stmt->error . '"); window.history.back();</script>';
    }
    $stmt->close();
}

$conn->close();
?>
