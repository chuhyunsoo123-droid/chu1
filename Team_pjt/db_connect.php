<?php
// 이 부분은 테라폼이 서버에서 자동으로 생성해줍니다.
// 로컬 테스트용으로 비워두거나 기본값만 넣어두세요.
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "app_db";
$openai_key = "";

if (file_exists('config_aws.php')) {
    include 'config_aws.php'; // 서버에서는 이 파일이 로드됩니다.
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>