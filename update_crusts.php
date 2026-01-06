<?php
session_start();
$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['crust_ids'], $_POST['names'], $_POST['descriptions'], $_POST['prices'])) {
        $crust_ids = $_POST['crust_ids'];
        $names = $_POST['names'];
        $descriptions = $_POST['descriptions'];
        $prices = $_POST['prices'];


        for ($i = 0; $i < count($crust_ids); $i++) {
            $crust_id = (int)$crust_ids[$i];
            $name = trim($names[$i]);
            $description = trim($descriptions[$i]);
            $price = (float)$prices[$i];

            if (!empty($name) && !empty($description) && $price >= 0) {

                $stmt = $conn->prepare("UPDATE crusts SET name = ?, description = ?, additional_price = ? WHERE crust_id = ?");
                $stmt->bind_param("ssdi", $name, $description, $price, $crust_id);
                $stmt->execute();
                $stmt->close();
            }
        }

        echo '<script>alert("Crusts updated successfully!"); window.location.href = "' . ($_SERVER['HTTP_REFERER'] ?? 'menu_crusts_admin_page.php') . '";</script>';
    } else {
        echo '<script>alert("No data provided."); window.history.back();</script>';
    }
} else {
    echo '<script>alert("Invalid request."); window.history.back();</script>';
}

$conn->close();
?>
