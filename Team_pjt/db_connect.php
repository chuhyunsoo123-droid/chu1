<?php
// 기본값 설정
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "app_db";

// LLM 기본값
$openai_key = ""; 
$llm_api_url = "https://api.openai.com/v1/chat/completions";
$llm_model = "gpt-3.5-turbo";

// 서버 배포 환경에서는 이 파일이 로드되어 위 변수들을 덮어씁니다.
if (file_exists('config_aws.php')) {
    include 'config_aws.php'; 
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>