<?php
session_start();
include 'db_connect.php';

// [중요] 로그인 체크 (user_id나 username 둘 중 하나라도 없으면 튕김)
if (!isset($_SESSION['user_id']) && !isset($_SESSION['username'])) { 
    echo "<script>alert('로그인이 필요합니다.'); location.href='index.php';</script>";
    exit; 
}

// ---------------------------------------------------------
// [API 처리 로직] AJAX 요청이 오면 여기서 답장만 주고 끝냄
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_input = isset($_POST['message']) ? trim($_POST['message']) : '';

    if (empty($user_input)) {
        echo "질문을 입력해주세요.";
        exit;
    }

    // ---------------------------------------------------------
    // [설정] AnythingLLM 연결 정보
    // ---------------------------------------------------------
    $base_url = 'http://localhost:3001'; 
    $workspace_slug = 'groq-test'; // 워크스페이스 이름 (설정과 다르면 수정 필요)
    $api_key = '9GES0QN-HYZMSPB-NCC87WH-9S7WHC4'; // 아까 확인된 키

    // cURL 요청 (채팅 API)
    $ch = curl_init();
    $url = "$base_url/api/v1/workspace/$workspace_slug/chat";

    $data = [
        'message' => $user_input,
        'mode' => 'chat'
    ];

    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key
    ];

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60); // 타임아웃 60초

    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        echo "⚠️ 서버 연결 에러: " . curl_error($ch);
    } else {
        $decoded = json_decode($response, true);
        if (isset($decoded['textResponse'])) {
            echo $decoded['textResponse'];
        } else {
            // 디버깅용: 응답이 없을 때 에러 메시지
            echo "⚠️ 답변을 가져오지 못했습니다. (API Key 또는 워크스페이스 이름 '$workspace_slug' 확인)";
        }
    }
    curl_close($ch);
    exit; // HTML 렌더링 방지
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>AI CHAT - Why Works?</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* 1. 기본 스타일 (Black & White 테마 유지) */
        body {
            background-color: #ffffff;
            color: #000;
            font-family: 'Arial', sans-serif;
            margin: 0;
            display: flex;
            flex-direction: column;
            height: 100vh; /* 화면 전체 높이 사용 */
            overflow: hidden; /* 이중 스크롤 방지 */
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
        }
        h2 {
            margin: 0;
            font-weight: 800;
            letter-spacing: 1px;
            font-size: 1.5rem;
        }
        .btn-home {
            text-decoration: none;
            color: #000;
            font-weight: bold;
            border: 2px solid #000;
            padding: 5px 15px;
            transition: 0.3s;
        }
        .btn-home:hover {
            background: #000;
            color: #fff;
        }

        /* 3. 채팅 영역 (스크롤 가능) */
        #chat-container {
            flex: 1; /* 남은 공간 다 차지 */
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            background: #f8f9fa; /* 아주 연한 회색 배경 */
        }

        /* 말풍선 공통 */
        .message {
            max-width: 80%;
            padding: 15px;
            border-radius: 10px;
            font-size: 1rem;
            line-height: 1.6;
            position: relative;
            word-break: break-word;
        }

        /* AI 말풍선 (흰색 배경 + 검은 테두리) */
        .message.ai {
            align-self: flex-start;
            background: #fff;
            border: 2px solid #000;
            color: #000;
        }
        .message.ai::before {
            content: "🤖 AI";
            display: block;
            font-weight: bold;
            font-size: 0.8rem;
            margin-bottom: 5px;
            color: #555;
        }

        /* 내 말풍선 (검은 배경 + 흰색 글씨) */
        .message.user {
            align-self: flex-end;
            background: #000;
            color: #fff;
            border: 2px solid #000;
        }
        .message.user::before {
            content: "ME";
            display: block;
            font-weight: bold;
            font-size: 0.8rem;
            margin-bottom: 5px;
            color: #ccc;
            text-align: right;
        }

        /* 4. 하단 입력창 영역 */
        .input-area {
            background: #fff;
            border-top: 4px solid #000;
            padding: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .input-wrapper {
            width: 100%;
            max-width: 800px;
            display: flex;
            gap: 10px;
        }
        textarea {
            flex: 1;
            height: 50px;
            padding: 12px;
            border: 2px solid #000;
            resize: none;
            font-family: inherit;
            font-size: 1rem;
            outline: none;
        }
        textarea:focus {
            background: #f0f0f0;
        }
        button#send-btn {
            width: 80px;
            background: #000;
            color: #fff;
            border: none;
            font-weight: bold;
            cursor: pointer;
            font-size: 1rem;
            transition: 0.2s;
        }
        button#send-btn:hover {
            background: #333;
        }

        /* 로딩 애니메이션 */
        .typing { font-style: italic; color: #666; font-size: 0.9rem; }

    </style>
</head>
<body>

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

    <script>
        const chatContainer = document.getElementById('chat-container');
        const userInput = document.getElementById('user-input');
        const sendBtn = document.getElementById('send-btn');

        // 엔터키 전송 기능 (Shift+Enter는 줄바꿈)
        function handleEnter(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault(); // 줄바꿈 방지
                sendMessage();
            }
        }

        function sendMessage() {
            const message = userInput.value.trim();
            if (message === "") return;

            // 1. 내 메시지 표시
            appendMessage('user', message);
            userInput.value = '';

            // 2. 로딩 표시
            const loadingId = appendMessage('ai', '<span class="typing">답변 생성 중...</span>');

            // 3. 서버로 전송 (AJAX)
            const formData = new FormData();
            formData.append('message', message);

            fetch('llm.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                // 로딩 메시지 내용을 진짜 답변으로 교체
                const loadingBubble = document.getElementById(loadingId);
                if (loadingBubble) {
                    // 줄바꿈 문자(\n)를 <br>로 변환해서 보기 좋게
                    loadingBubble.innerHTML = data.replace(/\n/g, '<br>');
                }
            })
            .catch(error => {
                const loadingBubble = document.getElementById(loadingId);
                if (loadingBubble) loadingBubble.innerHTML = "⚠️ 오류가 발생했습니다.";
            });
        }

        // 화면에 말풍선 붙이는 함수
        function appendMessage(type, text) {
            const uniqueId = 'msg-' + Date.now();
            const div = document.createElement('div');
            div.className = `message ${type}`;
            
            // 내용 넣기 (ID 부여해서 나중에 내용 바꿀 수 있게 함)
            if(type === 'ai' && text.includes('span class="typing"')) {
                // 로딩 중일 때는 전체가 ID 타겟
                 div.innerHTML = text; 
                 div.id = uniqueId;
            } else {
                 div.innerHTML = text;
            }

            chatContainer.appendChild(div);
            chatContainer.scrollTop = chatContainer.scrollHeight; // 스크롤 맨 아래로
            return uniqueId;
        }
    </script>
</body>
</html>