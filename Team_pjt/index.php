<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: board.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Why Works? - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .brand-title {
            font-size: 3rem; /* 글자 크기 키움 */
            font-weight: 800; /* 두께 두껍게 */
            color: #333;
            margin-bottom: 30px;
            letter-spacing: -1px;
        }
        .form-control {
            height: 50px;
            margin-bottom: 20px;
        }
        .btn-custom {
            width: 100%;
            height: 50px;
            font-size: 1.1rem;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="brand-title">Why Works?</div>
        
        <form action="login.php" method="POST">
            <input type="text" name="username" class="form-control" placeholder="아이디" required>
            <input type="password" name="password" class="form-control" placeholder="비밀번호" required>
            
            <button type="submit" class="btn btn-primary btn-custom">로그인</button>
            <a href="register.php" class="btn btn-outline-secondary btn-custom">회원가입</a>
        </form>
    </div>

</body>
</html>