<?php
session_start();
session_unset(); // 세션 변수 모두 제거
session_destroy(); // 세션 파괴
header("Location: index.php"); // 알림창 대신 즉시 홈으로 이동하여 오류 방지
exit;
?>