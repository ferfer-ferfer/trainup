<?php
// Include database connection
include 'db.php';

// Fetch courses with trainer info
$sql = "SELECT c.id, c.title, c.description, c.duration, c.price, c.start_date, c.level,
               c.image, u.firstname, u.lastname
        FROM course c
        JOIN users u ON c.trainer_id = u.id";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TrainUp | Courses</title>
  <link rel="icon" href="img/Group 100.png" type="image/png">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/dash.css">
  <style>
    .dashboard-content h2 {
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 25px;
      color: #fff;
    }
    .dashboard-content h2 span {
      color: #00e4ff;
    }
    .dashboard-content .courses-list {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 100px;
    }
    .dashboard-content .course-box {
      width: 280px;
    }
    .me {
      color: #00ffff;
    }
  </style>
</head>
<body>
  <header>
    <nav>
      <ul>
        <li><img src="img/logo.png" alt=""></li>
        <li><a href="dashboard.html">Home</a></li>
        <li><a class="me" href="courses.php">Courses</a></li>
        <li><a href="events.html">Events</a></li>
        <li><a href="#">Community</a></li>
        <li><a href="#"><img src="img/Search.png" class="bar" alt=""></a></li>
        <li>|</li>
        <li><a href="panier.html"><img src="img/Group 68.png" class="bar" alt=""></a></li>
        <li><a href="#"><img src="img/Doorbell.png" class="bar" alt=""></a></li>
        <li><a href="profile.html"><img src="img/User.png" class="bar" alt=""></a></li>
      </ul>
    </nav>
  </header>
  <main class="dashboard-container">
    <div class="sidebar">
      <a href="courses.php" class="sidebar-link active">Courses</a>
      <a href="dashboard.html" class="sidebar-link">Dashboard</a>
      <a href="enrolled-courses.html" class="sidebar-link">Enrolled Courses</a>
      <a href="events.html" class="sidebar-link">Events</a>
      <a href="community.html" class="sidebar-link">Community</a>
      <a href="settings.html" class="sidebar-link">Settings</a>
    </div>
    <section class="dashboard-content">
      <h2><span>Our</span> courses</h2>
      <div class="categorie">
        <button class="active">All</button>
        <button>Web Development</button>
        <button>UI/UX Design</button>
        <button>Digital Marketing</button>
        <button>Languages</button>
      </div>
      <div class="courses-list">
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $title = htmlspecialchars($row['title']);
                $trainer_name = htmlspecialchars($row['firstname'] . ' ' . $row['lastname']);
                $price = htmlspecialchars($row['price']);
                $course_img = htmlspecialchars($row['image']);

                echo '<div class="course-box">
                        <div class="img-box">
                          <img src="img/'.$course_img.'" alt="'.$title.'">
                        </div>
                        <div class="course-card">
                          <h3>'.$title.'</h3>
                          <p>by '.$trainer_name.'</p>
                          <p class="price">'.$price.' DZD</p>
                          <a href="course.php?id='.$row['id'].'" class="see-more">See more</a>
                        </div>
                      </div>';
            }
        } else {
            echo "<p>No courses available at the moment.</p>";
        }
        $conn->close();
        ?>
      </div>
    </section>
  </main>
</body>
</html>
