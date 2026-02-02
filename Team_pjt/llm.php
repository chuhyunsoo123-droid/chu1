<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// [API 처리 로직] : AJAX 요청이 들어오면 HTML 대신 텍스트만 뱉고 끝냄
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. 사용자 질문 받기
    $user_input = isset($_POST['message']) ? trim($_POST['message']) : '';

    if (empty($user_input)) {
        echo "질문을 입력해주세요.";
        exit;
    }

    // 2. AnythingLLM API 설정
    $api_key = '9GES0QN-HYZMSPB-NCC87WH-9S7WHC4'; // (아까 확인한 키로 넣어뒀습니다)
    $base_url = 'http://localhost:3001'; 
    $workspace_slug = 'groq-test'; 

    // 3. cURL 요청 준비
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
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 30초 대기

    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        echo "Error: " . curl_error($ch);
    } else {
        $decoded = json_decode($response, true);
        if (isset($decoded['textResponse'])) {
            echo $decoded['textResponse'];
        } else {
            echo "답변을 가져오지 못했습니다. (설정 확인 필요)";
        }
    }
    curl_close($ch);
    exit; // HTML 렌더링 방지하고 종료
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>AI Chat - Why Works?</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #343541; color: white; height: 100vh; display: flex; flex-direction: column; overflow: hidden; }
        
        /* 상단 헤더 */
        .chat-header {
            padding: 15px;
            background-color: #202123;
            border-bottom: 1px solid #4d4d4f;
            text-align: center;
            font-size: 1.2rem;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .chat-header a { text-decoration: none; color: #ccc; font-size: 0.9rem; }

        /* 채팅 영역 (스크롤 가능) */
        #chat-container {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            scroll-behavior: smooth;
        }

        /* 말풍선 공통 스타일 */
        .message {
            display: flex;
            gap: 15px;
            max-width: 800px;
            margin: 0 auto;
            width: 100%;
            padding: 10px;
            border-radius: 8px;
        }

        /* AI 답변 스타일 */
        .message.ai {
            background-color: #444654;
            align-self: flex-start;
        }
        /* 사용자 질문 스타일 */
        .message.user {
            background-color: #343541; /* 배경색 통일 */
            border: 1px solid #555;
        }
        
        .avatar {
            width: 30px;
            height: 30px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }
        .avatar.ai-icon { background-color: #19c37d; color: white; }
        .avatar.user-icon { background-color: #5b63d3; color: white; }

        .content {
            line-height: 1.6;
            word-break: break-word;
        }

        /* 하단 입력창 영역 */
        .input-area {
            background-color: #343541;
            padding: 20px;
            border-top: 1px solid #555;
            display: flex;
            justify-content: center;
        }
        .input-wrapper {
            width: 100%;
            max-width: 800px;
            position: relative;
        }
        .form-control {
            background-color: #40414f;
            border: 1px solid #303139;
            color: white;
            border-radius: 12px;
            padding: 15px 50px 15px 15px;
            resize: none;
            height: 55px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-control:focus {
            background-color: #40414f;
            color: white;
            box-shadow: none;
            border-color: #19c37d;
        }
        #send-btn {
            position: absolute;
            right: 10px;
            bottom: 10px;
            background: transparent;
            border: none;
            color: #ccc;
            cursor: pointer;
        }
        #send-btn:hover { color: white; }
        
        /* 로딩 애니메이션 */
        .typing-indicator { font-style: italic; color: #888; font-size: 0.9rem; }
    </style>
</head>
<body>

    <div class="chat-header">
        <span>Why Works? AI Chat</span>
        <a href="board.php">❌ 나가기</a>
    </div>

    <div id="chat-container">
        <div class="message ai">
            <div class="avatar ai-icon">AI</div>
            <div class="content">
                안녕하세요! 무엇을 도와드릴까요?<br>
                문서 내용을 바탕으로 답변해 드립니다.
            </div>
        </div>
    </div>

    <div class="input-area">
        <div class="input-wrapper">
            <input type="text" id="user-input" class="form-control" placeholder="메시지를 입력하세요..." autocomplete="off" onkeypress="handleEnter(event)">
            <button id="send-btn" onclick="sendMessage()">
                <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" height="20" width="20" xmlns="http://www.w3.org/2000/svg"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
            </button>
        </div>
    </div>

    <script>
        const chatContainer = document.getElementById('chat-container');
        const userInput = document.getElementById('user-input');

        // 엔터키 입력 처리
        function handleEnter(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        }

        function sendMessage() {
            const message = userInput.value.trim();
            if (message === "") return;

            // 1. 사용자 메시지 화면에 추가
            appendMessage('User', message, 'user');
            userInput.value = '';

            // 2. 로딩 표시
            const loadingId = appendMessage('AI', '<span class="typing-indicator">생각하는 중...</span>', 'ai');

            // 3. 서버(PHP)에 AJAX 요청 보내기
            const formData = new FormData();
            formData.append('message', message);

            fetch('llm.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                // 로딩 메시지 지우고 진짜 답변 넣기
                const loadingBubble = document.getElementById(loadingId);
                if(loadingBubble) {
                    loadingBubble.innerHTML = data.replace(/\n/g, "<br>"); // 줄바꿈 처리
                }
            })
            .catch(error => {
                const loadingBubble = document.getElementById(loadingId);
                if(loadingBubble) loadingBubble.innerHTML = "오류가 발생했습니다.";
                console.error('Error:', error);
            });
        }

        // 화면에 말풍선 추가하는 함수
        function appendMessage(sender, text, type) {
            const uniqueId = 'msg-' + Date.now();
            const div = document.createElement('div');
            div.className = `message ${type}`;
            
            const avatarClass = type === 'ai' ? 'ai-icon' : 'user-icon';
            const avatarLabel = type === 'ai' ? 'AI' : 'Me';

            div.innerHTML = `
                <div class="avatar ${avatarClass}">${avatarLabel}</div>
                <div class="content" id="${uniqueId}">${text}</div>
            `;
            
            chatContainer.appendChild(div);
            chatContainer.scrollTop = chatContainer.scrollHeight; // 스크롤 맨 아래로
            return uniqueId;
        }
    </script>
</body>
</html>