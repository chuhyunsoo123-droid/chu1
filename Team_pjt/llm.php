<?php
session_start();
include 'db_connect.php';

// [중요] 로그인 체크
if (!isset($_SESSION['user_id']) && !isset($_SESSION['username'])) { 
    echo "<script>alert('로그인이 필요합니다.'); location.href='index.php';</script>";
    exit; 
}

// ---------------------------------------------------------
// [API 처리 및 DB 저장 로직]
// ---------------------------------------------------------
if (!isset($_SESSION['user_id']) && !isset($_SESSION['username'])) { 
    echo "<script>alert('로그인이 필요합니다.'); location.href='index.php';</script>";
    exit; 
}

// ---------------------------------------------------------
// [API 처리 및 DB 저장 로직]
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_input = isset($_POST['message']) ? trim($_POST['message']) : '';
    if (empty($user_input)) { echo "질문을 입력해주세요."; exit; }

    $base_url = 'http://localhost:3001'; 
    $workspace_slug = 'groq-test'; 
    $api_key = '9GES0QN-HYZMSPB-NCC87WH-9S7WHC4'; 

    $url = "$base_url/api/v1/workspace/$workspace_slug/chat";
    $data = ['message' => $user_input, 'mode' => 'chat'];
    $headers = ['Content-Type: application/json', 'Authorization: Bearer ' . $api_key];

    // [NEW] 재시도 설정
    $max_retries = 3;  // 최대 3번까지 재시도
    $attempt = 0;      // 현재 시도 횟수
    $success = false;  // 성공 여부
    $ai_response = ""; // 답변 저장 변수
    $last_response_raw = ""; // [디버깅] 마지막 응답 원본 저장용

    // [NEW] 재시도 루프 시작
    while ($attempt < $max_retries && !$success) {
        $attempt++; // 시도 횟수 증가

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // [수정] 타임아웃을 60초 -> 180초로 늘림 (긴 답변 대비)
        curl_setopt($ch, CURLOPT_TIMEOUT, 180); 

        $response = curl_exec($ch);
        $last_response_raw = $response; // 에러 확인을 위해 응답 저장
        
        if (!curl_errno($ch)) {
            $decoded = json_decode($response, true);
            // 정상적인 답변(textResponse)이 있는지 확인
            if (isset($decoded['textResponse'])) {
                $ai_response = $decoded['textResponse'];
                $success = true; // 성공 플래그 세팅 -> 루프 종료
            } else {
                // 답변이 없으면 잠시 대기 후 재시도 (서버 부하 방지용 1초 대기)
                sleep(1); 
            }
        }
        curl_close($ch);
    }
    // [NEW] 재시도 루프 끝

    // 결과 처리
    if ($success) {
        echo $ai_response; // 1. 화면에 답변 출력

        // 2. RDS 데이터베이스에 대화 내용 저장
        if (isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
            $stmt = $conn->prepare("INSERT INTO chat_logs (username, question, answer) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $user_input, $ai_response);
            $stmt->execute();
            $stmt->close();
        }
    } else {
        // 3번 다 시도했는데도 실패한 경우 -> [디버깅] 상세 에러 로그 출력
        echo "<div style='background:#ffebee; color:#c62828; padding:15px; border:2px solid #ef9a9a; border-radius:5px; font-weight:bold;'>";
        echo "⚠️ 답변을 가져오지 못했습니다. (3회 재시도 실패)<br><br>";
        
        echo "[서버 응답 로그 - 원인을 확인하세요]<br>";
        echo "<div style='background:#fff; color:#333; padding:10px; margin-top:5px; border:1px solid #ccc; font-weight:normal; word-break:break-all;'>";
        
        if (empty($last_response_raw)) {
            echo "응답 값이 비어있습니다. (서버가 꺼져있거나 네트워크 문제)";
        } else {
            // HTML 태그가 섞여있을 수 있으므로 안전하게 출력
            echo htmlspecialchars($last_response_raw);
        }
        
        echo "</div>";
        echo "</div>";
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>AI CHAT - Why Works?</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, interactive-widget=resizes-content">
    <style>
        /* 1. 기본 Body 스타일 (바깥 배경) */
        body {
            background-color: #e9ecef; /* 여백 부분의 색상 (연한 회색) */
            color: #000;
            font-family: 'Arial', sans-serif;
            margin: 0;
            display: flex;
            justify-content: center; /* 중앙 정렬 */
            
            /* [수정 2] 모바일 브라우저 주소창 대응 */
            height: 100vh;       /* PC 및 구형 브라우저용 */
            height: 100dvh;      /* 최신 모바일 브라우저용 (Dynamic Viewport Height) */
            overflow: hidden;    /* 전체 스크롤 방지 */
        }

        /* [NEW] 메인 컨테이너 (실제 콘텐츠 영역) */
        .main-container {
            width: 100%;
            max-width: 1200px; /* 너무 넓어지지 않게 최대폭 설정 */
            height: 100%; /* 높이 꽉 채우기 */
            display: flex;
            flex-direction: column;
            background-color: #ffffff; /* 안쪽 배경은 흰색 */
            /* 테마 유지를 위한 좌우 굵은 테두리 추가 */
            border-left: 4px solid #000; 
            border-right: 4px solid #000;
            box-sizing: border-box; /* 테두리 포함 크기 계산 */
        }

        /* 2. 상단 헤더 */
        header {
            border-bottom: 4px solid #000;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            z-index: 10;
            flex-shrink: 0; /* 헤더 크기 고정 */
        }
        h2 { margin: 0; font-weight: 800; letter-spacing: 1px; font-size: 1.5rem; }
        .btn-home {
            text-decoration: none; color: #000; font-weight: bold;
            border: 2px solid #000; padding: 5px 15px; transition: 0.3s;
        }
        .btn-home:hover { background: #000; color: #fff; }

        /* 3. 채팅 영역 (변경 없음) */
        #chat-container {
            flex: 1; /* 남은 공간 모두 차지 */
            overflow-y: auto; /* 여기만 스크롤 됨 */
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            background: #f8f9fa;
        }

        /* 말풍선 스타일 (변경 없음) */
        .message {
            max-width: 80%; padding: 15px; border-radius: 10px;
            font-size: 1rem; line-height: 1.6; position: relative; word-break: break-word;
        }
        .message.ai {
            align-self: flex-start; background: #fff; border: 2px solid #000; color: #000;
        }
        .message.ai::before {
            content: "🤖 AI"; display: block; font-weight: bold; font-size: 0.8rem; margin-bottom: 5px; color: #555;
        }
        .message.user {
            align-self: flex-end; background: #000; color: #fff; border: 2px solid #000;
        }
        .message.user::before {
            content: "ME"; display: block; font-weight: bold; font-size: 0.8rem; margin-bottom: 5px; color: #ccc; text-align: right;
        }

        /* 4. 하단 입력창 영역 */
        .input-area {
            background: #fff;
            border-top: 4px solid #000;
            padding: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-shrink: 0; /* 입력창 크기 고정 */
            
            /* [수정 3] 아이폰 하단 홈 바 영역 침범 방지 */
            padding-bottom: env(safe-area-inset-bottom, 20px);
        }
        .input-wrapper {
            width: 100%; max-width: 800px; display: flex; gap: 10px;
        }
        textarea {
            flex: 1; height: 50px; padding: 12px; border: 2px solid #000;
            resize: none; font-family: inherit; font-size: 1rem; outline: none;
        }
        textarea:focus { background: #f0f0f0; }
        button#send-btn {
            width: 80px; background: #000; color: #fff; border: none; font-weight: bold;
            cursor: pointer; font-size: 1rem; transition: 0.2s;
        }
        button#send-btn:hover { background: #333; }
        .typing { font-style: italic; color: #666; font-size: 0.9rem; }

        /* 모바일 화면에서는 여백 없이 꽉 차게 */
        @media (max-width: 768px) {
            .main-container {
                border-left: none;
                border-right: none;
            }
        }
    </style>
</head>
<body>

    <div class="main-container">
        <header>
            <h2>SERVER BRAIN</h2>
            <a href="board.php" class="btn-home">EXIT</a>
        </header>

        <div id="chat-container">
            <div class="message ai">
                안녕하세요! <strong>Why Works?</strong> AI 서버입니다.<br>
                무엇을 도와드릴까요?
            </div>
        </div>

        <div class="input-area">
            <div class="input-wrapper">
                <textarea id="user-input" placeholder="질문을 입력하세요... (Enter로 전송)" onkeypress="handleEnter(event)"></textarea>
                <button id="send-btn" onclick="sendMessage()">SEND</button>
            </div>
        </div>
    </div> <script>
        const chatContainer = document.getElementById('chat-container');
        const userInput = document.getElementById('user-input');
        const sendBtn = document.getElementById('send-btn');

        function handleEnter(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        }

        function sendMessage() {
            const message = userInput.value.trim();
            if (message === "") return;
            appendMessage('user', message);
            userInput.value = '';
            const loadingId = appendMessage('ai', '<span class="typing">답변 생성 중...</span>');
            const formData = new FormData();
            formData.append('message', message);

            fetch('llm.php', { method: 'POST', body: formData })
            .then(response => response.text())
            .then(data => {
                const loadingBubble = document.getElementById(loadingId);
                if (loadingBubble) { loadingBubble.innerHTML = data.replace(/\n/g, '<br>'); }
            })
            .catch(error => {
                const loadingBubble = document.getElementById(loadingId);
                if (loadingBubble) loadingBubble.innerHTML = "⚠️ 오류가 발생했습니다.";
            });
        }

        function appendMessage(type, text) {
            const uniqueId = 'msg-' + Date.now();
            const div = document.createElement('div');
            div.className = `message ${type}`;
            if(type === 'ai' && text.includes('span class="typing"')) {
                 div.innerHTML = text; div.id = uniqueId;
            } else {
                 div.innerHTML = text;
            }
            chatContainer.appendChild(div);
            chatContainer.scrollTop = chatContainer.scrollHeight;
            return uniqueId;
        }
    </script>
</body>
</html>