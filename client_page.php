<?php
session_start();


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if (
    empty($_SESSION['customer_id']) ||
    empty($_SESSION['user_email']) ||
    empty($_SESSION['customer_logged_in']) ||
    $_SESSION['customer_logged_in'] !== true) {

    $referer = $_SERVER['HTTP_REFERER'] ?? 'main_client_page.php';
    echo "<script>alert('Please Log In!'); window.location.href = '$referer';</script>";
    exit;
}


$customer_id = (int) $_SESSION['customer_id'];

if ($customer_id === 0) {
    die("Error: Invalid customer ID in session.");
}


$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}

$now = date("Y-m-d H:i:s");

$conn->query("
    UPDATE orders
    SET order_status = 'wait_customer'
    WHERE order_status = 'getting_ready'
      AND type_delivery = 'self_pickup'
      AND order_datetime <= '$now'
");

$conn->query("
    UPDATE orders
    SET order_status = 'issue_courier'
    WHERE order_status = 'getting_ready'
      AND type_delivery = 'address_pickup'
      AND order_datetime <= '$now'
");


$ordersQuery = $conn->query("
    SELECT o.order_id, o.order_datetime, o.total_price, o.order_status, o.type_delivery, b.basket_id, b.created_at
    FROM orders o
    JOIN baskets b ON o.basket_id = b.basket_id
    WHERE o.customer_id = $customer_id
    ORDER BY o.order_id DESC
");


$orders = [];
if ($ordersQuery && $ordersQuery->num_rows > 0) {
    while ($order = $ordersQuery->fetch_assoc()) {
        $orders[] = $order;
    }
}



$sql = "SELECT first_name, last_name, phone_number, address, email FROM customers WHERE customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();


if ($result && $result->num_rows > 0) {
    $client = $result->fetch_assoc();
} else {
    $client = [
        'first_name' => '',
        'last_name' => '',
        'phone_number' => '',
        'address' => '',
        'email' => ''
    ];
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Account</title>
  <link rel="shortcut icon" href="images/pizza_icon.png" type="image/png">
  <link rel="stylesheet" href="pizza_tower.css">
</head>
<body>
  <header>
    <div class="container">
      <div class="header-container">
        <div class="header-div">
          <a href="main_client_page.php">
            <svg class="logo" viewBox="0 0 32 32">
              <path d="M14.655 2.7v-2.7c-3.337 0.275-6.464 1.57-9.018 3.735l9.018 9.018v-10.053h-0zM12.82 6.98c-0.25 0.25-0.596 0.394-0.951 0.394s-0.701-0.144-0.951-0.394c-0.25-0.251-0.394-0.597-0.394-0.951s0.144-0.7 0.394-0.951c0.251-0.25 0.597-0.394 0.951-0.394s0.7 0.144 0.951 0.394c0.251 0.251 0.394 0.597 0.394 0.951s-0.143 0.7-0.394 0.951z"></path>
              <path d="M28.265 5.638l-9.018 9.017h12.753c-0.275-3.337-1.57-6.464-3.735-9.017zM26.922 12.821c-0.25 0.25-0.597 0.394-0.951 0.394s-0.701-0.143-0.951-0.394c-0.25-0.25-0.394-0.597-0.394-0.951s0.143-0.701 0.394-0.951c0.25-0.25 0.597-0.394 0.951-0.394s0.7 0.144 0.951 0.394c0.251 0.251 0.395 0.597 0.395 0.951s-0.143 0.7-0.395 0.951z"></path>
              <path d="M2.7 17.345h-2.7c0.275 3.337 1.57 6.464 3.735 9.018l9.018-9.018h-10.053zM6.029 21.475c-0.354 0-0.701-0.144-0.951-0.394s-0.394-0.597-0.394-0.951c0-0.354 0.144-0.7 0.394-0.951s0.597-0.394 0.951-0.394 0.7 0.143 0.951 0.394c0.25 0.251 0.394 0.598 0.394 0.951s-0.144 0.7-0.394 0.951c-0.251 0.25-0.597 0.394-0.951 0.394z"></path>
              <path d="M5.645 7.547l-1.909-1.909c-2.165 2.554-3.46 5.681-3.735 9.018h12.753l-7.108-7.108zM6.029 13.215c-0.354 0-0.701-0.143-0.951-0.394s-0.394-0.597-0.394-0.951c0-0.354 0.144-0.7 0.394-0.951s0.597-0.394 0.951-0.394 0.7 0.144 0.951 0.394c0.25 0.251 0.394 0.597 0.394 0.951s-0.144 0.7-0.394 0.951c-0.251 0.25-0.597 0.394-0.951 0.394z"></path>
              <path d="M7.547 26.355l-1.909 1.909c2.554 2.165 5.681 3.46 9.018 3.735v-12.753l-7.108 7.108zM12.82 26.922c-0.25 0.251-0.596 0.395-0.951 0.395s-0.701-0.144-0.951-0.395c-0.25-0.25-0.394-0.597-0.394-0.951s0.144-0.701 0.394-0.951 0.597-0.394 0.951-0.394c0.354 0 0.7 0.143 0.951 0.394s0.394 0.597 0.394 0.951c0 0.353-0.143 0.7-0.394 0.951z"></path>
              <path d="M25.408 27.31l-8.062-8.062v12.753c3.337-0.275 6.464-1.57 9.017-3.735l-0.955-0.955zM21.081 26.922c-0.251 0.251-0.597 0.395-0.951 0.395s-0.701-0.144-0.951-0.395c-0.25-0.25-0.394-0.596-0.394-0.951s0.144-0.701 0.394-0.951c0.25-0.25 0.597-0.394 0.951-0.394s0.7 0.143 0.951 0.394c0.25 0.25 0.394 0.597 0.394 0.951s-0.143 0.7-0.394 0.951z"></path>
              <path d="M30.649 17.345h-11.402l9.018 9.017c2.165-2.554 3.46-5.681 3.735-9.017h-1.351zM26.922 21.081c-0.25 0.25-0.597 0.394-0.951 0.394s-0.701-0.144-0.951-0.394-0.394-0.597-0.394-0.951c0-0.354 0.143-0.7 0.394-0.951s0.597-0.394 0.951-0.394c0.354 0 0.7 0.143 0.951 0.394s0.395 0.597 0.395 0.951c-0 0.354-0.144 0.7-0.395 0.951z"></path>
              <path d="M17.345 0v12.753l9.017-9.017c-2.554-2.165-5.681-3.46-9.017-3.735zM21.082 6.98c-0.251 0.25-0.598 0.394-0.951 0.394s-0.701-0.144-0.951-0.394c-0.25-0.25-0.394-0.597-0.394-0.951s0.144-0.7 0.394-0.95c0.25-0.251 0.597-0.395 0.951-0.395s0.7 0.144 0.951 0.395c0.25 0.25 0.394 0.596 0.394 0.95s-0.143 0.7-0.394 0.951z"></path>
            </svg>
          </a>
          <ul class="header-info">
            <li>
              <div class="header-info-list">
                <p class="header-info-main-text">Working Hours</p>
                <p class="header-info-text">Mon-Sun</p>
                <p class="header-info-text">8:00 AM - 10:00 PM</p>
              </div>
            </li>
            <li>
              <div class="header-info-list">
                <p class="header-info-main-text">Our Contacts</p>
                <a href="tel:+380671234567" class="header-info-text">+380 (67) 123-45-67</a><br>
                <a href="tel:+380672345671" class="header-info-text">+380 (67) 234-56-71</a>
              </div>
            </li>
          </ul>
        </div>
        <?php if (isset($_SESSION['user_email'])): ?>
        <div class="user-info">
          <span class="user-inf-email"><?php echo htmlspecialchars($_SESSION['user_email']); ?></span>
          <a class="form-button button button-logout" href="logout_client.php">Logout</a>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </header>
  <main>
    <section class="hero-section-client">
      <div class="container">
        <h1 class="hero-title">My Account</h1>
      </div>
    </section>
    <section class="nav-section">
      <div class="container">
        <nav>
          <ul class="navigation">
            <li><a class="nav-link" href="main_client_page.php">Home</a></li>
            <li><a class="nav-link nav-link-after" href="menu_all_client.php">Menu</a>
              <ul class="menu-list">
                <li><a class="nav-link" href="menu_pizzas_client_page.php">Pizza</a></li>
                <li><a class="nav-link" href="menu_pizzarolls_client.php">Pizza Rolls</a></li>
                <li><a class="nav-link" href="menu_snaks_client.php">Snacks</a></li>
                <li><a class="nav-link" href="menu_desserts_client.php">Desserts</a></li>
                <li><a class="nav-link" href="menu_drinks_client.php">Drinks</a></li>
                <li><a class="nav-link" href="menu_yourpizza_client.php">Custom Pizza</a></li>
              </ul> 
            </li>
            <li><a class="nav-link nav-link-after" href="menu_offers_client.php">Offers</a>
              <ul class="menu-list">
                <li><a class="nav-link" href="menu_promotions_client.php">Promotions</a></li>
                <li><a class="nav-link" href="menu_sets_client.php">Sets</a></li>
              </ul>
            </li>
            <li><a class="nav-link" href="about_client_page.php">About Us</a></li>
            <li><a class="nav-link" href="client_basket.php">Cart</a></li>
            <li><a class="nav-link" href="client_page.php?from=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">My Account</a></li>
          </ul>
        </nav>
      </div>
    </section>
    <section class="client-info-section" id="client-info-section">
      <div class="container client-info-container">
        <div class="client-info-head">
          <h4>Information
            <span class="edit-pen" onclick="editInfoClient()">
              <svg class="edit-pen-client" viewBox="0 0 32 32">
                <path d="M26.215 2.954l2.828 2.828c0.781 0.781 0.781 2.047 0 2.828l-16.732 16.732c-0.455 0.455-1.014 0.794-1.628 0.987l-6.191 1.947c-0.316 0.099-0.653-0.076-0.752-0.392-0.038-0.121-0.037-0.252 0.004-0.372l2.209-6.538c0.197-0.583 0.526-1.113 0.961-1.548l16.473-16.473c0.781-0.781 2.047-0.781 2.828 0z"></path>
              </svg>
            </span>
          </h4>
        </div>
        <div class="client-info-all">
          <form name="info-about-client" method="post" action="update_client.php" autocomplete="off">
            <table class="client-info-table">
              <tr>
                <td>Name</td>
                <td>
                  <label class="form-label visually-hidden" for="client-name-info">Name</label>
                  <input 
                  class="form-input-info-client" 
                  type="text" 
                  name="client-name-info" 
                  id="client-name-info"
                  value="<?= htmlspecialchars($client['first_name']) ?>"
                  required
                  readonly>
                </td>
              </tr>
              <tr>
                <td>Last Name</td>
                <td>
                  <label class="form-label visually-hidden" for="client-last-name-info">Last Name</label>
                  <input 
                  class="form-input-info-client" 
                  type="text" 
                  name="client-last-name-info" 
                  id="client-last-name-info"
                  value="<?= htmlspecialchars($client['last_name']) ?>"
                  required
                  readonly>
                </td>
              </tr>
              <tr>
                <td>Phone Number</td>
                <td>
                  <label class="form-label visually-hidden" for="client-tel-info">Phone Number</label>
                  <input 
                  class="form-input-info-client" 
                  type="tel" 
                  name="client-tel-info" 
                  id="client-tel-info"
                  value="<?= htmlspecialchars($client['phone_number']) ?>"
                  pattern="^\+[0-9]{12}"
                  required 
                  readonly>
                </td>
              </tr>
              <tr>
                <td>Address</td>
                <td>
                  <label class="form-label visually-hidden" for="client-address-info">Address</label>
                  <input 
                  class="form-input-info-client" 
                  type="text" 
                  name="client-address-info" 
                  id="client-address-info"
                  value="<?= htmlspecialchars($client['address']) ?>"
                  required
                  readonly>
                </td>
              </tr>
              <tr>
                <td>Email</td>
                <td>
                  <label class="form-label visually-hidden" for="client-email-info">Email</label>
                  <input 
                  class="form-input-info-client" 
                  type="email" 
                  name="client-email-info" 
                  id="client-email-info"
                  value="<?= htmlspecialchars($client['email']) ?>"
                  required
                  readonly>
                </td>
              </tr>
            </table>
            <div class="client-info-buttons">
              <button class="form-button button client-change-button" type="submit" disabled>Save Changes</button>
              <button class="form-button button client-change-password-button" type="button">Change Password</button>
            </div>
          </form>
        </div>
      </div>
    </section>
    <section class="client-history-section" id="client-history-section">
      <h4>History</h4>
    </div>
  </section>
  <section class="menu-pizzas-section" id="menu-pizzas-section">
    <?php foreach ($orders as $order): 
      $order_id = $order['order_id'];
      $issued_time = null;

      $issuedRes = $conn->prepare("SELECT issued_at FROM issued_orders WHERE order_id = ?");
      $issuedRes->bind_param("i", $order_id);
      $issuedRes->execute();
      $issuedResult = $issuedRes->get_result();

      if ($issuedResult && $issuedResult->num_rows > 0) {
        $issued_time = date('Y-m-d\TH:i', strtotime($issuedResult->fetch_assoc()['issued_at']));
      }

      $issuedRes->close();

      ?>
      <?php
      $basket_id = (int)$order['basket_id'];
      $created_at = date('Y-m-d\TH:i', strtotime($order['created_at']));
      $total = $order['total_price'];
      $status = $order['order_status'];

      $statusTranslations = [
        'getting_ready'   => 'Preparing',
        'wait_customer'   => 'Awaiting pickup',
        'issue_courier'   => 'Out for delivery',
        'issued'          => 'Delivered',
        'cancelled'       => 'Cancelled'
      ];

      ?>
      <div class="container client-history-container">
        <div class="client-history-head">
          <div class="client-history-order-date">
            <?php
            $created_at = date('Y-m-d\TH:i', strtotime($order['created_at']));
            $ready_time = date('Y-m-d\TH:i', strtotime($order['order_datetime']));
            ?>
            <?php

            $delivery_time_value = '';
            if ($order['type_delivery'] === 'address_pickup') {
              $stmt = $conn->prepare("SELECT delivery_datetime FROM delivery WHERE order_id = ?");
              $stmt->bind_param("i", $order['order_id']);
              $stmt->execute();
              $res = $stmt->get_result();
              if ($res && $row = $res->fetch_assoc()) {
                $delivery_time_value = date('Y-m-d\TH:i', strtotime($row['delivery_datetime']));
              }
              $stmt->close();
            }


            ?>
            <div class="client-history-order-datess">
              <h5 class="order-number">Order № <?= $order['order_id'] ?></h5>
              Ordered at 
              <input class="client-order-datetime" type="datetime-local" value="<?= $created_at ?>" readonly>
              Ready by 
              <input class="client-order-datetime" type="datetime-local" value="<?= $ready_time ?>" readonly>
              <?php if (!empty($delivery_time_value)): ?>
                Estimated delivery
                <input class="client-order-datetime" type="datetime-local" value="<?= $delivery_time_value ?>" readonly>
              <?php endif; ?>
              <?php if ($issued_time): ?>
                Completed at 
                <input class="client-order-datetime" type="datetime-local" value="<?= $issued_time ?>" readonly>
              <?php endif; ?>
            </div>


            <p>Status: <?= $statusTranslations[$status] ?? 'Unknown' ?></p>
            <?php if ($status === 'getting_ready'): ?>
              <form method="post" action="cancel_client_order.php">
                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                <input class="form-button button client-back-order-button" type="submit" value="Cancel Order">
              </form>
            <?php endif; ?>
          </div>
        </div>
        <div class="pizza-option-prices">
          <h4>Total: $<?= number_format($total, 2, ',', ' ') ?></h4>
        </div>


        <?php
        $order_id = $order['order_id'];

        $itemStmt = $conn->prepare("
          SELECT item_type, name, description, image_path, quantity, unit_price
          FROM order_items
          WHERE order_id = ?
          ");
        $itemStmt->bind_param("i", $order_id);
        $itemStmt->execute();
        $itemResult = $itemStmt->get_result();

        while ($item = $itemResult->fetch_assoc()):

          $img = file_exists($item['image_path']) ? $item['image_path'] : 'uploads/not-pic.jpg';
          ?>
          <div class="container menu-pizzas-container">
            <div class="pizza-img-text">
              <img class="img-dish" src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
              <div class="pizza-info">
                <div class="pizza-text">
                  <h3 class="about-dish"><?= htmlspecialchars($item['name']) ?></h3>
                  <?php
                  $descriptionClean = preg_replace('/\|\|customization_id=\d+/', '', $item['description']);
                  ?>
                  <p class="about-dish"><?= htmlspecialchars($descriptionClean) ?></p>

                  <?php if ($item['item_type'] === 'custom' && preg_match('/customization_id=(\d+)/', $item['description'], $matches)): ?>
                    <?php
                    $customId = (int)$matches[1];
                    $ingredientsRes = $conn->prepare("
                      SELECT i.name 
                      FROM pizza_ingredients pi
                      JOIN ingredients i ON pi.ingredient_id = i.ingredient_id
                      WHERE pi.customization_id = ?
                      ");
                    $ingredientsRes->bind_param("i", $customId);
                    $ingredientsRes->execute();
                    $ingredientsResult = $ingredientsRes->get_result();

                    $ingredientNames = [];
                    while ($ing = $ingredientsResult->fetch_assoc()) {
                      $ingredientNames[] = htmlspecialchars($ing['name']);
                    }
                    $ingredientsRes->close();
                    ?>

                    <?php if ($ingredientNames): ?>
                      <p class="about-dish">Ingredients: <?= implode(', ', $ingredientNames) ?></p>
                      <?php else: ?>
                        <p class="about-dish">No extra ingredients</p>
                      <?php endif; ?>
                    <?php endif; ?>

                    <p class="about-dish">Unit Price: $<?= number_format($item['unit_price'], 2, ',', ' ') ?></p>
                    <p class="about-dish">Quantity: <?= (int)$item['quantity'] ?></p>
                    <p class="about-dish">Subtotal: $<?= number_format($item['unit_price'] * $item['quantity'], 2, ',', ' ') ?></p>
                  </div>
                </div>
              </div>
            </div>
          <?php endwhile; ?>

        </div>
      <?php endforeach; ?>
    </section>
  </main>
  <footer>
    <div class="container">
      <div class="footer-container">
        <div class="footer-div">
          <a href="main_client_page.php">
            <svg class="logo" viewBox="0 0 32 32">
              <path d="M14.655 2.7v-2.7c-3.337 0.275-6.464 1.57-9.018 3.735l9.018 9.018v-10.053h-0zM12.82 6.98c-0.25 0.25-0.596 0.394-0.951 0.394s-0.701-0.144-0.951-0.394c-0.25-0.251-0.394-0.597-0.394-0.951s0.144-0.7 0.394-0.951c0.251-0.25 0.597-0.394 0.951-0.394s0.7 0.144 0.951 0.394c0.251 0.251 0.394 0.597 0.394 0.951s-0.143 0.7-0.394 0.951z"></path>
              <path d="M28.265 5.638l-9.018 9.017h12.753c-0.275-3.337-1.57-6.464-3.735-9.017zM26.922 12.821c-0.25 0.25-0.597 0.394-0.951 0.394s-0.701-0.143-0.951-0.394c-0.25-0.25-0.394-0.597-0.394-0.951s0.143-0.701 0.394-0.951c0.25-0.25 0.597-0.394 0.951-0.394s0.7 0.144 0.951 0.394c0.251 0.251 0.395 0.597 0.395 0.951s-0.143 0.7-0.395 0.951z"></path>
              <path d="M2.7 17.345h-2.7c0.275 3.337 1.57 6.464 3.735 9.018l9.018-9.018h-10.053zM6.029 21.475c-0.354 0-0.701-0.144-0.951-0.394s-0.394-0.597-0.394-0.951c0-0.354 0.144-0.7 0.394-0.951s0.597-0.394 0.951-0.394 0.7 0.143 0.951 0.394c0.25 0.251 0.394 0.598 0.394 0.951s-0.144 0.7-0.394 0.951c-0.251 0.25-0.597 0.394-0.951 0.394z"></path>
              <path d="M5.645 7.547l-1.909-1.909c-2.165 2.554-3.46 5.681-3.735 9.018h12.753l-7.108-7.108zM6.029 13.215c-0.354 0-0.701-0.143-0.951-0.394s-0.394-0.597-0.394-0.951c0-0.354 0.144-0.7 0.394-0.951s0.597-0.394 0.951-0.394 0.7 0.144 0.951 0.394c0.25 0.251 0.394 0.597 0.394 0.951s-0.144 0.7-0.394 0.951c-0.251 0.25-0.597 0.394-0.951 0.394z"></path>
              <path d="M7.547 26.355l-1.909 1.909c2.554 2.165 5.681 3.46 9.018 3.735v-12.753l-7.108 7.108zM12.82 26.922c-0.25 0.251-0.596 0.395-0.951 0.395s-0.701-0.144-0.951-0.395c-0.25-0.25-0.394-0.597-0.394-0.951s0.144-0.701 0.394-0.951 0.597-0.394 0.951-0.394c0.354 0 0.7 0.143 0.951 0.394s0.394 0.597 0.394 0.951c0 0.353-0.143 0.7-0.394 0.951z"></path>
              <path d="M25.408 27.31l-8.062-8.062v12.753c3.337-0.275 6.464-1.57 9.017-3.735l-0.955-0.955zM21.081 26.922c-0.251 0.251-0.597 0.395-0.951 0.395s-0.701-0.144-0.951-0.395c-0.25-0.25-0.394-0.596-0.394-0.951s0.144-0.701 0.394-0.951c0.25-0.25 0.597-0.394 0.951-0.394s0.7 0.143 0.951 0.394c0.25 0.25 0.394 0.597 0.394 0.951s-0.143 0.7-0.394 0.951z"></path>
              <path d="M30.649 17.345h-11.402l9.018 9.017c2.165-2.554 3.46-5.681 3.735-9.017h-1.351zM26.922 21.081c-0.25 0.25-0.597 0.394-0.951 0.394s-0.701-0.144-0.951-0.394-0.394-0.597-0.394-0.951c0-0.354 0.143-0.7 0.394-0.951s0.597-0.394 0.951-0.394c0.354 0 0.7 0.143 0.951 0.394s0.395 0.597 0.395 0.951c-0 0.354-0.144 0.7-0.395 0.951z"></path>
              <path d="M17.345 0v12.753l9.017-9.017c-2.554-2.165-5.681-3.46-9.017-3.735zM21.082 6.98c-0.251 0.25-0.598 0.394-0.951 0.394s-0.701-0.144-0.951-0.394c-0.25-0.25-0.394-0.597-0.394-0.951s0.144-0.7 0.394-0.95c0.25-0.251 0.597-0.395 0.951-0.395s0.7 0.144 0.951 0.395c0.25 0.25 0.394 0.596 0.394 0.95s-0.143 0.7-0.394 0.951z"></path>
            </svg>
          </a>
          <ul class="footer-info">
            <li>
              <div class="footer-info-list">
                <p class="footer-info-main-text">Working Hours</p>
                <p class="footer-info-text">Mon-Sun</p>
                <p class="footer-info-text">8:00 AM - 10:00 PM</p>
              </div>
            </li>
            <li>
              <div class="footer-info-list">
                <p class="footer-info-main-text">Our Contacts</p>
                <a href="tel:+380671234567" class="footer-info-text">+380 (67) 123-45-67</a><br>
                <a href="tel:+380672345671" class="footer-info-text">+380 (67) 234-56-71</a>
              </div>
            </li>
            <li>
              <div class="footer-info-list">
                <p class="footer-info-main-text">Address</p>
                <a class="footer-info-text" href="https://maps.app.goo.gl/qN2TQ2LbQ1oPTmaCA" target="_blank">5 Oleksandrivskyi Ave, Odesa, Ukraine</a>
              </div>
            </li>
            <li>
              <div class="footer-info-list">
                <a class="footer-info-text footer-info-text-right" href="#" target="_blank">Privacy Policy</a>
                <p class="footer-info-text footer-info-text-right">&copy; Diana, 2025</p>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </footer>
  <div class="backdrop is-hidden">
    <div class="modal">
      <button class="form-button modal-btn-close button" type="button">Close</button>
      <h2>Log In</h2>
      <form name="modal-form" method="post" action="login_client.php" autocomplete="off">
        <div class="form-field">
          <label class="form-label visually-hidden" for="client-email">Email</label>
          <input 
          class="form-input"
          type="email"
          name="client-email"
          id="client-email"
          placeholder="Email"
          required>
        </div>
        <div class="form-field">
          <label class="form-label visually-hidden" for="client-password">Password</label>
          <input 
          class="form-input"
          type="password"
          name="client-password"
          id="client-password"
          placeholder="Password"
          required>
        </div>
        <button class="form-button button" type="submit">Login</button>
        <p class="form-info-text">Sign up (below) if you don't have an account, or call us at <a href="tel:+380673456712" class="form-info-text">+380 (67) 345-67-12</a> if you forgot your password.</p>
      </form>
      <h2>Registration</h2>
      <form name="modal-form-client-register" method="post" action="register_client.php" autocomplete="off">
        <div class="form-field">
          <label class="form-label visually-hidden" for="client-name">Name</label>
          <input 
          class="form-input" 
          type="text" 
          name="client-name" 
          id="client-name"
          placeholder="Name"
          required>
        </div>
        <div class="form-field">
          <label class="form-label visually-hidden" for="client-last-name">Last Name</label>
          <input 
          class="form-input" 
          type="text" 
          name="client-last-name" 
          id="client-last-name"
          placeholder="Last Name"
          required>
        </div>
        <div class="form-field">
          <label class="form-label visually-hidden" for="client-tel">Phone Number</label>
          <input 
          class="form-input" 
          type="tel" 
          name="client-tel" 
          id="client-tel"
          placeholder="Phone Number"
          pattern="^\+[0-9]{12}" 
          required>
        </div>
        <div class="form-field">
          <label class="form-label visually-hidden" for="client-address">Address</label>
          <input 
          class="form-input" 
          type="text" 
          name="client-address" 
          id="client-address"
          placeholder="Address"
          required>
        </div>
        <div class="form-field">
          <label class="form-label visually-hidden" for="client-new-email">Email</label>
          <input 
          class="form-input" 
          type="email" 
          name="client-new-email" 
          id="client-new-email"
          placeholder="Email"
          required>
        </div>
        <div class="form-field">
          <label class="form-label visually-hidden" for="client-new-password">Password</label>
          <input 
          class="form-input"
          type="password"
          name="client-new-password"
          id="client-new-password"
          placeholder="Password"
          required>
        </div>
        <div class="form-field">
          <label class="form-label visually-hidden" for="client-accept-password">Confirm Password</label>
          <input 
          class="form-input"
          type="password"
          name="client-accept-password"
          id="client-accept-password"
          placeholder="Confirm Password"
          required>
        </div>
        <button class="form-button button" type="submit">Sign Up</button>
        <p class="form-info-text">After registration, please log in (above).</p>
      </form>
    </div>
  </div>
  <div class="backdrop-change-client-pass is-hidden">
    <div class="modal modal-pass-change-client">
      <button class="form-button modal-btn-close-pass-client button" type="button">Close</button>
      <h2>Change Password</h2>
      <form name="modal-form-changge-client-pass" method="post" action="new_password_change_client.php" autocomplete="off">
        <input type="hidden" name="client-id" id="modal-client-id">
        <div class="form-field">
          <label class="form-label visually-hidden" for="client-now-pass-changge">Current Password</label>
          <input 
          class="form-input"
          type="password"
          name="client-now-pass-changge"
          id="client-now-pass-changge"
          placeholder="Current Password"
          required>
        </div>
        <div class="form-field">
          <label class="form-label visually-hidden" for="client-new-password-changge">New Password</label>
          <input 
          class="form-input"
          type="password"
          name="client-new-password-changge"
          id="client-new-password-changge"
          placeholder="New Password"
          required>
        </div>
        <div class="form-field">
          <label class="form-label visually-hidden" for="client-accept-password-changge">Confirm Password</label>
          <input 
          class="form-input"
          type="password"
          name="client-accept-password-changge"
          id="client-accept-password-changge"
          placeholder="Confirm Password"
          required>
        </div>
        <button class="form-button button" type="submit">Update Password</button>
      </form>
    </div>
  </div>
  <script src="modal.js"></script>
  <script src="modal-client-change-password.js"></script>
  <script src="yourpizza-price-check.js"></script>
  <script src="client-page.js"></script>
</body>
</html>