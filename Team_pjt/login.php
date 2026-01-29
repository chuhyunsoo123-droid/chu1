<?php
session_start();
include 'db_connect.php';

$error_msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT id, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            header("Location: index.php"); // 성공 시 즉시 이동
            exit;
        } else { $error_msg = "비밀번호 틀림"; }
    } else { $error_msg = "없는 아이디"; }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>LOGIN</title>
    <style>
        body { background-color: #ffffff; color: #000000; font-family: 'Arial', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .container { border: 2px solid #000; padding: 40px; width: 300px; text-align: center; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #000; box-sizing: border-box; outline: none; }
        button { width: 100%; padding: 10px; background: #000; color: #fff; border: none; cursor: pointer; font-weight: bold; margin-top: 10px; }
        button:hover { background: #333; }
        a { color: #000; text-decoration: none; font-size: 0.9em; margin-top: 15px; display: inline-block; border-bottom: 1px solid #000; }
        .error { color: red; font-size: 0.8em; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>LOGIN</h2>
        <?php if($error_msg): ?>
            <div class="error"><?php echo $error_msg; ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="text" name="username" placeholder="ID" required>
            <input type="password" name="password" placeholder="PASSWORD" required>
            <button type="submit">LOGIN</button>
        </form>
        <a href="register.php">CREATE ACCOUNT</a><br>
        <a href="index.php">BACK TO HOME</a>
    </div>
</body>
</html>