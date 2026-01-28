<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 비밀번호 암호화 (보안 필수)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 중복 아이디 체크 및 가입
    $check_sql = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        echo "<script>alert('이미 존재하는 아이디입니다.');</script>";
    } else {
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $hashed_password);
        
        if ($stmt->execute()) {
            echo "<script>alert('가입 성공! 로그인해주세요.'); location.href='login.php';</script>";
        } else {
            echo "가입 실패: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>회원가입</title></head>
<body>
    <h2>📝 회원가입</h2>
    <form method="post">
        ID: <input type="text" name="username" required><br><br>
        PW: <input type="password" name="password" required><br><br>
        <button type="submit">가입하기</button>
    </form>
    <a href="index.php">홈으로</a>
</body>
</html>