<?php 
require 'db.php'; // your database connection

// Detect filter from URL
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Build SQL based on filter
switch($filter){
    case 'online':
        $sql = "SELECT * FROM events WHERE type='Online' ORDER BY start_date ASC";
        break;

    case 'onsite':
        $sql = "SELECT * FROM events WHERE type='Onsite' ORDER BY start_date ASC";
        break;

    case 'upcoming':
        $sql = "SELECT * FROM events WHERE start_date >= CURDATE() ORDER BY start_date ASC";
        break;

    case 'all':
    default:
        $sql = "SELECT * FROM events ORDER BY start_date ASC";
        break;
}

$result = $conn->query($sql);
?>  

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TrainUp | Events</title>
  <link rel="icon" href="img/Group 100.png" type="image/png">
  <link rel="stylesheet" href="css/dash.css" />
  <link rel="stylesheet" href="css/style.css" />

  <style>
    .event-container {
      flex: 3;
      background: rgba(0, 31, 84, 0.3);
      border: 1px solid rgba(0, 255, 255, 0.4);
      border-radius: 10px;
      padding: 25px;
      box-shadow: 0 0 25px rgba(0, 255, 255, 0.1);
    }
    .event-container h2 {
      font-size: 24px;
      color: #00eaff;
      margin-bottom: 25px;
    }
    .cards-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 25px;
    }
    .card {
      background: rgba(0, 31, 84, 0.7);
      border: 1px solid rgba(0, 255, 255, 0.5);
      border-radius: 12px;
      padding: 25px 15px;
      text-align: center;
      box-shadow: 0 0 20px rgba(0, 191, 255, 0.1);
      transition: all 0.3s ease;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      min-height: 350px;
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 0 25px rgba(0, 255, 255, 0.4);
    }
    .center-content {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      flex-grow: 1;
    }
    .card h3 {
      color: #00ffff;
      font-size: 18px;
      margin-bottom: 6px;
      font-weight: 600;
    }
    .card .details {
      color: #b3e7ff;
      font-size: 14px;
      margin-bottom: 2px;
    }
    .card .date {
      color: #ff7b00;
      font-size: 14px;
      font-weight: 500;
    }
    .card a {
      display: inline-block;
      background: rgba(0, 191, 255, 0.46);
      border: 3px solid #00ffff;
      border-radius: 30px;
      padding: 10px 26px;
      color: #fff;
      font-weight: 600;
      font-size: 14px;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 0 10px rgba(0, 255, 255, 0.25);
    }
    .card a:hover {
      background: rgba(0, 255, 255, 0.3);
      box-shadow: 0 0 15px rgba(0, 255, 255, 0.6);
      transform: scale(1.05);
    }

    .me { color: #00ffff; }

    .sidebar-link.active {
      border-bottom: 2px solid #00ffff;
      color: #00ffff;
    }

    @media (max-width: 900px) {
      .dashboard-container {
        flex-direction: column;
        align-items: center;
      }
      .sidebar {
        flex-direction: row;
        width: 100%;
        justify-content: center;
      }
      .cards-container {
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
      }
    }
  </style>
</head>

<body>

<header>
  <nav>
    <ul>
      <li><img src="img/logo.png" alt=""></li>
      <li><a class="me" href="dashboard.php">Home</a></li>
      <li><a href="courses.php">Courses</a></li>
      <li><a class="me" href="events.php">Events</a></li>
      <li><a href="my_courses.php">My Courses</a></li>
      <li><a href="#"><img src="img/Search.png" alt="Search" class="bar"></a></li>
      <li>|</li>
      <li><a href="panier.php"><img src="img/Group 68.png" alt="Cart" class="bar"></a></li>
      <li><a href="#"><img src="img/Doorbell.png" alt="Notifications" class="bar"></a></li>
      <li><a href="profile.php"><img src="img/User.png" alt="User" class="bar"></a></li>
    </ul>
  </nav>
</header>

<main class="dashboard-container">

  <div class="sidebar">
    <a class="sidebar-link <?= $filter=='all' ? 'active' : '' ?>" href="events.php?filter=all">All</a>
    <a class="sidebar-link <?= $filter=='upcoming' ? 'active' : '' ?>" href="events.php?filter=upcoming">Upcoming</a>
    <a class="sidebar-link <?= $filter=='online' ? 'active' : '' ?>" href="events.php?filter=online">Online</a>
    <a class="sidebar-link <?= $filter=='onsite' ? 'active' : '' ?>" href="events.php?filter=onsite">Onsite</a>
  </div>

  <div class="event-container">
    <h2>
      <?php 
      if($filter=="all") echo "All Events";
      if($filter=="upcoming") echo "Upcoming Events";
      if($filter=="online") echo "Online Events";
      if($filter=="onsite") echo "Onsite Events";
      ?>
    </h2>

    <div class="cards-container">

      <?php if($result->num_rows > 0): ?>
        <?php while($event = $result->fetch_assoc()): ?>
          <div class="card">
            <div class="center-content">
              <h3><?= htmlspecialchars($event['title']); ?></h3>
              <p class="details"><?= htmlspecialchars($event['type'].' Workshop'); ?></p>
              <p class="date"><?= date('j F Y', strtotime($event['start_date'])); ?></p>
            </div>
            <a href="event.php?id=<?= htmlspecialchars($event['id']); ?>">Show more</a>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No events available.</p>
      <?php endif; ?>

    </div>
  </div>

</main>

</body>
</html>

