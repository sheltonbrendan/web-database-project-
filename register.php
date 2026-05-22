<?php
session_start();
require 'db.php';
require 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username and password are required.';
    } elseif ($password !== $password2) {
        $error = 'Passwords do not match.';
    } else {
        // Check existing username
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = 'Username already taken.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $ins->bind_param('sss', $username, $email, $hash);
            if ($ins->execute()) {
                $success = "Registration successful. You can now <a href='login.php'>login</a>.";
            } else {
                $error = 'Database error: ' . $ins->error;
            }
            $ins->close();
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Register - simple_app</title>

  <!-- Option 1: External stylesheet (recommended) -->
  <link rel="stylesheet" href="assets/style.css">

  <!-- Option 2: Inline CSS (you can leave this here if you want it standalone) -->
  <style>
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      background: #f5f7fb;
      color: #111;
      margin: 0;
      padding: 0;
    }
    .register-container {
      width: 450px;
      margin: 60px auto;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      padding: 35px 40px;
    }
    h1 {
      text-align: center;
      margin-bottom: 25px;
      color: #2b7cff;
    }
    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }
    label {
      font-weight: 600;
      font-size: 15px;
    }
    input[type="text"], input[type="password"], input[type="email"] {
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-size: 15px;
      width: 100%;
    }
    button {
      background: #2b7cff;
      color: white;
      padding: 10px;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      transition: 0.2s ease;
    }
    button:hover {
      opacity: 0.9;
      transform: translateY(-1px);
    }
    .error {
      background: #ffe8e8;
      color: #900;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 15px;
      text-align: center;
      font-size: 14px;
    }
    .success {
      background: #e6ffea;
      color: #0a8a2f;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 15px;
      text-align: center;
      font-size: 14px;
    }
    p {
      text-align: center;
      font-size: 14px;
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
  <div class="register-container">
    <h1>Create an Account</h1>

    <?php if (!empty($error)): ?>
      <div class="error"><?= esc($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
      <div class="success"><?= $success ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
      <label>Username</label>
      <input name="username" type="text" value="<?= esc($_POST['username'] ?? '') ?>" required>

      <label>Email (optional)</label>
      <input name="email" type="email" value="<?= esc($_POST['email'] ?? '') ?>">

      <label>Password</label>
      <input type="password" name="password" required>

      <label>Confirm Password</label>
      <input type="password" name="password2" required>

      <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login</a></p>
    <p><a href="items.php">View Public Items</a></p>
  </div>
</body>
</html>
