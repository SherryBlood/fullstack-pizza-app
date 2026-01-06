<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli("localhost", "root", "1234", "pizza_db");
    $conn->set_charset("utf8");

    if ($conn->connect_error) {
        die("Connection Error: " . $conn->connect_error);
    }

    $price = isset($_POST['base-price-edit']) ? (float)$_POST['base-price-edit'] : null;

    if ($price !== null && $price >= 0) {

        $checkSql = "SELECT COUNT(*) as count FROM pizza_base WHERE base_id = 1";
        $checkResult = $conn->query($checkSql);
        $row = $checkResult->fetch_assoc();

        if ($row['count'] == 0) {

            $insertSql = "INSERT INTO pizza_base (base_id, price) VALUES (1, 0.00)";
            $conn->query($insertSql);
        }


        $sql = "UPDATE pizza_base SET price = ? WHERE base_id = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("d", $price);

        if ($stmt->execute()) {
            $referrer = $_SERVER['HTTP_REFERER'] ?? 'menu_admin_page.php';
            echo '<script>alert("Pizza base price updated successfully!"); window.location.href = "' . $referrer . '";</script>';
        } else {
            echo '<script>alert("Update error: ' . $stmt->error . '"); window.history.back();</script>';
        }

        $stmt->close();
    } else {
        echo '<script>alert("Invalid price."); window.history.back();</script>';
    }

    $conn->close();
} else {
    echo '<script>alert("Invalid request."); window.history.back();</script>';
}
?>
