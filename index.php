<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Welcome - simple_app</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    body {
      margin: 0;
      padding: 0;
      height: 100vh;
      font-family: "Segoe UI", Roboto, Arial, sans-serif;
      background: linear-gradient(135deg, #2b7cff, #6fb1fc);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #333;
    }
    .welcome {
      background: #fff;
      padding: 40px 50px;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
      text-align: center;
      max-width: 420px;
      width: 100%;
    }
    .welcome h1 {
      color: #2b7cff;
      margin-bottom: 10px;
    }
    .welcome p {
      color: #555;
      margin-bottom: 30px;
    }
    .btns a {
      display: inline-block;
      background: #2b7cff;
      color: #fff;
      padding: 10px 20px;
      border-radius: 6px;
      margin: 5px;
      text-decoration: none;
      font-weight: 500;
      transition: 0.2s ease;
    }
    .btns a:hover {
      background: #195ed6;
      transform: translateY(-2px);
    }
  </style>
</head>
<body>
  <div class="welcome">
    <h1>Welcome to simple_app</h1>
    <p>A mini PHP & MySQL website with user login and CRUD functionality.</p>
    <div class="btns">
      <a href="register.php">Register</a>
      <a href="login.php">Login</a>
      <a href="items.php">View Items</a>
    </div>
  </div>
</body>
</html>
