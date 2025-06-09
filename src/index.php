<?php
session_start();
require_once 'config/db.php';

// Auto-login using remember_token if session is not set
if (!isset($_SESSION['email']) && isset($_COOKIE['remember_token'])) {
  $token = $_COOKIE['remember_token'];

  $stmt = $db->prepare("SELECT user_email FROM remember_tokens WHERE token = ? AND expiry > NOW()");
  $stmt->bind_param("s", $token);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();

  if ($row) {
    $_SESSION['email'] = $row['user_email'];
    header("Location: dashboard.php");
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Log-in</title>
  <link rel="stylesheet" href="css/index.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Gilda+Display&family=Montserrat:wght@400;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" href="img/logo.jpg" type="image/x-icon">
</head>

<body>
  <div class="container">
    <div class="box-shadow-decoration">
      <div class="left-side">
        <div class="logo">
          <img src="img/pennywise-logo.jpg" alt="logo">
        </div>
      </div>

      <form action="login.php" method="post">
        <div class="form-side">
          <div class="login-box">
            <label for="Email"><span class="red-text">*</span>Email</label>
            <input type="email" name="email" id="Email" placeholder="Enter your email" required value="<?php echo htmlspecialchars($_COOKIE['email'] ?? '', ENT_QUOTES); ?>">

            <label for="Password"><span class="red-text">*</span>Password</label>
            <input type="password" name="password" id="Password" placeholder="Enter your password" required>

            <div class="actions">
              <label><input type="checkbox" id="Remember" name="remember" <?php if (isset($_COOKIE['email'])) echo 'checked'; ?>> Remember me</label>
              <a href="forgot-password.php">Forgot password?</a>
            </div>

            <button type="submit" name="login">SIGN IN</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</body>

</html>