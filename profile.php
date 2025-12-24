<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) die("User not found");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TrainUp | Profile</title>
  <link rel="icon" href="img/Group 100.png" type="image/png">
<link rel="stylesheet" href="css/style.css">
<style>
h1 { color:#e0f7fa; text-align:center; margin:40px 0; }
h1 span { color:#00ffff; }

.profile-container, .edit-profile {
    background:rgba(255,255,255,.08);
    backdrop-filter:blur(10px);
    border-radius:15px;
    border:1px solid rgba(255,255,255,.25);
    box-shadow:0 0 25px rgba(75,77,77,.2);
    padding:40px 50px;
    margin:auto;
    margin-bottom:50px;
    width:85%;
    max-width:900px;
}

.profile-header { display:flex; align-items:center; gap:30px; border-bottom:1px solid rgba(255,255,255,.2); padding-bottom:25px; margin-bottom:25px; flex-wrap:wrap; }
.photo-preview { width:150px; height:150px; border-radius:50%; border:4px solid #00ffff; object-fit:cover; }
.user-info h2{ color:#00ffff; margin:0; }
.user-info p{ color:#e0f7fa; margin:6px 0; }
table{ width:100%; border-collapse:collapse; }
td{ padding:12px 10px; vertical-align:top; }
.label{ font-weight:600; color:#00ffff; width:180px; text-align:right; }
.value{ color:#e0f7fa; }
.actions{text-align:center;margin-top:35px;}
.actions a, button{ background:#00ffff; color:#ffffff; border:none; border-radius:25px; padding:10px 25px; font-weight:600; cursor:pointer; text-decoration:none; margin:0 10px; }
.edit-profile input, .edit-profile textarea{ width:90%; background:#d6d7d7; padding:8px; border-radius:6px; border:1px solid #ccc; }

@media(max-width:700px){ .profile-header{ flex-direction:column; } .label{text-align:left;} }
</style>
</head>
<body>
    <header>
    <nav>
      <ul>
        <li><img src="img/logo.png" alt=""></li>
        <li><a class="me" href="dashboard.php" >Home</a></li>
        <li><a href="courses.php">Courses</a></li>
        <li><a href="events.php">Events</a></li>
        <li><a href="my_courses.php">My Courses</a></li>
        <li><a href="#"><img src="img/Search.png" class="bar" alt=""></a></li>
        <li>|</li>
        <li><a href="panier.php"><img src="img/Group 68.png" class="bar" alt=""></a></li>
        <li><a href="#"><img src="img/Doorbell.png" class="bar" alt=""></a></li>
        <li><a href="profile.php "><img src="img/User.png" class="bar" alt=""></a></li>
      </ul>
    </nav>
  </header>

<h1><span>User</span> Profile</h1>

<!-- MAIN PROFILE -->
<div id="main-profile" class="profile-container">
  <div class="profile-header">
    <img src="<?= $user['profile_picture'] ? $user['profile_picture'] : 'default.png' ?>" class="photo-preview">
    <div class="user-info">
      <h2><?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></h2>
      <p><?= htmlspecialchars($user['email']); ?></p>
      <p><?= htmlspecialchars($user['address']); ?></p>
    </div>
  </div>

  <table>
    <tr><td class="label">Username</td><td class="value"><?= htmlspecialchars($user['username']); ?></td></tr>
    <tr><td class="label">Phone</td><td class="value"><?= htmlspecialchars($user['phone_number']); ?></td></tr>
    <tr><td class="label">Gender</td><td class="value"><?= htmlspecialchars($user['gender']); ?></td></tr>
    <tr><td class="label">Telegram</td><td class="value"><?= htmlspecialchars($user['telegram']); ?></td></tr>
    <tr><td class="label">Bio</td><td class="value"><?= htmlspecialchars($user['bio']); ?></td></tr>
  </table>

  <div class="actions">
    <a href="#" id="editBtn">Edit Profile</a>
  </div>
</div>

<!-- EDIT FORM -->
<div id="edit-profile" class="edit-profile" style="display:none;">
<form action="update_profile.php" method="POST" enctype="multipart/form-data">
<table>
<tr>
  <td class="label">Profile Picture</td>
  <td>
    <img id="photoPreview" src="<?= $user['profile_picture'] ? $user['profile_picture'] : 'default.png' ?>" class="photo-preview"><br>
    <input type="file" name="profile_picture" onchange="previewPhoto(event)">
  </td>
</tr>
<tr><td class="label">First Name</td><td><input type="text" name="firstname" value="<?= htmlspecialchars($user['firstname']); ?>" required></td></tr>
<tr><td class="label">Last Name</td><td><input type="text" name="lastname" value="<?= htmlspecialchars($user['lastname']); ?>" required></td></tr>
<tr><td class="label">Phone</td><td><input type="text" name="phone_number" value="<?= htmlspecialchars($user['phone_number']); ?>"></td></tr>
<tr><td class="label">Address</td><td><textarea name="address"><?= htmlspecialchars($user['address']); ?></textarea></td></tr>
<tr><td class="label">Telegram</td><td><input type="text" name="telegram" value="<?= htmlspecialchars($user['telegram']); ?>"></td></tr>
<tr><td class="label">Bio</td><td><textarea name="bio"><?= htmlspecialchars($user['bio']); ?></textarea></td></tr>
<tr>
  <td colspan="2" style="text-align:center;">
    <button type="submit">Save Changes</button>
    <button type="button" id="cancelBtn" style="background:#ff5555;">Cancel</button>
  </td>
</tr>
</table>
</form>
</div>

<script>
// Show edit form & hide main profile
document.getElementById('editBtn').onclick = function(e){
  e.preventDefault();
  document.getElementById('main-profile').style.display = 'none';
  document.getElementById('edit-profile').style.display = 'block';
}

// Cancel button: hide form & show main profile
document.getElementById('cancelBtn').onclick = function(){
  document.getElementById('edit-profile').style.display = 'none';
  document.getElementById('main-profile').style.display = 'block';
}

// Profile picture live preview
function previewPhoto(event){
    const reader = new FileReader();
    reader.onload = function(){
        document.getElementById('photoPreview').src = reader.result;
    }
    if(event.target.files[0]) reader.readAsDataURL(event.target.files[0]);
}
</script>

</body>
</html>
