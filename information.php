<?php
session_start();
include "db.php"; // Your database connection

$success = $error = "";

// Make sure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $telegram = trim($_POST['telegram'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $address = $_POST['address'] ?? '';
    $bio = $_POST['bio'] ?? '';

    // Check if username is already taken
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $stmt->bind_param("si", $username, $user_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $error = "This username is already taken. Please choose another.";
    }
    $stmt->close();

    // Handle profile photo upload
    $photoPath = "";
    if (!empty($_FILES['photo']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $photoName = time() . "_" . basename($_FILES['photo']['name']);
        $photoPath = $targetDir . $photoName;
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath)) {
            $error = "Failed to upload photo.";
        }
    }

    // Update user info if no errors
    if (empty($error)) {
        if ($photoPath !== "") {
            $stmt = $conn->prepare("UPDATE users SET firstname=?, lastname=?, username=?, gender=?, telegram=?, address=?, bio=?, profile_picture=? WHERE id=?");
            $stmt->bind_param("sssssssi", $firstName, $lastName, $username, $gender, $telegram, $address, $bio, $photoPath, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET firstname=?, lastname=?, username=?, gender=?, telegram=?, address=?, bio=? WHERE id=?");
            $stmt->bind_param("ssssssi", $firstName, $lastName, $username, $gender, $telegram, $address, $bio, $user_id);
        }

        if ($stmt->execute()) {
            $success = "Profile updated successfully!";
            echo "<script>window.location.href='dashboard.php';</script>";
            exit();
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TrainUp | Information</title>
<link rel="icon" href="img/Group 100.png" type="image/png">
<link rel="stylesheet" href="css/style.css">
<style>
h1 { text-align:center; color:#e0f7fa; margin-bottom:30px; font-size:2rem; }
h1 span { color:#00e5ff; }
.form-box { width:60%; background: rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.25); border-radius:12px; box-shadow:0 0 25px rgba(0,217,255,0.2); backdrop-filter:blur(10px); padding:40px 50px; margin:auto; margin-bottom:50px; }
.form-box table { width:100%; border-collapse:collapse; }
.form-box td { padding:12px 10px; vertical-align:middle; }
.form-box label { display:block; font-size:14px; color:#cce7ff; margin-bottom:6px; }
.form-box input, .form-box select, .form-box textarea { width:100%; padding:10px 12px; border:1px solid #00bfff; border-radius:8px; background:transparent; color:#fff; font-size:14px; outline:none; transition:0.3s; }
.form-box input:focus, .form-box select:focus, .form-box textarea:focus { border-color:#00e5ff; box-shadow:0 0 6px #00e5ff; }
.photo-preview { display:block; margin-top:10px; max-width:100px; height:100px; border-radius:50%; object-fit:cover; border:2px solid #00d5ff; background-color: rgba(255,255,255,0.05);}
.submit-row { text-align:center; padding-top:25px; }
.button { width:100%; padding:12px; background:#00d5ff; color:#001F54; font-weight:600; border:none; border-radius:20px; cursor:pointer; font-size:16px; transition:0.3s; text-decoration:none; display:inline-block; }
.button:hover { background:#00a9cc; transform:scale(1.05); }
@media (max-width:480px) { .form-box { width:90%; padding:25px 20px; } h1 { font-size:1.6rem; } .form-box td { display:block; width:100%; padding:8px 0; } .form-box label { font-size:13px; } .form-box input, .form-box select, .form-box textarea { font-size:13px; padding:8px 10px; } .submit-row { padding-top:15px; } .photo-preview { max-width:80px; height:80px; } }
.form-box select {
    background: transparent;
    color: #838384; /* Gray text for selected value */
    border: 1px solid #00bfff;
    border-radius: 8px;
    padding: 10px 12px;
    outline: none;
    font-size: 14px;
    transition: 0.3s;
    appearance: none;
    cursor: pointer;
}

/* Dropdown options */
.form-box select option {
    background: #001F54; /* Dark background for options */
    color: #838384;      /* Gray text for options */
}

/* Optional: make placeholder text gray */
.form-box select option[value=""] {
    color: #838384;
}

</style>
</head>
<body>

<div style="display:flex; align-items:center; justify-content:space-between; margin:40px 5%; flex-wrap:wrap;">
  <h1 style="flex:1; text-align:center; color:#e0f7fa; margin:0; font-size:2rem;">
    <span>Welcome</span> — Enter Your Information
  </h1>
  <a href="dashboard.php" style="background:#00d5ff; color:#001F54; padding:12px 25px; border-radius:25px; text-decoration:none; font-weight:600; transition:0.3s; white-space:nowrap; margin-left:20px;">
    Skip
  </a>
</div>

<div class="form-box">
<?php if($success) echo "<p style='color:lightgreen;'>$success</p>"; ?>
<?php if($error) echo "<p style='color:red;'>$error</p>"; ?>
<form action="#" method="post" enctype="multipart/form-data">
<table>
<tr>
  <td><label for="photo">Profile Photo</label></td>
  <td>
    <img id="photoPreview" class="photo-preview" src="" alt="">
    <input type="file" id="photo" name="photo" accept="image/*" onchange="previewPhoto(event)">
  </td>
</tr>
<tr>
<td><label for="firstName">First Name</label></td>
<td><input type="text" id="firstName" name="firstName" placeholder="Enter your first name" required></td>
</tr>
<tr>
<td><label for="lastName">Last Name</label></td>
<td><input type="text" id="lastName" name="lastName" placeholder="Enter your last name" required></td>
</tr>
<tr>
<td><label for="username">Username</label></td>
<td><input type="text" id="username" name="username" placeholder="Enter a username" required></td>
</tr>
<tr>
<td><label for="gender">Gender</label></td>
<td>
<select id="gender" name="gender" required>
<option value="">-- Select Gender --</option>
<option value="male">Male</option>
<option value="female">Female</option>
</select>
</td>
</tr>
<tr>
<td><label for="address">Address</label></td>
<td><textarea id="address" name="address" rows="3" placeholder="Enter your address"></textarea></td>
</tr>
<tr>
<td><label for="bio">Enter your bio</label></td>
<td><textarea id="bio" name="bio" rows="4" placeholder="Write a short bio about yourself..."></textarea></td>
</tr>
<tr>
  <td><label for="telegram">Telegram</label></td>
  <td>
    <input 
      type="text" 
      id="telegram" 
      name="telegram" 
      placeholder="@username ">
  </td>
</tr>
<tr>
<td colspan="2" class="submit-row">
<button type="submit" class="button">Sign Up</button>
</td>
</tr>
</table>
</form>
</div>

<script>
function previewPhoto(event) {
    const reader = new FileReader();
    reader.onload = function(){
        document.getElementById('photoPreview').src = reader.result;
    };
    if(event.target.files[0]) reader.readAsDataURL(event.target.files[0]);
}
</script>

</body>
</html>
