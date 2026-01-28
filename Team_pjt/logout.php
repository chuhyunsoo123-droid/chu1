<?php
session_start();
session_destroy(); // 세션 삭제 (로그아웃 처리)
echo "<script>alert('로그아웃 되었습니다.'); location.href='index.php';</script>";
?>