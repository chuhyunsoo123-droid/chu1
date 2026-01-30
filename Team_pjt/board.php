<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// 게시글 목록 가져오기
$sql = "SELECT * FROM posts ORDER BY created_at DESC";
$stmt = $pdo->query($sql);
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>Why Works? - Board</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .header-title {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
            text-align: center;
            margin: 30px 0;
        }
        .container { max-width: 900px; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="header-title">Why Works?</div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>자유 게시판</h4>
            <div>
                <a href="llm.php" class="btn btn-success me-2">🤖 AI Chat</a>
                <a href="write.php" class="btn btn-primary">글쓰기</a>
                <a href="logout.php" class="btn btn-danger">로그아웃</a>
            </div>
        </div>

        <table class="table table-hover table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th style="width: 10%;">번호</th>
                    <th style="width: 50%;">제목</th>
                    <th style="width: 20%;">작성자</th>
                    <th style="width: 20%;">작성일</th>
                    <th style="width: 10%;">관리</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                <tr>
                    <td><?= $post['id'] ?></td>
                    <td class="text-start ps-4"><?= htmlspecialchars($post['title']) ?></td>
                    <td><?= htmlspecialchars($post['username']) ?></td>
                    <td><?= substr($post['created_at'], 0, 10) ?></td>
                    <td>
                        <?php if ($_SESSION['username'] === $post['username']): ?>
                            <a href="delete.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('정말 삭제하시겠습니까?');">삭제</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>