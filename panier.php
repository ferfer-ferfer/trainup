<?php
session_start();
include 'db.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];


$sql = "SELECT c.id AS cart_id, c.quantity, c.price AS cart_price, 
               co.title, co.duration, co.trainer_id, u.username AS instructor
        FROM cart c
        JOIN course co ON c.course_id = co.id
        JOIN users u ON co.trainer_id = u.id
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = $result->fetch_all(MYSQLI_ASSOC);


$total = 0;
foreach ($cart_items as $item) {
    $total += $item['cart_price'] * $item['quantity'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TrainUp | Cart</title>
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
        <li><a href="cart.php"><img src="img/Group 68.png" class="bar" alt=""></a></li>
        <li><a href="#"><img src="img/Doorbell.png" class="bar" alt=""></a></li>
        <li><a href="profile.php"><img src="img/User.png" class="bar" alt=""></a></li>
      </ul>
    </nav>
  </header>

  <main>
    <section class="cart">
      <div class="cart-items box">
        <h2>My Cart</h2>
        <?php if(count($cart_items) > 0): ?>
        <table>
          <thead>
            <tr>
              <th>Course</th>
              <th>Duration</th>
              <th>Price</th>
              <th>Instructor</th>
              <th>Quantity</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($cart_items as $item): ?>
            <tr>
              <td><?= htmlspecialchars($item['title']); ?></td>
              <td><?= htmlspecialchars($item['duration']); ?></td>
              <td><?= number_format($item['cart_price'], 2); ?> DA</td>
              <td><?= htmlspecialchars($item['instructor']); ?></td>
              <td><?= $item['quantity']; ?></td>
              <td>
                <form method="POST" action="remove_from_cart.php" style="display:inline;">
                  <input type="hidden" name="cart_id" value="<?= $item['cart_id']; ?>">
                  <button type="submit" style="background:none;border:none;cursor:pointer;">
                    <img src="img/Delete.png" alt="Delete" style="width:30px; height:30px;">
                  </button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php else: ?>
          <p>Your cart is empty.</p>
        <?php endif; ?>
      </div>

      <div class="summary box">
        <h3>Order Summary</h3>
        <div>
          <?php foreach($cart_items as $item): ?>
            <p><?= number_format($item['cart_price'] * $item['quantity'], 2); ?> DA</p>
          <?php endforeach; ?>
          <hr>
          <h4>= <?= number_format($total, 2); ?> DA</h4>
        </div>
        <a href="paiment.php" class="button">Buy Now</a>
      </div>
    </section>
  </main>
</body>
</html>

