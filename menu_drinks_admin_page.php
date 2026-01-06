<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: main_work_page.html');
    exit;
}

$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}


$productsQuery = "SELECT * FROM products WHERE category = 'drink'";
$productsResult = $conn->query($productsQuery);

$basePizzaPrice = "SELECT price FROM pizza_base WHERE base_id = 1";
$result = $conn->query($basePizzaPrice);
$basePrice = "0.00";

if ($result && $row = $result->fetch_assoc()) {
    $basePrice = htmlspecialchars($row['price']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Drinks</title>
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
          <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']): ?>
          <span class="login-admin-text">Logged in</span>
        <?php endif; ?>
        <button class="form-button button modal-btn-open" type="button">Switch to Client View</button>
        </div>
      </div>
    </div>
  </header>
  <main>
    <section class="hero-section-menu-drinks-admin">
      <div class="container">
        <h1 class="hero-title hero-title-order">Drinks</h1>
      </div>
    </section>
    <section class="nav-section">
      <div class="container">
        <nav>
          <ul class="navigation">
            <li><a class="nav-link" href="orders_admin_page.php">Orders</a></li>
            <li><a class="nav-link" href="menu_admin_page.php">Menu</a></li>
            <li><a class="nav-link" href="clients_admin_page.php">Clients</a></li>
            <li><a class="nav-link" href="couriers_admin_page.php">Couriers</a></li>
            <li><a class="nav-link" href="charts.php">Analytics</a></li>
            <li><a class="nav-link" href="logout_admin.php">Logout</a></li>
          </ul>
        </nav>
      </div>
    </section>
    <section class="menu-search-section" id="menu-search-section">
      <div class="container search-container">
        <form name="search-form-admin" method="get" action="menu_search_admin_page.php" autocomplete="off">
          <div class="form-field search-form-admin">
            <label class="form-label visually-hidden" for="search-admin">Search</label>
            <input 
            class="form-input search-input"
            type="text"
            name="search-admin"
            id="search-admin"
            placeholder="Search"
            required>
            <button class="form-button button search-button" type="submit">Search</button>
            <input class="form-button button search-button" type="button" value="Expensive" onclick="highPriceAdmin()">
            <input class="form-button button search-button" type="button" value="Cheapest" onclick="lowPriceAdmin()">
            <button class="form-button button modal-add-products-sets-btn-open" type="button">Add</button>
          </div>
        </form>
      </div>
    </section>
    <section class="menu-search-section-elements" id="menu-search-section-elements">
      <div class="container search-container-elements">
        <div class="search-elements-butt">
          <input 
          class="form-button button search-button search-button-elements" 
          type="button" 
          value="Dishes" 
          onclick="window.location.href='menu_admin_page.php';">
          <input 
          class="form-button button search-button search-button-elements" 
          type="button" 
          value="Sets" 
          onclick="window.location.href='menu_sets_admin_page.php';">
        </div>
        <div class="search-elements-butt">
          <input 
          class="form-button button search-button search-button-elements" 
          type="button" 
          value="Pizza" 
          onclick="window.location.href='menu_pizzas_admin_page.php';">
          <input 
          class="form-button button search-button search-button-elements" 
          type="button" 
          value="Pizza Rolls" 
          onclick="window.location.href='menu_pizzarolls_admin_page.php';">
          <input 
          class="form-button button search-button search-button-elements" 
          type="button" 
          value="Snacks" 
          onclick="window.location.href='menu_snaks_admin_page.php';">
          <input 
          class="form-button button search-button search-button-elements" 
          type="button" 
          value="Desserts" 
          onclick="window.location.href='menu_desserts_admin_page.php';">
          <input 
          class="form-button button search-button search-button-elements" 
          type="button" 
          value="Drinks" 
          onclick="window.location.href='menu_drinks_admin_page.php';">
        </div>
        <div class="search-elements-butt">
          <input 
          class="form-button button search-button search-button-elements" 
          type="button" 
          value="Promotions" 
          onclick="window.location.href='menu_promotions_admin_page.php';">
          <input 
          class="form-button button search-button search-button-elements" 
          type="button" 
          value="Crusts" 
          onclick="window.location.href='menu_crusts_admin_page.php';">
          <input 
          class="form-button button search-button search-button-elements" 
          type="button" 
          value="Ingredients" 
          onclick="window.location.href='menu_ingredients_admin_page.php';">
          <button class="form-button button modal-edit-price-base-btn-open" type="button" data-price="<?php echo $basePrice; ?>">Base Price</button>
        </div>
      </div>
    </section>
    <section class="menu-pizzas-section" id="menu-pizzas-section">
      <div class="products-container" id="products-container">
        <?php if ($productsResult && $productsResult->num_rows > 0): ?>
        <?php while($product = $productsResult->fetch_assoc()): ?>
        <?php
        $promotionEndDate = null;
        $promotionStartDate = null;
        $discountedPrice = null;
        $futurePromotion = false;

        if (!empty($product['promotion_id'])) {
          $promoId = (int)$product['promotion_id'];
          $promoQuery = "SELECT discount_percentage, start_date, end_date FROM promotions WHERE promotion_id = $promoId LIMIT 1";
          $promoResult = $conn->query($promoQuery);

          if ($promoResult && $promoResult->num_rows > 0) {
            $promotion = $promoResult->fetch_assoc();

            $today = date('Y-m-d');
            if ($promotion['start_date'] <= $today && $promotion['end_date'] >= $today) {

              $promotionStartDate = $promotion['start_date'];
              $promotionEndDate = $promotion['end_date'];
              $discountedPrice = $product['price'] * (1 - $promotion['discount_percentage'] / 100);
            } elseif ($promotion['start_date'] > $today) {

                $promotionStartDate = $promotion['start_date'];
                $futurePromotion = true;
              } elseif ($promotion['end_date'] < $today) {

                $conn->query("UPDATE products SET promotion_id = NULL WHERE product_id = " . (int)$product['product_id']);
              }
            }
          }
          ?>
      <div class="container menu-pizzas-container">
        <div class="pizza-img-text">
          <?php
          $imagePath = htmlspecialchars($product['image_path']);
          if (!file_exists($imagePath) || empty($imagePath)) {
          $imagePath = 'uploads/not-pic.jpg';
          }
          ?>
          <img class="img-dish" src="<?= $imagePath ?>" alt="<?= htmlspecialchars($product['name']) ?>">
        <div class="pizza-info">
          <div class="pizza-text">
            <h3 class="about-dish"><?= htmlspecialchars($product['name']) ?></h3>
            <p class="about-dish"><?= htmlspecialchars($product['description']) ?></p>
          </div>
          </div>
        </div>
        <div class="pizza-price-btns">
          <div class="pizza-option">
            <div class="pizza-option-prices">
              <?php if ($discountedPrice !== null): ?>
            <h4>
              <span class="h4-grey">
                $<?= number_format($product['price'], 2, ',', ' ') ?>
              </span><br>
              <span class="h4-red">
                $<?= number_format($discountedPrice, 2, ',', ' ') ?>
              </span>
            </h4>
            <?php if (!empty($promotionEndDate)): ?>
            <p class="promo-end-date">Offer valid until: <?= date('d.m.Y', strtotime($promotionEndDate)) ?></p>
            <?php endif; ?>
            <?php elseif ($futurePromotion && !empty($promotionStartDate)): ?>
            <h4>$<?= number_format($product['price'], 2, ',', ' ') ?></h4>
            <p class="promo-end-date">Promo starts on: <?= date('d.m.Y', strtotime($promotionStartDate)) ?></p>
            <?php else: ?>
            <h4>$<?= number_format($product['price'], 2, ',', ' ') ?></h4>
            <?php endif; ?>
            </div>
          </div>
            <div class="pizza-option-button">
              <input type="hidden" name="id" value="<?= $product['product_id'] ?>">
              <button 
              class="form-button button modal-edit-product-btn-open" 
              type="button"
              data-id="<?= $product['product_id'] ?>"
              data-category="<?= $product['category'] ?>"
              data-name="<?= htmlspecialchars($product['name']) ?>"
              data-description="<?= htmlspecialchars($product['description']) ?>"
              data-price="<?= $product['price'] ?>">Edit</button>
              <form method="post" action="delete_product.php" onsubmit="return confirm('Are you sure you want to delete this dish?');">
                <input type="hidden" name="id" value="<?= $product['product_id'] ?>">
                <button class="form-button button" type="submit">Delete</button>
              </form>
              <form method="post" action="delete_promo_product.php" onsubmit="return confirm('Are you sure you want to end this promotion early?');">
                <input type="hidden" name="id" value="<?= $product['product_id'] ?>">
                <button class="form-button button" type="submit">Remove Promo</button>
              </form>
            </div>
        </div>
      </div>
      <?php endwhile; ?>
      <?php else: ?>
      <?php endif; ?>
    </div>
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
  <div class="backdrop-produсts-sets is-hidden">
    <div class="modal modal-produts-sets">
      <button class="form-button modal-produсts-sets-btn-close button" type="button">Close</button>
      <h2>Add Dish</h2>
      <form name="modal-form-add-product" method="post" action="admin_add_product.php" enctype="multipart/form-data" autocomplete="off">
        <div class="form-field">
          <label class="form-label visually-hidden" for="client-name">Category</label>
          <select 
          class="form-input" 
          name="product-category" 
          id="product-category" 
          required>
          <option value="" disabled selected>Select Category</option>
          <option value="pizza">Pizza</option>
          <option value="pizza-roll">Pizza Rolls</option>
          <option value="snack">Snacks</option>
          <option value="dessert">Desserts</option>
          <option value="drink">Drinks</option>
          </select>
        </div>
        <div class="form-field">
          <label class="form-label visually-hidden" for="product-name">Name</label>
          <input 
          class="form-input" 
          type="text" 
          name="product-name" 
          id="product-name"
          placeholder="Name"
          required>
        </div>
        <div class="form-field">
          <label class="form-label visually-hidden" for="product-description">Description</label>
          <textarea 
          class="form-input form-input-textarea" 
          rows="3" 
          name="product-description" 
          id="product-description"
          placeholder="Description"
          required></textarea>
        </div>
        <div class="form-field">
          <label class="form-label visually-hidden" for="product-price">Price ($)</label>
          <input 
          class="form-input" 
          type="number" 
          name="product-price" 
          id="product-price"
          placeholder="Price ($)"
          min="0"
          required>
        </div>
        <div class="form-field">
          <label class="form-label visually-hidden" for="product-image">Image</label>
          <input 
          class="form-input" 
          type="file" 
          name="product-image" 
          id="product-image"
          accept="image/*"
          required>
        </div>
        <button class="form-button button button-add-product" type="submit">Add Dish</button>
      </form>
      <h2>Add Set</h2>
      <form name="modal-form-add-set" method="post" action="admin_add_set.php" enctype="multipart/form-data" autocomplete="off">
        <div class="form-field">
          <label class="form-label visually-hidden" for="set-name">Name</label>
          <input 
          class="form-input" 
          type="text" 
          name="set-name" 
          id="set-name"
          placeholder="Name"
          required>
        </div>
        <div class="form-field">
          <label class="form-label visually-hidden" for="set-description">Description</label>
          <textarea 
          class="form-input form-input-textarea" 
          rows="3" 
          name="set-description" 
          id="set-description"
          placeholder="Description"
          required></textarea>
        </div>
        <div class="form-field">
          <label class="form-label visually-hidden" for="set-price">Price ($)</label>
          <input 
          class="form-input" 
          type="number" 
          name="set-price" 
          id="set-price"
          placeholder="Price ($)"
          min="0"
          required>
        </div>
        <div class="form-field">
          <label class="form-label visually-hidden" for="set-image">Image</label>
          <input 
          class="form-input" 
          type="file" 
          name="set-image" 
          id="set-image"
          accept="image/*"
          required>
        </div>
        <button class="form-button button button-add-product" type="submit">Add Set</button>
      </form>
    </div>
  </div>
  <div class="backdrop-products-edit is-hidden">
    <div class="modal modal-products-edit">
      <button class="form-button modal-products-edit-btn-close button" type="button">Close</button>
      <h2>Edit Dish</h2>
      <form name="modal-form-edit-product" method="post" action="admin_edit_product.php" enctype="multipart/form-data" autocomplete="off">
        <div class="form-field">
          <label class="form-label visually-hidden" for="product-category-edit">Category</label>
          <select 
          class="form-input" 
          name="product-category-edit" 
          id="product-category-edit" 
          required>
          <option value="" disabled selected>Select Category</option>
          <option value="pizza">Pizza</option>
          <option value="pizza-roll">Pizza Rolls</option>
          <option value="snack">Snacks</option>
          <option value="dessert">Desserts</option>
          <option value="drink">Drinks</option>
          </select>
        </div>
        <div class="form-field">
          <label class="form-label visually-hidden" for="product-name-edit">Name</label>
          <input 
          class="form-input" 
          type="text" 
          name="product-name-edit" 
          id="product-name-edit"
          placeholder="Name"
          required>
        </div>
        <div class="form-field">
          <label class="form-label visually-hidden" for="product-description-edit">Description</label>
          <textarea 
          class="form-input form-input-textarea" 
          rows="3" 
          name="product-description-edit" 
          id="product-description-edit"
          placeholder="Description"
          required></textarea>
        </div>
        <div class="form-field">
          <label class="form-label visually-hidden" for="product-price-edit">Price ($)</label>
          <input 
          class="form-input" 
          type="number" 
          name="product-price-edit" 
          id="product-price-edit"
          placeholder="Price ($)"
          min="0" 
          required>
        </div>
        <div class="form-field">
          <label class="form-label visually-hidden" for="product-image-edit">Image</label>
          <input 
          class="form-input" 
          type="file" 
          name="product-image-edit" 
          id="product-image-edit"
          accept="image/*"
          >
        </div>
        <button class="form-button button button-edit-product" type="submit">Save</button>
      </form>
      <h2>Add Promotion to Dish</h2>
      <form name="modal-form-add-promo-product" method="post" action="admin_add_promo_product.php" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="product-id-for-promo" id="product-id-for-promo">
        <div class="form-field">
          <label class="form-label visually-hidden" for="product-price-add-promo">Discount Percentage (%)</label>
          <input 
          class="form-input" 
          type="number" 
          name="product-price-add-promo" 
          id="product-price-add-promo"
          placeholder="Discount Percentage (%)"
          max="100" 
          required>
        </div>
        <div class="form-field">
          <label class="form-label visually-hidden" for="product-start-date-add-promo">Start Date</label>
          <input 
          class="form-input client-order-datetime" 
          type="date" 
          name="product-start-date-add-promo" 
          id="product-start-date-add-promo"
          required>
        </div>
        <div class="form-field">
          <label class="form-label visually-hidden" for="product-end-date-add-promo">End Date</label>
          <input 
          class="form-input client-order-datetime" 
          type="date" 
          name="product-end-date-add-promo" 
          id="product-end-date-add-promo"
          required>
        </div>
        <button class="form-button button button-add-promo-product" type="submit">Add Promotion</button>
      </form>
    </div>
  </div>
  <div class="backdrop-base-edit is-hidden">
    <div class="modal modal-base-edit">
      <button class="form-button modal-base-edit-btn-close button" type="button">Close</button>
      <h2>Edit Base Price</h2>
      <form name="modal-form-edit-base" method="post" action="admin_edit_base.php" autocomplete="off">
        <div class="form-field">
          <label class="form-label visually-hidden" for="base-price-edit">Price ($)</label>
          <input 
          class="form-input" 
          type="number" 
          name="base-price-edit" 
          id="base-price-edit"
          placeholder="Ціна (грн)"
          min="0" 
          required>
        </div>
        <button class="form-button button button-edit-base" type="submit">Save</button>
      </form>
    </div>
  </div>
  <script src="modal.js"></script>
  <script src="modal-admin-add-products-sets.js"></script>
  <script src="modal-admin-edit-product.js"></script>
  <script src="modal-admin-edit-base.js"></script>
  <script src="sort-products-admin.js"></script>
  <script src="end-date-promo-products-admin.js"></script>
</body>
</html>