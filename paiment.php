<?php
session_start();
require 'db.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch cart items
$sql = "
    SELECT 
        c.title,
        c.price,
        c.image,
        ct.quantity
    FROM cart ct
    JOIN course c ON ct.course_id = c.id
    WHERE ct.user_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

$cartItems = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
    $total += $row['price'] * $row['quantity'];
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TrainUp | Payment</title>
  <link rel="icon" href="img/Group 100.png" type="image/png">
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/pa.css" />
</head>
<body>

<header>
  <nav>
    <ul>
      <li><img src="img/logo.png" alt=""></li>
      <li><a href="dashboard.php">Home</a></li>
      <li><a href="courses.php">Courses</a></li>
      <li><a href="events.php">Events</a></li>
      <li><a href="#">Community</a></li>
      <li><a href="#"><img src="img/Search.png" class="bar" alt=""></a></li>
      <li>|</li>
      <li><a href="panier.php"><img src="img/Group 68.png" class="bar" alt=""></a></li>
      <li><a href="#"><img src="img/Doorbell.png" class="bar" alt=""></a></li>
      <li><a href="profile.php"><img src="img/User.png" class="bar" alt=""></a></li>
    </ul>
  </nav>
</header>

<main class="payment-container">

  <!-- Payment info -->
  <div class="payment-info box">
    <h3>Payment Information</h3>
    <input type="text" placeholder="Full Name" required>
    <input type="tel" placeholder="Phone Number" required>
    <input type="email" placeholder="Email" required>
  </div>

  <!-- Order summary -->
  <div class="order-summary box">
    <h3>Your Order</h3>

    <?php if (!empty($cartItems)): ?>
        <?php foreach ($cartItems as $item): ?>
        <div class="order-item">
          <img src="img/<?= htmlspecialchars($item['image']) ?>" alt="">
          <div class="order-item-details">
            <p><?= htmlspecialchars($item['title']) ?></p>
            <small>Qty <?= (int)$item['quantity'] ?></small>
          </div>
          <p><?= number_format($item['price'] * $item['quantity'], 0) ?> DA</p>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>

    <div class="total-line"></div>
    <div class="total">Total: <?= number_format($total, 0) ?> DA</div>

    <!-- Payment Form -->
    <form action="pay_now.php" method="POST" enctype="multipart/form-data">
        <label class="payment-method">
          <input type="radio" name="method" value="cib" checked>
          <span>CIB/EDAHABIA (Slick-Pay)</span>
        </label>

        <label class="payment-method">
          <input type="radio" name="method" value="ccp">
          <span>CCP/BaridiMob</span>
        </label>

        <label class="payment-method">
          <input type="radio" name="method" value="receipt" id="receiptMethod">
          <span>Receipt Payment</span>
        </label>

        <!-- Receipt upload hidden by default -->
        <div id="receiptUpload" style="display:none; margin-top:10px;">
          <label>Upload payment receipt</label>
          <input type="file" name="receipt_image" accept="image/*">
        </div>

        <input type="hidden" name="total_amount" value="<?= $total ?>">
        <button class="button" type="submit">Pay Now</button>
    </form>

  </div>
</main>

<!-- JS to show/hide receipt upload -->
<script>
  const receiptRadio = document.getElementById("receiptMethod");
  const receiptUpload = document.getElementById("receiptUpload");

  document.querySelectorAll('input[name="method"]').forEach(radio => {
    radio.addEventListener("change", () => {
      receiptUpload.style.display = receiptRadio.checked ? "block" : "none";
    });
  });
</script>

</body>
</html>

