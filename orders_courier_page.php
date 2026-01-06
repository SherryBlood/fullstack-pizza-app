<?php
session_start();
date_default_timezone_set('Europe/Kyiv');

if (!isset($_SESSION['courier_logged_in']) || $_SESSION['courier_logged_in'] !== true) {
    header('Location: main_work_page.html');
    exit;
}

$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");
if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}

$courier_id = (int)$_SESSION['courier_id'];

$stmt = $conn->prepare("
    SELECT o.*, b.created_at, c.first_name, c.last_name, c.phone_number, c.address, d.delivery_datetime
    FROM orders o
    JOIN baskets b ON o.basket_id = b.basket_id
    JOIN customers c ON o.customer_id = c.customer_id
    JOIN delivery d ON o.order_id = d.order_id
    WHERE d.courier_id = ? AND o.order_status NOT IN ('issued', 'cancelled')
    ORDER BY o.order_datetime DESC
");
$stmt->bind_param("i", $courier_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Orders</title>
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
        <div class="login-admin">
          <?php if (isset($_SESSION['courier_logged_in']) && $_SESSION['courier_logged_in']): ?>
          <span class="login-admin-text">Logged in</span>
        <?php endif; ?>
        <button class="form-button button modal-btn-open" type="button">Switch to Client View</button>
        </div>
      </div>
    </div>
  </header>
  <main>
    <section class="hero-section-orders-admin">
      <div class="container">
        <h1 class="hero-title hero-title-order">Orders</h1>
      </div>
    </section>
    <section class="nav-section">
      <div class="container">
        <nav>
          <ul class="navigation">
            <li><a class="nav-link" href="orders_courier_page.php">Orders</a></li>
            <li><a class="nav-link" href="logout_courier.php">Logout</a></li>
          </ul>
        </nav>
      </div>
    </section>
    <section class="order-admin-section">
      <?php foreach ($orders as $order): ?>
  <?php
    $created_at = date('Y-m-d\TH:i', strtotime($order['created_at']));
    $ready_time = date('Y-m-d\TH:i', strtotime($order['order_datetime']));
    $full_name = htmlspecialchars($order['first_name'] . ' ' . $order['last_name']);
    $phone = htmlspecialchars($order['phone_number']);
    $address = htmlspecialchars($order['address']);
    $total = $order['total_price'];
    $status = $order['order_status'];
    $delivery_type = $order['type_delivery'] === 'self_pickup' ? 'Pickup' : 'Delivery to address';
    $statusTranslations = [
      'getting_ready' => 'Preparing',
      'wait_customer' => 'Awaiting pickup',
      'issue_courier' => 'Out for delivery',
      'issued' => 'Delivered',
      'cancelled' => 'Cancelled'
    ];
  ?>
  <div class="container order-admin-container">
    <h5 class="order-number">Order № <?= $order['order_id'] ?></h5>
    <div class="order-info-admin">
      <div class="order-info-get-red-admin">
        Ordered at
        <input class="client-order-datetime" type="datetime-local" value="<?= $created_at ?>" readonly>
        Ready by
        <input class="client-order-datetime" type="datetime-local" value="<?= $ready_time ?>" readonly>
        Estimated delivery
        <input class="client-order-datetime" type="datetime-local" value="<?= date('Y-m-d\TH:i', strtotime($order['delivery_datetime'])) ?>" readonly>
      </div>

      <p class="order-status-admin">Status: <?= $statusTranslations[$status] ?? 'Unknown' ?></p>

      <?php if ($status === 'wait_customer' || $status === 'issue_courier'): ?>
        <form method="post" action="issue_courier_order.php">
          <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
          <input class="form-button button client-back-order-button" type="submit" value="Complete Order">
        </form>
      <?php endif; ?>
    </div>

    <div class="order-info-client-admin">
      <p class="order-info-p">Delivery Method: <span class="order-taking"><?= $delivery_type ?></span></p>
      <?php if ($order['type_delivery'] === 'address_pickup'): ?>
  <?php

    $assignedCourierId = null;
    $courierRes = $conn->prepare("SELECT courier_id FROM delivery WHERE order_id = ?");
    $courierRes->bind_param("i", $order['order_id']);
    $courierRes->execute();
    $courierResult = $courierRes->get_result();
    if ($courierResult->num_rows > 0) {
        $assignedCourierId = (int)$courierResult->fetch_assoc()['courier_id'];
    }
    $courierRes->close();
  ?>
<?php endif; ?>


      <p class="order-info-p">Total: <span class="order-suma">$<?= number_format($total, 2, ',', ' ') ?></span></p>
      <p class="order-info-p">Client: <span class="order-client-nsn"><?= $full_name ?></span></p>
      <p class="order-info-p">Phone: <span class="order-teleph"><?= $phone ?></span></p>
      <p class="order-info-p">Address: <span class="order-adress"><?= $address ?></span></p>
    </div>

    <?php
    $order_id = $order['order_id'];
    $items = $conn->query("SELECT * FROM order_items WHERE order_id = $order_id");

    while ($item = $items->fetch_assoc()):
      $img = file_exists($item['image_path']) ? $item['image_path'] : 'uploads/not-pic.jpg';
      $descriptionClean = preg_replace('/\|\|customization_id=\d+/', '', $item['description']);
    ?>
      <div class="container menu-pizzas-container">
        <div class="pizza-img-text">
          <img class="img-dish" src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
          <div class="pizza-info">
            <div class="pizza-text">
              <h3 class="about-dish"><?= htmlspecialchars($item['name']) ?></h3>
              <p class="about-dish"><?= htmlspecialchars($descriptionClean) ?></p>

              <?php if ($item['item_type'] === 'custom' && preg_match('/customization_id=(\d+)/', $item['description'], $matches)): ?>
                <?php
                  $customId = (int)$matches[1];
                  $ingRes = $conn->prepare("SELECT i.name FROM pizza_ingredients pi JOIN ingredients i ON pi.ingredient_id = i.ingredient_id WHERE pi.customization_id = ?");
                  $ingRes->bind_param("i", $customId);
                  $ingRes->execute();
                  $ingResult = $ingRes->get_result();
                  $ings = [];
                  while ($i = $ingResult->fetch_assoc()) {
                      $ings[] = htmlspecialchars($i['name']);
                  }
                  $ingRes->close();
                ?>
                <p class="about-dish">Ingredients: <?= $ings ? implode(', ', $ings) : 'No extra ingredients' ?></p>
              <?php endif; ?>

              <p>Unit Price: $<?= number_format($item['unit_price'], 2, ',', ' ') ?></p>
              <p>Quantity: <?= (int)$item['quantity'] ?></p>
              <p>Subtotal: $<?= number_format($item['unit_price'] * $item['quantity'], 2, ',', ' ') ?></p>
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
  <script src="modal.js"></script>
  <script src="updt-statuses.js"></script>
</body>
</html>