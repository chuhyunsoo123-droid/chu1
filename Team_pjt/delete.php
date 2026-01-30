<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['username']) || !isset($_GET['id'])) {
    header("Location: board.php");
    exit;
}

$id = $_GET['id'];
$username = $_SESSION['username'];

// 작성자 본인인지 확인 후 삭제
$sql = "DELETE FROM posts WHERE id = ? AND author = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $id, $username);

if ($stmt->execute()) {
    echo "<script>alert('삭제되었습니다.'); location.href='board.php';</script>";
} else {
    echo "<script>alert('삭제 실패.'); location.href='board.php';</script>";
}
?>