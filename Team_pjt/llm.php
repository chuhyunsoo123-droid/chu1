<?php
session_start();
include 'db_connect.php'; // DB 연결

// 로그인 안 했으면 로그인 페이지로 쫓아냄
if (!isset($_SESSION['username'])) { 
    echo "<script>alert('로그인이 필요합니다.'); location.href='login.php';</script>";
    exit; 
}

$response_text = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prompt = $_POST['prompt'];
    
    // ---------------------------------------------------------
    // [설정 1] AnythingLLM 서버 주소 (도커 내부 통신용)
    // ---------------------------------------------------------
    $base_url = 'http://localhost:3001'; 
    
    // ---------------------------------------------------------
    // [설정 2] AnythingLLM API Key (웹 화면에서 발급받은 키를 아래 따옴표 안에 넣으세요)
    // ---------------------------------------------------------
    $api_key = '여기에_ANYTHING_LLM_API_KEY_를_붙여넣으세요'; 

    // API 호출 준비
    $url = $base_url . "/api/v1/openai/chat/completions";
    
    $postData = [
        "model" => "anything-llm", // 모델명은 고정
        "messages" => [
            [ "role" => "user", "content" => $prompt ]
        ],
        "temperature" => 0.7
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key
    ]);

    // 서버에 요청 보내기
    $result = curl_exec($ch);
    
    // 결과 처리
    if (curl_errno($ch)) {
        $response_text = '⚠️ 서버 연결 실패: AnythingLLM이 켜져 있는지 확인해주세요.<br>(' . curl_error($ch) . ')';
    } else {
        $decoded = json_decode($result, true);
        if (isset($decoded['choices'][0]['message']['content'])) {
             $response_text = $decoded['choices'][0]['message']['content'];
        } else {
             // 응답이 이상할 경우 (API Key 오류 등)
             $response_text = "⚠️ 답변을 가져오지 못했습니다.<br>API Key나 워크스페이스 설정을 확인해주세요.";
        }
    }
    curl_close($ch);
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>AI CHAT</title>
    <style>
        /* 기존 사이트 스타일 (Black & White) 완벽 적용 */
        body { 
            background-color: #ffffff; 
            color: #000000; 
            font-family: 'Arial', sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0; 
        }
        .container { 
            border: 2px solid #000; 
            padding: 40px; 
            width: 450px; 
            text-align: center; 
        }
        h2 { 
            border-bottom: 4px solid #000; 
            padding-bottom: 10px; 
            letter-spacing: 2px; 
            margin-bottom: 30px;
        }
        textarea { 
            width: 100%; 
            height: 120px; 
            padding: 12px; 
            margin: 10px 0; 
            border: 1px solid #000; 
            box-sizing: border-box; 
            outline: none; 
            font-family: inherit; 
            resize: none; 
        }
        button { 
            width: 100%; 
            padding: 12px; 
            background: #000; 
            color: #fff; 
            border: none; 
            cursor: pointer; 
            font-weight: bold; 
            margin-top: 10px; 
        }
        button:hover { background: #333; }
        
        /* AI 답변 박스 스타일 */
        .response-box {
            text-align: left;
            border: 1px solid #000;
            padding: 20px;
            margin-top: 30px;
            background: #fafafa;
            line-height: 1.6;
            white-space: pre-wrap; /* 줄바꿈 유지 */
        }
        .response-title {
            font-weight: bold;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            margin-bottom: 10px;
            display: block;
        }
        
        a { 
            color: #000; 
            text-decoration: none; 
            font-size: 0.85em; 
            margin-top: 20px; 
            display: inline-block; 
            border-bottom: 1px solid #000; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>SERVER BRAIN</h2>
        <p style="font-size: 0.9em; margin-bottom: 20px;">
            서버에 저장된 문서를 기반으로 답변합니다.
        </p>

        <form method="post">
            <textarea name="prompt" placeholder="질문을 입력하세요..." required></textarea>
            <button type="submit">ASK AI</button>
        </form>

        <?php if ($response_text): ?>
            <div class="response-box">
                <span class="response-title">ANSWER</span>
                <?php echo htmlspecialchars($response_text); ?>
            </div>
        <?php endif; ?>

        <a href="index.php">BACK TO HOME</a>
    </div>
</body>
</html>