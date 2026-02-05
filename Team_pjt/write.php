<?php
session_start(); //
include 'db_connect.php'; //

if (!isset($_SESSION['username'])) { //
    echo "<script>alert('로그인이 필요합니다.'); location.href='login.php';</script>"; //
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") { //
    $title = $_POST['title']; //
    $content = $_POST['content']; //
    $author = $_SESSION['username']; //

    $sql = "INSERT INTO posts (title, content, author) VALUES (?, ?, ?)"; //
    $stmt = $conn->prepare($sql); //
    $stmt->bind_param("sss", $title, $content, $author); //
    
    if ($stmt->execute()) { //
        header("Location: board.php"); //
    } else {
        echo "오류 발생: " . $conn->error; //
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>글쓰기</title>
    <style>
        body { background-color: #ffffff; color: #000000; font-family: 'Arial', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .container { border: 2px solid #000; padding: 40px; width: 450px; text-align: left; }
        h2 { text-align: center; letter-spacing: 2px; border-bottom: 4px solid #000; padding-bottom: 10px; }
        input[type="text"], textarea { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #000; box-sizing: border-box; outline: none; font-family: inherit; }
        textarea { height: 200px; resize: none; }
        button { width: 100%; padding: 12px; background: #000; color: #fff; border: none; cursor: pointer; font-weight: bold; margin-top: 10px; }
        button:hover { background: #333; }
        .back-link { text-align: center; margin-top: 20px; }
        a { color: #000; text-decoration: none; font-size: 0.85em; border-bottom: 1px solid #000; }
    </style>
</head>
<body>
    <div class="container">
        <h2>NEW POST</h2>
        <form method="post">
            <input type="text" name="title" placeholder="TITLE" required>
            <textarea name="content" placeholder="WRITE YOUR CONTENT HERE..." required></textarea>
            <button type="submit">POSTING</button>
        </form>
        <div class="back-link">
            <a href="board.php">CANCEL</a>
        </div>
    </div>
</body>
</html>