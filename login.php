<?php
// login.php
session_start();
require 'db.php';
require 'functions.php';

// If user is already logged in, go to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$row['id'];
            $_SESSION['username'] = $username;
            header('Location: dashboard.php'); // ✅ Correct redirect here
            exit;
        }
    }

    $error = "Invalid username or password.";
    $stmt->close();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login - simple_app</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    body {
      margin: 0;
      font-family: "Segoe UI", Roboto, Arial, sans-serif;
      background: linear-gradient(135deg, #2b7cff, #6fb1fc);
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      color: #333;
    }

    .login-container {
      background: #fff;
      width: 90%;
      max-width: 420px;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    h2 {
      color: #2b7cff;
      text-align: center;
      margin-bottom: 25px;
    }

    label {
      display: block;
      margin-bottom: 10px;
      font-weight: 600;
    }

    input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 6px;
      margin-bottom: 15px;
      font-size: 15px;
    }

    button {
      width: 100%;
      background: #2b7cff;
      color: #fff;
      border: none;
      padding: 10px;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      transition: 0.2s ease;
    }

    button:hover {
      background: #195ed6;
      transform: translateY(-2px);
    }

    .error {
      background: #ffe8e8;
      color: #900;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 15px;
      text-align: center;
    }

    p {
      text-align: center;
      margin-top: 15px;
    }

    a {
      color: #2b7cff;
      text-decoration: none;
      font-weight: 500;
    }

    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>Login</h2>

    <?php if (!empty($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
      <label>Username</label>
      <input name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>

      <label>Password</label>
      <input name="password" type="password" required>

      <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a></p>
    <p><a href="items.php">View Public Items</a></p>
  </div>
</body>
</html>