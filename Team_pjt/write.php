<?php
session_start();
include 'db_connect.php';

// 로그인 안 했으면 로그인 페이지로 쫓아냄
if (!isset($_SESSION['username'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='login.php';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author = $_SESSION['username']; // 현재 로그인한 사람

    $sql = "INSERT INTO posts (title, content, author) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $title, $content, $author);
    
    if ($stmt->execute()) {
        header("Location: board.php");
    } else {
        echo "오류 발생: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>글쓰기</title></head>
<body>
    <h2>✏️ 글쓰기</h2>
    <form method="post">
        제목: <input type="text" name="title" style="width:300px;" required><br><br>
        내용:<br>
        <textarea name="content" style="width:300px; height:150px;" required></textarea><br><br>
        <button type="submit">등록</button>
    </form>
    <a href="board.php">취소</a>
</body>
</html>