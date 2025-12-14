<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>TrainUp | Sign In / Sign Up</title>
  <link rel="icon" href="img/Group 100.png" type="image/png">
<link rel="stylesheet" href="css/style.css" />

<style>

main {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: space-around;
  padding: 50px 100px;
}

.welcome {
  max-width: 400px;
}

.welcome h1 {
  font-size: 2rem;
  margin-bottom: 20px;
}

.welcome p {
  line-height: 1.6;
  margin-bottom: 10px;
}

.signin-box {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(0, 217, 255, 0.3);
  border-radius: 10px;
  padding: 40px;
  width: 330px;
  box-shadow: 0 0 20px rgba(0, 217, 255, 0.2);
}

.signin-box label {
  display: block;
  font-size: 0.9rem;
  margin-bottom: 6px;
}

.signin-box input[type="email"],
.signin-box input[type="password"] {
  width: 100%;
  background: transparent;
  border: 1px solid #00b7e6;
  border-radius: 6px;
  padding: 10px 12px;
  color: #fff;
  margin-bottom: 18px;
  outline: none;
  font-size: 0.9rem;
}


.options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 10px 0 15px;
}

.remember {
  display: flex;
  align-items: center;
}

.remember input[type="checkbox"] {
  margin-right: 6px;
  accent-color: #00d9ff;
  width: 16px;
  height: 16px;
  cursor: pointer;
}

.remember label {
  font-size: 14px;
  color: #cce7ff;
  cursor: pointer;
}

.forgot a {
  font-size: 14px;
  color: #00d5ff;
  text-decoration: none;
  transition: 0.3s;
}

.forgot a:hover {
  text-decoration: underline;
}


.social {
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
  font-size: 0.9rem;
}

.social a {
  text-decoration: none;
  color: #fff;
  transition: color 0.3s;
}

.social a:hover {
  color: #00d9ff;
}

.signin-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  background: #00d9ff;
  border: none;
  color: #fff;
  font-weight: 700;
  padding: 12px;
  border-radius: 20px;
  text-decoration: none;
  font-size: 1rem;
  cursor: pointer;
  margin-bottom: 15px;
  transition: 0.3s;
}

.signin-btn:hover {
  background: #00b7e6;
}


.form-box {
  flex: 1;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 10px;
  backdrop-filter: blur(10px);
  padding: 30px;
  box-shadow: 0 0 20px rgba(0, 255, 255, 0.15);
  max-width: 50%;
  margin: 50px auto;
}

.form-box label {
  display: block;
  font-size: 14px;
  margin: 10px 0 5px;
}

.form-box input {
  width: 100%;
  padding: 10px 12px;
  border-radius: 8px;
  border: 1px solid #00bfff;
  background: transparent;
  color: #fff;
  font-size: 14px;
  outline: none;
  transition: 0.3s;
}

.form-box input:focus {
  border-color: #00e5ff;
  box-shadow: 0 0 5px #00e5ff;
}

.social-login {
  margin-top: 15px;
  text-align: center;
  font-size: 14px;
}

.social-login span {
  margin: 0 10px;
  cursor: pointer;
  transition: 0.3s;
}

.social-login span:hover {
  color: #00ffff;
}

.form-box button,
.signin-btn {
  width: 100%;
  padding: 12px;
  margin-top: 20px;
  background: #00d5ff;
  color: #001f54;
  font-weight: 600;
  border: none;
  border-radius: 20px;
  cursor: pointer;
  font-size: 16px;
  transition: 0.3s;
}

.form-box button:hover {
  background: #00a9cc;
}

.switch-link {
  text-align: center;
  margin-top: 15px;
}

.switch-link a {
  color: #00d5ff;
  text-decoration: none;
  font-weight: 600;
  transition: 0.3s;
}

.switch-link a:hover {
  text-decoration: underline;
}


/* Medium screens (≤1024px) */
@media (max-width: 1024px) {
  .signin-box,
  .form-box {
    max-width: 380px;
    padding: 35px;
  }

  .signin-box input,
  .form-box input {
    width: 90%;
  }

  .signin-btn,
  .form-box button {
    width: 90%;
  }
}

/* Tablets (≤768px) */
@media (max-width: 768px) {
  main {
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
  }

  .welcome {
    text-align: center;
    max-width: 400px;
  }

  .signin-box,
  .form-box {
    max-width: 320px;
    padding: 30px;
  }

  .signin-box input,
  .form-box input {
    width: 85%;
  }

  .signin-btn,
  .form-box button {
    width: 85%;
  }
}

/* Small Phones (≤480px) */
@media (max-width: 480px) {
  main {
    flex-direction: column;
    padding: 20px 10px;
    text-align: center;
  }

  .welcome {
    max-width: 100%;
    margin-bottom: 20px;
  }

  .signin-box,
  .form-box {
    width: 90%;
    max-width: 300px;
    padding: 20px;
    margin: 0 auto;
  }

  .signin-box input,
  .form-box input {
    width: 100%;
    font-size: 0.85rem;
    padding: 9px 10px;
  }

  .signin-btn,
  .form-box button {
    width: 100%;
    font-size: 0.9rem;
    padding: 10px;
  }

  .options {
    flex-direction: column;
    gap: 10px;
    text-align: center;
  }

  .social {
    flex-direction: column;
    gap: 10px;
  }

  .switch-link {
    font-size: 0.9rem;
  }
}
</style>
</head>

<body>
  <header>
    <nav>
      <ul>
        <li><img src="img/logo.png" alt=""></li>
        <li><a href="index.html">Home</a></li>
        <li><a href="index.html">Courses</a></li>
        <li><a href="aboutas.html">About Us</a></li>
        <li><a href="aboutas.html">Contact</a></li>
        <li><a href="login.html" class="button" id="headerSignIn">Sign In</a></li>
      </ul>
    </nav>
  </header>

  <!-- Sign In -->
  <main class="signin active">
    <div class="welcome" >
      <h1><span>Welcome</span> Back</h1>
      <p><strong>The best learners never stop.</strong></p>
      <p>Ready to learn something new today?</p>
    </div>

    <div class="signin-box" >
      <form action="signin.php" method="POST">
        <label>Email Address</label>
        <input type="email" name="email" placeholder="Email" required />

        <label>Password</label>
        <input type="password" name="password" placeholder="Password" required />

        <div class="options">
          <div class="remember">
            <input type="checkbox" id="remember" />
            <label for="remember">Remember Me</label>
          </div>
          <div class="forgot">
            <a href="#">Forgot Password?</a>
          </div>
        </div>

        <div class="social">
          <a href="#">Google</a>
        </div>
        <button type="submit" class="signin-btn">Sign In</button>

        <div class="switch-link">
          Don't have an account? <a href="#" id="toSignup">Sign Up</a>
        </div>
      </form>
    </div>
  </main>

  <!-- Sign Up -->
  <div class="container signup" >
    <div class="form-box">
<form action="signup.php" method="POST">
    <label>First Name</label>
    <input type="text" name="firstname" placeholder="First Name" required />

    <label>Last Name</label>
    <input type="text" name="lastname" placeholder="Last Name" required />

    <label>Username</label>
    <input type="text" name="username" placeholder="Username" required />

    <label>Email Address</label>
    <input type="email" name="email" placeholder="Email" required />

    <label>Phone Number</label>
    <input type="text" name="phoneNumber" placeholder="Phone Number" />

    <label>Password</label>
    <input type="password" name="password" placeholder="Password" required />

    <label>Confirm Password</label>
    <input type="password" name="confirm_password" placeholder="Confirm Password" required />

    <button type="submit" class="signin-btn">Sign Up</button>
</form>


    </div>
  </div>
</body>
</html>


