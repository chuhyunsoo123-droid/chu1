<?php
session_start();
include 'db_connect.php';

// 이미 로그인했으면 게시판으로 보냄
if (isset($_SESSION['user_id'])) {
    header("Location: board.php");
    exit;
}

$error_msg = "";

// 로그인 요청이 들어오면 여기서 바로 처리
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // SQL 인젝션 방지 처리
    $sql = "SELECT id, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // 비밀번호 확인
        if (password_verify($password, $row['password'])) {
            // [중요] 세션 변수 2개 다 저장 (버그 해결)
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $username;
            
            header("Location: board.php");
            exit;
        } else {
            $error_msg = "비밀번호가 틀렸습니다.";
        }
    } else {
        $error_msg = "존재하지 않는 아이디입니다.";
    }
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
        .login-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .brand-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #333;
            margin-bottom: 30px;
        }
        .form-control {
            height: 50px;
            margin-bottom: 15px;
        }
        .btn-login {
            width: 100%;
            height: 50px;
            font-size: 1.1rem;
            font-weight: bold;
        }
        .alert-custom {
            font-size: 0.9rem;
            padding: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="brand-title">Why Works?</div>
        
        <?php if($error_msg): ?>
            <div class="alert alert-danger alert-custom" role="alert">
                <?= $error_msg ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="text" name="username" class="form-control" placeholder="아이디" required>
            <input type="password" name="password" class="form-control" placeholder="비밀번호" required>
            
            <button type="submit" class="btn btn-primary btn-login mb-3">로그인</button>
            <a href="register.php" class="btn btn-outline-secondary w-100">회원가입</a>
        </form>
    </div>

</body>
</html>