<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "app_db";

// AWS 등 배포 환경 설정이 있으면 덮어쓰기
if (file_exists('config_aws.php')) {
    include 'config_aws.php'; 
}

// MySQLi 연결 (로그인/게시판 공용)
$conn = new mysqli($servername, $username, $password, $dbname);

// 한글 깨짐 방지
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}
?>