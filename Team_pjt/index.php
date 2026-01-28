<?php
session_start();
include 'db_connect.php';

// 대문 공지사항 가져오기
$sql = "SELECT content FROM notices ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);
$notice = ($result->num_rows > 0) ? $result->fetch_assoc()['content'] : "공지사항이 없습니다.";
?>
<!DOCTYPE html>
<html>
<head><title>PHP 웹사이트</title></head>
<body>
    <h1>환영합니다! (PHP Version)</h1>
    <div style="border:1px solid #ddd; padding:10px; background:#f9f9f9;">
        <h3>📢 공지사항</h3>
        <p><?php echo htmlspecialchars($notice); ?></p>
    </div>
    <hr>
    <?php if(isset($_SESSION['username'])): ?>
        <p>안녕하세요, <b><?php echo $_SESSION['username']; ?></b>님!</p>
        <a href="board.php">게시판 가기</a> | 
        <a href="llm.php">AI 채팅</a> | 
        <a href="logout.php">로그아웃</a>
    <?php else: ?>
        <a href="login.php">로그인</a> | <a href="register.php">회원가입</a>
    <?php endif; ?>
</body>
</html>