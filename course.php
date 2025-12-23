<?php
// course.php
include 'db.php'; // Your database connection file

// Get course ID from URL
$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch course
$courseQuery = $conn->query("SELECT * FROM course WHERE id = $course_id LIMIT 1");
if($courseQuery->num_rows === 0) {
    die("Course not found.");
}
$course = $courseQuery->fetch_assoc();

// Fetch sections/modules
$sectionsQuery = $conn->query("SELECT * FROM section WHERE course_id = $course_id ORDER BY position ASC");
$sections = $sectionsQuery->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>TrainUp | <?= htmlspecialchars($course['title']); ?></title>
  <link rel="icon" href="img/Group 100.png" type="image/png">
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/dash.css" />
  <style>
    /* === KEEP ALL YOUR COURSE CSS HERE EXACTLY AS IT WAS === */
    .course-details {
      background: transparent;
      color: #fff;
      padding: 20px;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      max-width: 800px;
      margin: 0 auto;
    }

    .course-details > h2 {
      font-weight: 700;
      font-size: 1.4rem;
      margin-bottom: 25px;
      color: #00ffff;
    }

    .course-header {
      display: flex;
      align-items: stretch;
      justify-content: center;
      gap: 30px;
      margin-bottom: 25px;
    }

    .course-image {
      flex: 1;
      border-radius: 18px;
      object-fit: cover;
      box-shadow: 0 0 20px rgba(0, 255, 255, 0.4);
      width: 100%;
      height: auto;
      max-height: 280px;
    }

    .price-box {
      flex: 1.6;
      border-radius: 22px;
      padding: 30px 40px;
      display: flex;
      flex-direction: column;
      box-shadow: 0 0 30px rgba(0, 255, 255, 0.4);
      color: #fff;
    }

    .price-box h3 {
      font-size: 2rem;
      font-weight: 900;
      color: #fff;
      margin-bottom: 15px;
    }

    .price-box span {
      color: #ff7b00;
      font-weight: 700;
      float: right;
    }

    .price-box p {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: 5px 0;
      font-size: 0.9rem;
      width: 100%;
    }

    .add-to-cart {
      background: linear-gradient(90deg, #00ffff, #00c4ff);
      color: #fff;
      font-weight: 700;
      font-size: 1rem;
      border: none;
      padding: 12px 50px;
      border-radius: 10px;
      cursor: pointer;
      margin-bottom: 12px;
      transition: 0.3s;
      text-decoration: none;
      text-align: center;
    }

    .add-to-cart:hover {
      background: linear-gradient(90deg, #00e5e5, #00bfff);
      transform: scale(1.05);
    }

    .last-updated {
      font-size: 0.9rem;
      font-weight: 600;
      color: #e0faff;
    }

    .certificate {
      font-size: 0.9rem;
      font-weight: 700;
      color: #fff;
      margin-top: 5px;
    }

    .course-stats {
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 25px 0 35px;
      text-align: center;
      gap: 50px;
    }

    .stat {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 10px;
      color: #fff;
      font-weight: 600;
      font-size: 1rem;
    }

    .stat img {
      width: 50px;
      height: 50px;
      padding: 8px;
      border-radius: 14px;
      border: 2px solid rgba(0, 255, 255, 0.6);
      background: rgba(255, 255, 255, 0.08);
      box-shadow: 0 0 12px rgba(0, 255, 255, 0.3);
    }

    .stat p {
      margin: 0;
      color: #e0faff;
      font-weight: 600;
      font-size: 0.95rem;
    }

    .description {
      padding: 20px;
      border-radius: 14px;
      margin-bottom: 35px;
    }

    .description h3 {
      color: #00ffff;
      margin-bottom: 10px;
      font-weight: 700;
      font-size: 1.1rem;
    }

    .description p {
      background: rgba(255, 255, 255, 0.08);
      border: 1.5px solid rgba(0, 255, 255, 0.5);
      font-size: 0.95rem;
      color: #ccefff;
      line-height: 1.6;
      padding: 20px 25px;
      border-radius: 12px;
      margin-top: 10px;
      box-shadow: 0 0 12px rgba(0, 255, 255, 0.15);
    }

    .concept-section h3 {
      color: #fff;
      margin-bottom: 18px;
      font-weight: 700;
      font-size: 1.2rem;
      text-align: center;
    }

    .concept-section h3 span {
      color: #00ffff;
    }

    .module {
      padding: 6px 12px;
      margin-bottom: 8px;
      border-radius: 10px;
    }

    /* === MEDIA QUERIES === */
    @media (max-width: 1024px) { /* ... keep your media queries ... */ }
    @media (max-width: 768px) { /* ... keep your media queries ... */ }
    @media (max-width: 480px) { /* ... keep your media queries ... */ }
  </style>
</head>

<body>
  <header class="navigation">
    <nav>
      <ul>
        <li><img src="img/logo.png" alt=""></li>
        <li><a href="dashboard.php">Home</a></li>
        <li><a class="me" href="courses.php">Courses</a></li>
        <li><a href="events.php">Events</a></li>
        <li><a href="my_courses.php">My Courses</a></li>
        <li><a href="#"><img src="img/Search.png" class="bar" alt="search" /></a></li>
        <li>|</li>
        <li><a href="panier.php"><img src="img/Group 68.png" class="bar" alt="cart" /></a></li>
        <li><a href="#"><img src="img/Doorbell.png" class="bar" alt="notif" /></a></li>
        <li><a href="profile.php"><img src="img/User.png" class="bar" alt="user" /></a></li>
      </ul>
    </nav>
  </header>

  <main class="dashboard-container">
    <div class="sidebar">
      <a href="courses.php" class="sidebar-link active">Courses</a>
      <a href="dashboard.php" class="sidebar-link">Dashboard</a>
      <a href="enrolled-courses.php" class="sidebar-link">Enrolled Courses</a>
      <a href="events.php" class="sidebar-link">Events</a>
      <a href="my_courses.php" class="sidebar-link">My Courses</a>
      <a href="settings.php" class="sidebar-link">Settings</a>
    </div>

    <section class="dashboard-content">
      <div class="course-details">
        <h2><?= htmlspecialchars($course['title']); ?></h2>

        <div class="course-header">
         <img src="img/<?= htmlspecialchars($course['image']); ?>" 
     alt="<?= htmlspecialchars($course['title']); ?>" 
     class="course-image" />

          <div class="price-box">
            <h3><?= htmlspecialchars($course['price']); ?> DA</h3>
<form method="POST" action="add_to_cart.php">
  <input type="hidden" name="course_id" value="<?= $course['id']; ?>">
  <button type="submit" class="add-to-cart">Add to Cart</button>
</form>

            <p class="last-updated">Last updated <span><?= date('d F Y', strtotime($course['last_updated'])); ?></span></p>
            <p class="certificate">Certificate <span><?= htmlspecialchars($course['certificate']); ?></span></p>
          </div>
        </div>

        <div class="course-stats">
          <div class="stat">
            <img src="img/Alarm Add.png" alt="Duration" />
            <p><?= htmlspecialchars($course['duration']); ?> Weeks of Study</p>
          </div>
          <div class="stat">
            <img src="img/Users.png" alt="Type" />
            <p><?= htmlspecialchars($course['type']); ?></p>
          </div>
          <div class="stat">
            <img src="img/Laptop.png" alt="Group" />
            <p><?= htmlspecialchars($course['group_size']); ?> in the group</p>
          </div>
        </div>

        <div class="description">
          <h3>Description</h3>
          <p><?= nl2br(htmlspecialchars($course['description'])); ?></p>
        </div>

        <div class="concept-section">
          <h3>Concept of <span><?= htmlspecialchars($course['title']); ?></span></h3>

          <?php foreach($sections as $section): ?>
          <label class="module">
            <input type="radio" name="accordion" />
            <div class="header">
              <span><?= htmlspecialchars($section['title']); ?></span>
              <span class="icon"></span>
            </div>
            <div class="content">
              <p><?= nl2br(htmlspecialchars($section['content'])); ?></p>
            </div>
          </label>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  </main>
</body>
</html>
