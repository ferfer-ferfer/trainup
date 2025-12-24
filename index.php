<?php
include "db.php";

// Fetch latest 4 courses for normal display
$sql_courses = "SELECT id, title, price, image, level, duration 
                FROM course 
                ORDER BY last_updated DESC 
                LIMIT 4";
$result_courses = $conn->query($sql_courses);

// Fetch promotions with course info
$sql_promotions = "SELECT p.id AS promotion_id, p.discount, c.id AS course_id, c.title, c.price, c.image, c.level, c.duration
                   FROM promotion p
                   JOIN course c ON p.course_id = c.id
                   ORDER BY p.start_date DESC
                   LIMIT 4";
$result_promotions = $conn->query($sql_promotions);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title> TrainUp | Index</title>
  <link rel="icon" href="img/Group 100.png" type="image/png">
  <link rel="stylesheet" href="css/style.css">
  <style>
    #reviews {
      text-align: center;
      padding: 80px 0;
      color: #fff;
      font-family: 'Poppins', sans-serif;
    }

    #reviews h2 {
      font-size: 2.5em;
      font-weight: 800;
      margin-bottom: 40px;
    }

    #reviews h2 span {
      color: #00d4ff;
    }

    #reviews .reviews-box {
      background: rgba(255, 255, 255, 0.08);
      border: 2px solid #00d4ff;
      border-radius: 25px;
      padding: 40px 25px;
      width: 80%;
      margin: 0 auto;
      box-shadow: 0 0 30px rgba(0, 212, 255, 0.25);
      backdrop-filter: blur(10px);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    #reviews .reviews-rev {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 60px;
      justify-items: center;
      margin-bottom: 60px;
    }

    #reviews .review {
      background: rgba(255, 255, 255, 0.9);
      color: #111;
      border-radius: 10px;
      padding: 20px;
      margin: 15px;
      text-align: left;
      box-shadow: 0 0 15px rgba(0, 200, 255, 0.25);
      transition: 0.3s;
    }

    #reviews .review:hover {
      transform: translateY(-5px);
      box-shadow: 0 0 25px rgba(0, 200, 255, 0.4);
    }

    #reviews .review img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #00c8ff;
      margin-bottom: 10px;
    }

    #reviews .review p {
      font-size: 15px;
      line-height: 1.5;
    }

    #reviews .see-more {
      background: #00d5ff;
      color: #fff;
      border: none;
      border-radius: 25px;
      padding: 12px 32px;
      font-weight: 600;
      cursor: pointer;
      transition: 0.3s;
      font-size: 15px;
      box-shadow: 0 0 15px rgba(0, 200, 255, 0.6);
    }

    #reviews .see-more:hover {
      transform: scale(1.05);
      box-shadow: 0 0 25px rgba(0, 200, 255, 0.9);
    }

    #reviews h3 {
      font-size: 20px;
      font-weight: 500;
      margin-top: 35px;
    }

    #reviews h3 span {
      color: #00d5ff;
      font-weight: 600;
    }
    .personne{
  width: 150px;
  border-radius: 50%;
   border: 1px solid #00ffff;
}

    @keyframes float {
  0% { transform: translateY(0px); }
  50% { transform: translateY(-50px); }
  100% { transform: translateY(0px); }
}

.personne.float {
  animation: float 2s ease-in-out infinite;
}
  </style>
</head>

<body>
  <header>
    <nav>
      <ul>
        <li><img src="img/logo.png" alt=""></li>
        <li><a href="index.php">Home</a></li>
        <li><a href="#courses-list">Courses</a></li>
        <li><a href="aboutas.php">About Us</a></li>
        <li><a href="#contact">Contact</a></li>
        <li><a href="login.php" class="button">Sign In</a></li>
      </ul>
    </nav>
  </header>

  <section id="part1">
    <div class="left">
      <h1>TrainUp</h1>
      <p>All Your Learning <span>in One Platform</span></p>
      <div class="buttons">
        <a href="#part3" class="primary">Get Started</a>
        <a href="login.html" class="secondary">Sign Up</a>
      </div>
    </div>
    <div class="right">
      <img class="personne float" src="img/indexx.png" alt="TrainUp Platform" width="300">
    </div>
  </section>

  <section id="part2">
    <h2><span>Our</span> Sponsors</h2>
    <p></p>
  </section>

  <section id="part3">
    <h2><span>Our</span> Courses</h2>

    <div class="categorie">
      <button class="active">All</button>
      <button>Web Development</button>
      <button>UI/UX Design</button>
      <button>Digital Marketing</button>
      <button>Languages</button>
    </div>

    <div id="courses-list" class="courses-list">

<?php if ($result_courses->num_rows > 0) { ?>
  <?php while ($row = $result_courses->fetch_assoc()) { ?>

    <div class="course-box">
      <div class="img-box">
        <img src="img/<?php echo htmlspecialchars($row['image']); ?>"
             alt="<?php echo htmlspecialchars($row['title']); ?>">
      </div>

      <div class="course-card">
        <h3><?php echo htmlspecialchars($row['title']); ?></h3>

        <p>
          <?php echo htmlspecialchars($row['level']); ?> ·
          <?php echo htmlspecialchars($row['duration']); ?>
        </p>

        <p class="price"><?php echo $row['price']; ?> DZD</p>

        <!-- Redirect to login -->
        <a href="login.php" class="see-more">See more</a>
      </div>
    </div>

  <?php } ?>
<?php } else { ?>
  <p>No courses available.</p>
<?php } ?>

</div>


    <a class="see-all" href="login.php">See all courses</a>
  </section>

  <section id="certification">
    <h2><span>Our</span> Certification</h2>
    <div class="certification-list">
      <div class="cert-card">
        <div class="icon-box"><img src="img/Group 28.png" alt="Certificate of Completion"></div>
        <h3>Certificate of Completion</h3>
        <p>Earn this certificate by completing your training modules successfully.</p>
        <button class="see-more">Earn your Certification</button>
      </div>

      <div class="cert-card">
        <div class="icon-box"><img src="img/Graduation Cap.png" alt="Certificate of Achievement"></div>
        <h3>Certificate of Achievement</h3>
        <p>Show your dedication and excellence with an achievement certificate.</p>
        <button class="see-more">Earn your Certification</button>
      </div>

      <div class="cert-card">
        <div class="icon-box"><img src="img/Trophy.png" alt="Professional Certification"></div>
        <h3>Professional Certification</h3>
        <p>Gain professional recognition for mastering your field of expertise.</p>
        <button class="see-more">Earn your Certification</button>
      </div>
    </div>
  </section>

  <section id="reviews">
    <h2><span>Our</span> Reviews</h2>
    <div class="reviews-box">
      <div class="reviews-rev">
        <div class="review">
          <img src="student1.jpg" alt="Student 1">
          <p>“TrainUp helped me become confident in web design.”</p>
        </div>
        <div class="review">
          <img src="student2.jpg" alt="Student 2">
          <p>“Great platform for learning. The community is so helpful.”</p>
        </div>
      </div>
      <button class="see-more">See more</button>
      <h3>Join 1,000+ Students Learning with <span>TrainUp</span></h3>
    </div>
  </section>

  <section id="part3">
  <h2><span>Promotion</span> of the Month</h2>

  <div class="courses-list">


  <?php if ($result_promotions->num_rows > 0) { ?>
    <?php while ($promo = $result_promotions->fetch_assoc()) { 
        // Calculate discounted price
        $discounted_price = $promo['price'] * (1 - $promo['discount'] / 100);
    ?>
      <div class="course-box">
        <div class="img-box">
          <img src="img/<?php echo htmlspecialchars($promo['image']); ?>" 
               alt="<?php echo htmlspecialchars($promo['title']); ?>">
        </div>

        <div class="course-card">
          <h3><?php echo htmlspecialchars($promo['title']); ?></h3>

          <p>
            <?php echo htmlspecialchars($promo['level']); ?> ·
            <?php echo htmlspecialchars($promo['duration']); ?>
          </p>

          <p class="price">
  <span style="color: #fff; text-decoration: line-through; display: block;">
    <?php echo $promo['price']; ?> DZD
  </span>
  <?php echo round($discounted_price); ?> DZD
</p>


          <!-- Redirect to login -->
          <a href="login.php" class="see-more">See more</a>
        </div>
      </div>
    <?php } ?>
  <?php } else { ?>
    <p>No promotions available.</p>
  <?php } ?>

  </div>


  <a class="see-all" href="login.php">See all Promotions</a>
</section>

  <footer>
    <div id="contact" class="footer-container">
      <div class="footer-section logo-section">
        <img src="img/logo.png" alt="TrainUp Logo" class="footer-logo">
        <p>Empowering learners through connection and knowledge.</p>
        <div class="social-icons">
          <a href="https://www.instagram.com/"><i class="fab fa-instagram"></i></a>
          <a href="https://t.me/"><i class="fab fa-telegram"></i></a>
          <a href="https://www.linkedin.com/"><i class="fab fa-linkedin"></i></a>
        </div>
      </div>

      <div class="footer-section">
        <h3>MENU</h3>
        <ul>
          <li><a href="index.html">Home</a></li>
          <li><a href="courses.html">Courses</a></li>
          <li><a href="aboutas.html">About Us</a></li>
          <li><a href="#contact">Contact</a></li>
        </ul>
      </div>

      <div class="footer-section">
        <h3>INFORMATION</h3>
        <ul>
          <li><a href="#certification">Certification</a></li>
          <li><a href="#team">Our Team</a></li>
        </ul>
      </div>

      <div class="footer-section">
        <h3>CONTACT</h3>
        <ul>
          <li><strong>Email:</strong> trainupcommunity@gmail.com</li>
          <li><strong>Phone:</strong> +213 63336641</li>
        </ul>
      </div>
    </div>
  </footer>
</body>
</html>
