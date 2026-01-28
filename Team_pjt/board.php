<?php
session_start();
include 'db_connect.php';

// 게시글 가져오기 (최신순)
$sql = "SELECT * FROM posts ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head><title>게시판</title></head>
<body>
    <h2>📌 자유 게시판</h2>
    <?php if(isset($_SESSION['username'])): ?>
        <button onclick="location.href='write.php'">글쓰기</button>
    <?php else: ?>
        <p>글을 쓰려면 <a href="login.php">로그인</a>하세요.</p>
    <?php endif; ?>
    <hr>
    
    <table border="1" width="100%" style="border-collapse: collapse;">
        <tr style="background-color: #eee;">
            <th>번호</th>
            <th>제목</th>
            <th>작성자</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td style="text-align:center;"><?php echo $row['id']; ?></td>
            <td style="padding: 5px;"><?php echo htmlspecialchars($row['title']); ?></td>
            <td style="text-align:center;"><?php echo htmlspecialchars($row['author']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    
    <br>
    <a href="index.php">홈으로 돌아가기</a>
</body>
</html>