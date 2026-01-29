<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT id, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // 보안상 해시를 써야 하지만, 테스트용으로 평문 비교 혹은 password_verify 사용
        // 여기선 password_verify 권장
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            header("Location: index.php");
        } else { echo "비밀번호 틀림"; }
    } else { echo "없는 아이디"; }
}
?>
<form method="post">
    ID: <input type="text" name="username"><br>
    PW: <input type="password" name="password"><br>
    <button type="submit">로그인</button>
</form>