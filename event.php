<?php
session_start();
require 'db.php';
require 'phpqrcode/qrlib.php';

// Check login
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if(!$event_id){
    die("Event not found.");
}

// Fetch event info
$stmt = $conn->prepare("SELECT * FROM events WHERE id=?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    die("Event not found.");
}

$event = $result->fetch_assoc();

// Handle registration
$qr_file = null;
$reg_message = '';

if(isset($_POST['register'])){
    // Check if already registered
    $stmt2 = $conn->prepare("SELECT qr_code FROM event_registrations WHERE user_id=? AND event_id=?");
    $stmt2->bind_param("ii", $user_id, $event_id);
    $stmt2->execute();
    $res2 = $stmt2->get_result();

    if($res2->num_rows > 0){
        $row = $res2->fetch_assoc();
        $qr_file = $row['qr_code'];
        $reg_message = "You are already registered!";
    } else {
        // Generate QR
        $code = $user_id . '-' . $event_id . '-' . time();
        $path = "qrcodes/";
        if(!file_exists($path)) mkdir($path, 0777, true);

        $qr_file = $path . $code . ".png";
        QRcode::png($code, $qr_file, QR_ECLEVEL_L, 6);

        // Save registration
        $stmt3 = $conn->prepare("INSERT INTO event_registrations(user_id,event_id,qr_code) VALUES(?,?,?)");
        $stmt3->bind_param("iis", $user_id, $event_id, $qr_file);
        $stmt3->execute();

        $reg_message = "Registration successful!";
    }
}

// Decode modules JSON
$modules = json_decode($event['modules_json'], true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>TrainUp | Event</title>
<link rel="stylesheet" href="css/style.css" />
<link rel="stylesheet" href="css/dash.css" />
<style>
#qrPopup {
    display: <?= $qr_file ? 'flex' : 'none' ?>;
    position: fixed;
    top:0; left:0;
    width:100%; height:100%;
    background: rgba(0,0,0,0.6);
    justify-content:center;
    align-items:center;
    z-index:9999;
}
#qrPopup .popup-content {
    background:#fff;
    padding:20px;
    border-radius:10px;
    text-align:center;
    max-width:300px;
}
.register-btn{
    cursor:pointer;
    padding:10px 20px;
    border:none;
    background:#00bfff;
    color:#fff;
    border-radius:5px;
    font-weight:600;
}
.modules label{
    display:block;
    border:1px solid #00ffff;
    border-radius:8px;
    margin-bottom:10px;
    padding:10px;
}
.modules .header{
    display:flex;
    justify-content:space-between;
    cursor:pointer;
}
.modules input[type=radio]{display:none;}
.modules .content{display:none; margin-top:5px;}
.modules input[type=radio]:checked + .header + .content{display:block;}
</style>
</head>
<body>
<header>
    <nav>
        <ul>
            <li><img src="img/logo.png" alt=""></li>
            <li><a href="dashboard.html">Home</a></li>
            <li><a href="courses.html">Courses</a></li>
            <li><a href="events.html">Events</a></li>
        </ul>
    </nav>
</header>

<main class="dashboard-container">
    <div class="sidebar">
      <a class="sidebar-link">All</a>
      <a class="sidebar-link">Upcoming</a>
      <a class="sidebar-link active">Online</a>
      <a class="sidebar-link">Onsite</a>
    </div>

    <div class="event-container">
      <div class="events-grid">
        <h3>Workshop: <span><?= htmlspecialchars($event['title']) ?></span></h3>
        <p><?= htmlspecialchars($event['description']) ?></p>

        <h4>Modules</h4>
        <div class="modules">
            <?php if($modules): ?>
                <?php foreach($modules as $mod): ?>
                <label class="module">
                    <input type="radio" name="accordion" />
                    <div class="header">
                        <span><?= htmlspecialchars($mod['title']) ?></span>
                    </div>
                    <div class="content">
                        <p><?= htmlspecialchars($mod['content']) ?></p>
                    </div>
                </label>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No modules available.</p>
            <?php endif; ?>
        </div>

        <div class="info-grid">
          <div><strong>Status:</strong><span class="tag"><?= htmlspecialchars($event['status']) ?></span></div>
          <div><strong>Type:</strong><span class="tag"><?= htmlspecialchars($event['type']) ?></span></div>
          <div><strong>Duration:</strong><span class="tag"><?= htmlspecialchars($event['duration']) ?></span></div>
          <div><strong>Level:</strong><span class="tag"><?= htmlspecialchars($event['level']) ?></span></div>
        </div>

        <div class="coach-card">
          <h5>Coaches</h5>
          <div class="coach">
            <div class="avatar"></div>
            <div>
              <p class="name"><?= htmlspecialchars($event['coach_name']) ?></p>
              <p class="role"><?= htmlspecialchars($event['coach_role']) ?></p>
            </div>
          </div>
        </div>

        <!-- Registration Form -->
        <form method="post">
            <button type="submit" name="register" class="register-btn">Register</button>
        </form>

        <!-- QR Popup -->
        <div id="qrPopup">
          <div class="popup-content">
            <img src="<?= $qr_file ?>" alt="Event QR Code">
            <p><?= htmlspecialchars($reg_message) ?></p>
            <a href="#" class="close-btn">Close</a>
          </div>
        </div>
      </div>
    </div>
</main>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const popup = document.getElementById('qrPopup');
    const closeBtn = popup.querySelector('.close-btn');

    closeBtn.addEventListener('click', function(e){
        e.preventDefault();
        popup.style.display = 'none';
    });
});
</script>
</body>
</html>
