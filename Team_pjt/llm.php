<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['username'])) { header("Location: login.php"); exit(); }

$response = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prompt = $_POST['prompt'];
    
    // OpenAI API 호출
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $openai_key
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $data = [
        'model' => 'gpt-3.5-turbo',
        'messages' => [['role' => 'user', 'content' => $prompt]]
    ];
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $result = curl_exec($ch);
    curl_close($ch);
    
    $json = json_decode($result, true);
    $response = $json['choices'][0]['message']['content'] ?? "오류 발생: API 키를 확인하세요.";
}
?>
<!DOCTYPE html>
<html>
<body>
    <h2>🤖 AI에게 물어보세요</h2>
    <form method="post">
        <textarea name="prompt" style="width:300px; height:100px;"></textarea><br>
        <button type="submit">질문하기</button>
    </form>
    <?php if ($response): ?>
        <div style="background:#eef; padding:10px; margin-top:10px;">
            <strong>AI 답변:</strong><br>
            <?php echo nl2br(htmlspecialchars($response)); ?>
        </div>
    <?php endif; ?>
    <br><a href="index.php">홈으로</a>
</body>
</html>