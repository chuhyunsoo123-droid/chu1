<?php
session_start();
include 'db_connect.php';

$sql = "SELECT * FROM posts ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>BOARD</title>
    <style>
        body { background: #fff; color: #000; padding: 50px; font-family: 'Arial', sans-serif; }
        .container { max-width: 800px; margin: 0 auto; }
        h2 { border-bottom: 4px solid #000; padding-bottom: 10px; letter-spacing: 2px; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #000; color: #fff; padding: 12px; }
        td { border-bottom: 1px solid #000; padding: 12px; text-align: center; }
        .btn { background: #000; color: #fff; padding: 8px 15px; text-decoration: none; font-weight: bold; border: none; cursor: pointer; display: inline-block; }
        .btn-outline { background: #fff; color: #000; border: 1px solid #000; }
        .top-menu { display: flex; justify-content: space-between; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>COMMUNITY</h2>
        <div class="top-menu">
            <a href="index.php" class="btn btn-outline">GO HOME</a>
            <?php if(isset($_SESSION['username'])): ?>
                <a href="write.php" class="btn">WRITE</a>
            <?php endif; ?>
        </div>
        <table>
            <tr>
                <th>NO</th>
                <th>TITLE</th>
                <th>AUTHOR</th>
                <th>ACTION</th>
            </tr>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td style="text-align:left;"><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['author']); ?></td>
                    <td>
                        <?php if(isset($_SESSION['username']) && $_SESSION['username'] == $row['author']): ?>
                            <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-outline" style="font-size: 0.8em;" onclick="return confirm('삭제하시겠습니까?')">DEL</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">게시글이 없습니다.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>