<?php
session_start();
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>PHP 웹사이트</title>
    <style>
        body { background-color: #ffffff; color: #000000; font-family: 'Arial', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .container { border: 2px solid #000; padding: 40px; width: 300px; text-align: center; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #000; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #000; color: #fff; border: none; cursor: pointer; font-weight: bold; }
        button:hover { background: #333; }
        a { color: #000; text-decoration: none; font-size: 0.9em; margin-top: 15px; display: inline-block; border-bottom: 1px solid #000; }
        .notice-box { border: 1px solid #000; padding: 15px; margin-bottom: 20px; text-align: left; }
    </style>
</head>
<body>
    <div class="container">
        <?php if(!isset($_SESSION['username'])): ?>
            <h2>LOGIN</h2>
            <form action="login.php" method="post">
                <input type="text" name="username" placeholder="ID" required>
                <input type="password" name="password" placeholder="PASSWORD" required>
                <button type="submit">LOGIN</button>
            </form>
            <a href="register.php">CREATE ACCOUNT</a>
        <?php else: ?>
            <h2>WELCOME</h2>
            <p>Hello, <b><?php echo $_SESSION['username']; ?></b></p>
            <div class="notice-box">
                <small>📢 NOTICE</small>
                <p style="margin: 5px 0 0 0;">새로운 소식이 준비되어 있습니다.</p>
            </div>
            <button onclick="location.href='board.php'">BOARD</button><br><br>
            <button onclick="location.href='llm.php'">AI CHAT</button><br><br>
            <a href="logout.php">LOGOUT</a>
        <?php endif; ?>
    </div>
</body>
</html>