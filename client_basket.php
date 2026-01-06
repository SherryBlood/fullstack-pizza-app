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

$basketStmt = $conn->prepare("SELECT basket_id FROM baskets WHERE customer_id = ? AND is_ordered = 0 LIMIT 1");
$basketStmt->bind_param("i", $customer_id);
$basketStmt->execute();
$basketResult = $basketStmt->get_result();

if ($basketResult->num_rows === 0) {
    echo "<script>alert('Your cart is empty!'); window.location.href = 'main_client_page.php';</script>";
    exit;
}

$basket_id = $basketResult->fetch_assoc()['basket_id'];


$productQuery = $conn->prepare("SELECT p.*, bp.quantity FROM basket_products bp JOIN products p ON bp.product_id = p.product_id WHERE bp.basket_id = ?");
$productQuery->bind_param("i", $basket_id);
$productQuery->execute();
$productsResult = $productQuery->get_result();


$setQuery = $conn->prepare("SELECT s.*, bs.quantity FROM basket_sets bs JOIN sets s ON bs.set_id = s.set_id WHERE bs.basket_id = ?");
$setQuery->bind_param("i", $basket_id);
$setQuery->execute();
$setsResult = $setQuery->get_result();


$customQuery = $conn->prepare("
  SELECT 
    bc.id AS basket_customization_id,
    bc.quantity,
    pc.customization_id,
    pc.price AS customization_price,
    c.name AS crust_name,
    pb.price AS base_price
  FROM basket_customizations bc
  JOIN pizza_customizations pc ON bc.customization_id = pc.customization_id
  JOIN crusts c ON pc.crust_id = c.crust_id
  JOIN pizza_base pb ON pc.base_id = pb.base_id
  WHERE bc.basket_id = ?
");

if (!$customQuery) {
    die("Prepare failed: " . $conn->error);
}

$customQuery->bind_param("i", $basket_id);
if (!$customQuery->execute()) {
    die("Execute failed: " . $customQuery->error);
}

$customResult = $customQuery->get_result();
if (!$customResult) {
    die("Failed to get result: " . $conn->error);
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cart</title>
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
    <section class="hero-section-client-basket">
      <div class="container">
        <h1 class="hero-title">Cart</h1>
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
    <section class="to-do-order-section" id="to-do-order-section">
      <button class="form-button button" id="scroll-to-do-order-btn" type="button">Order</button>
    </section>
    <section class="menu-pizzas-section" id="menu-pizzas-section">
      <div class="products-container" id="products-container">
        <?php while ($row = $productsResult->fetch_assoc()): ?>
          <?php
          $discountedPrice = null;
          $promotionStartDate = null;
          $promotionEndDate = null;
          $futurePromotion = false;

          if (!empty($row['promotion_id'])) {
            $promoId = (int)$row['promotion_id'];
            $promoRes = $conn->query("SELECT discount_percentage, start_date, end_date FROM promotions WHERE promotion_id = $promoId LIMIT 1");
            if ($promoRes && $promoRes->num_rows > 0) {
              $promo = $promoRes->fetch_assoc();
              $today = date('Y-m-d');
              $promotionStartDate = $promo['start_date'];
              $promotionEndDate = $promo['end_date'];

              if ($promotionStartDate <= $today && $promotionEndDate >= $today) {
                $discountedPrice = $row['price'] * (1 - $promo['discount_percentage'] / 100);
              } elseif ($promotionEndDate < $today) {
                $conn->query("UPDATE products SET promotion_id = NULL WHERE product_id = $row[product_id]");
              } elseif ($promotionStartDate > $today) {
                $futurePromotion = true;
              }
            }
          }

          $imagePath = htmlspecialchars($row['image_path']);
          if (!file_exists($imagePath) || empty($imagePath)) {
            $imagePath = 'uploads/not-pic.jpg';
          }
          ?>
          <div class="container menu-pizzas-container">
            <div class="pizza-img-text">
              <img class="img-dish" src="<?= $imagePath ?>" alt="<?= htmlspecialchars($row['name']) ?>">
              <div class="pizza-info">
                <div class="pizza-text">
                  <h3 class="about-dish"><?= htmlspecialchars($row['name']) ?></h3>
                  <p class="about-dish"><?= htmlspecialchars($row['description']) ?></p>
                </div>
              </div>
            </div>
            <div class="pizza-price-btns">
              <form method="post" action="change_product_quantity.php">
                <input type="hidden" name="type" value="product">
                <input type="hidden" name="id" value="<?= $row['product_id'] ?>">
                Quantity
                <div class="form-field">
                  <input
                  class="form-input form-input-basket" 
                  type="number" 
                  name="quantity" 
                  value="<?= $row['quantity'] ?>" 
                  min="1" 
                  required>
                </div>
                <p class="promo-end-date h4-red edit-quan">Make sure to save <br>quantity changes!</p>
                <button class="form-button button" type="submit">Save</button>
              </form>

              <div class="pizza-option-prices">
                <?php if ($discountedPrice !== null): ?>
                  Promo Dish
                  <h4>
                    <span class="h4-grey">$<?= number_format($row['price'], 2, ',', ' ') ?></span><br>
                    <span class="h4-red">$<?= number_format($discountedPrice, 2, ',', ' ') ?></span>
                  </h4>
                  <?php if (!empty($promotionEndDate)): ?>
                    <p class="promo-end-date">Offer valid until: <?= date('d.m.Y', strtotime($promotionEndDate)) ?></p>
                  <?php endif; ?>
                  <?php elseif ($futurePromotion && !empty($promotionStartDate)): ?>
                    <p class="promo-end-date">Offer starts at: <?= date('d.m.Y', strtotime($promotionStartDate)) ?></p>
                    <h4>$<?= number_format($row['price'], 2, ',', ' ') ?></h4>
                    <?php else: ?>
                      Unit Price
                      <h4>$<?= number_format($row['price'], 2, ',', ' ') ?></h4>
                    <?php endif; ?>
                    <?php
                    $finalPrice = $discountedPrice !== null ? $discountedPrice : $row['price'];
                    $total = $finalPrice * $row['quantity'];
                    ?>
                    <div class="pizza-option-prices">
                      <h4>Subtotal: $<?= number_format($total, 2, ',', ' ') ?></h4>
                    </div>

                  </div>

                  <form method="post" action="delete_product_from_basket.php">
                    <input type="hidden" name="id" value="<?= $row['product_id'] ?>">
                    <button class="form-button button" type="submit">Remove</button>
                  </form>
                </div>
                
              </div>
            <?php endwhile; ?>
          </div>

          <div class="products-container" id="products-container">
            <?php while ($set = $setsResult->fetch_assoc()): ?>
              <div class="container menu-pizzas-container">
                <div class="pizza-img-text">
                  <?php
                  $imagePath = htmlspecialchars($set['image_path']);
                  if (!file_exists($imagePath) || empty($imagePath)) {
                    $imagePath = 'uploads/not-pic.jpg';
                  }
                  ?>
                  <img class="img-dish" src="<?= $imagePath ?>" alt="<?= htmlspecialchars($set['name']) ?>">
                  <div class="pizza-info">
                    <div class="pizza-text">
                      <h3 class="about-dish"><?= htmlspecialchars($set['name']) ?></h3>
                      <p class="about-dish"><?= htmlspecialchars($set['description']) ?></p>
                    </div>
                  </div>
                </div>

                <div class="pizza-price-btns">
                  <form method="post" action="change_set_quantity.php">
                    <input type="hidden" name="type" value="set">
                    <input type="hidden" name="id" value="<?= $set['set_id'] ?>">
                    Quantity
                    <div class="form-field">
                      <input 
                      class="form-input form-input-basket"
                      type="number" 
                      name="quantity" 
                      value="<?= $set['quantity'] ?>" 
                      min="1" 
                      required>
                    </div>
                    <p class="promo-end-date h4-red edit-quan">Make sure to save <br>quantity changes!</p>
                    <button class="form-button button" type="submit">Save</button>
                  </form>
                  <div class="pizza-option-prices">
                    Unit Price
                    <h4>$<?= number_format($set['price'], 2, ',', ' ') ?></h4>
                    <?php

                    $totalSet = $set['price'] * $set['quantity'];
                    ?>
                    <div class="pizza-option-prices">
                      <h4>Subtotal: $<?= number_format($totalSet, 2, ',', ' ') ?></h4>
                    </div>
                  </div>


                  <form method="post" action="delete_set_from_basket.php">
                    <input type="hidden" name="id" value="<?= $set['set_id'] ?>">
                    <button class="form-button button" type="submit">Remove</button>
                  </form>
                </div>
              </div>
            <?php endwhile; ?>
          </div>

          <div class="products-container" id="products-container">
            <?php while ($customPizza = $customResult->fetch_assoc()): ?>
              <div class="container menu-pizzas-container">
                <div class="pizza-img-text">
                  <img class="img-dish" src="uploads/custom_pizza.jpg" alt="Custom pizza">
                  <div class="pizza-info">
                    <div class="pizza-text">
                      <h3 class="about-dish">Custom pizza</h3>
                      <p class="about-dish">Crust: <?= htmlspecialchars($customPizza['crust_name']) ?></p>

                      <?php

                      $ingredientsStmt = $conn->prepare("
                        SELECT i.name 
                        FROM pizza_ingredients pi
                        JOIN ingredients i ON pi.ingredient_id = i.ingredient_id
                        WHERE pi.customization_id = ?
                        ");
                      $ingredientsStmt->bind_param("i", $customPizza['customization_id']);
                      $ingredientsStmt->execute();
                      $ingredientsResult = $ingredientsStmt->get_result();
                      $ingredientNames = [];
                      while ($ingredient = $ingredientsResult->fetch_assoc()) {
                        $ingredientNames[] = htmlspecialchars($ingredient['name']);
                      }
                      $ingredientsStmt->close();
                      ?>

                      <?php if (!empty($ingredientNames)): ?>
                        <p class="about-dish">Ingredients: <?= implode(', ', $ingredientNames) ?></p>
                        <?php else: ?>
                          <p class="about-dish">No extra ingredients</p>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>

                  <div class="pizza-price-btns">
                    <form name="basket-quantity" method="post" action="change_custompizza_quantity.php" autocomplete="off">
                      Quantity
                      <div class="form-field">
                        <input 
                        class="form-input form-input-basket" 
                        type="number" 
                        name="quantity" 
                        id="basket-quantity"
                        placeholder="Quantity"
                        min="1"
                        value="<?= (int)$customPizza['quantity'] ?>" 
                        required>
                      </div>
                      <p class="promo-end-date h4-red edit-quan">Make sure to save <br>quantity changes!</p>
                      <input type="hidden" name="type" value="custom">
                      <input type="hidden" name="id" value="<?= $customPizza['basket_customization_id'] ?>">
                      <button class="form-button button button-basket-quantity" type="submit">Save</button>
                    </form>
                    <div class="pizza-option-prices">
                      Unit Price
                      <h4>$<?= number_format($customPizza['customization_price'], 2, ',', ' ') ?></h4>
                      <?php
                      $totalCustom = $customPizza['customization_price'] * $customPizza['quantity'];
                      ?>
                      <div class="pizza-option-prices">
                        <h4>Subtotal: $<?= number_format($totalCustom, 2, ',', ' ') ?></h4>
                      </div>
                    </div>
                    <div class="pizza-option-button">
                      <form method="post" action="delete_custompizza_from_basket.php">
                        <input type="hidden" name="id" value="<?= $customPizza['basket_customization_id'] ?>">
                        <button class="form-button button" type="submit">Remove</button>
                      </form>
                    </div>
                  </div>
                </div>
              <?php endwhile; ?>
            </div>
            <?php
            $totalPrice = 0.0;


            $productsResult->data_seek(0);
            while ($row = $productsResult->fetch_assoc()) {
              $price = $row['price'];
              $quantity = $row['quantity'];
              $discounted = $price;

              if (!empty($row['promotion_id'])) {
                $promoId = (int)$row['promotion_id'];
                $promoRes = $conn->query("SELECT discount_percentage, start_date, end_date FROM promotions WHERE promotion_id = $promoId LIMIT 1");
                if ($promoRes && $promoRes->num_rows > 0) {
                  $promo = $promoRes->fetch_assoc();
                  $today = date('Y-m-d');
                  if ($promo['start_date'] <= $today && $promo['end_date'] >= $today) {
                    $discounted = $price * (1 - $promo['discount_percentage'] / 100);
                  }
                }
              }

              $totalPrice += $discounted * $quantity;
            }


            $setsResult->data_seek(0);
            while ($set = $setsResult->fetch_assoc()) {
              $totalPrice += $set['price'] * $set['quantity'];
            }


            $customResult->data_seek(0);
            while ($custom = $customResult->fetch_assoc()) {
              $totalPrice += $custom['customization_price'] * $custom['quantity'];
            }
            ?>
          </section>

          <section class="client-basket-section" id="client-basket-section">
            <div class="container client-basket-container">
              <p class="caut-p-order">Cash payment. Order preparation time is 40 minutes. Delivery takes 10 minutes after the order is ready.</p>
              <form name="basket-form" method="post" action="submit_order.php" autocomplete="off">
                <div class="basket-form-elements">
                  <div class="client-basket-order-date">
                    <div class="client-basket-order-info-ab">
                      <h5 class="basket-order-head">Estimated ready time:</h5>
                      <input 
                      class="client-order-datetime" 
                      type="datetime-local" 
                      name="ready_time" 
                      required>
                    </div>
                    <div class="client-basket-order-info-ab">
                      <h5 class="basket-order-head">Delivery Method</h5>
                      <input 
                      class="client-order-radio" 
                      value="address_pickup"
                      type="radio"
                      name="delivery_type" 
                      id="client-basket-add"
                      required>
                      <label class="form-label basket-label" for="client-basket-add"><span class="basket-radio"></span>Delivery to address</label>
                      <input 
                      class="client-order-radio" 
                      type="radio"
                      name="delivery_type" 
                      value="self_pickup"
                      id="client-basket-self"
                      required>
                      <label class="form-label basket-label" for="client-basket-self"><span class="basket-radio"></span>Pickup</label>
                    </div>
                  </div>
                  <h4 class="order-price-display" id="order-total-price">
                    $<?= number_format($totalPrice, 2, ',', ' ') ?>
                  </h4>
                  <button class="form-button button basket-button" type="submit" id="to-do-order-btn">Order Now</button>
                </div>
              </form>
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
  <script src="modal.js"></script>
  <script src="yourpizza-price-check.js"></script>
  <script src="client-page.js"></script>
  <script src="basket-datetime-client.js"></script>
  <script src="scroll-to-do-order.js"></script>
</body>
</html>