<?php
include 'db_connect.php'; //

if ($_SERVER["REQUEST_METHOD"] == "POST") { //
    $username = $_POST['username']; //
    $password = $_POST['password']; //

    $hashed_password = password_hash($password, PASSWORD_DEFAULT); //

    $check_sql = "SELECT id FROM users WHERE username = ?"; //
    $stmt = $conn->prepare($check_sql); //
    $stmt->bind_param("s", $username); //
    $stmt->execute(); //
    
    if ($stmt->get_result()->num_rows > 0) { //
        echo "<script>alert('이미 존재하는 아이디입니다.');</script>"; //
    } else {
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)"; //
        $stmt = $conn->prepare($sql); //
        $stmt->bind_param("ss", $username, $hashed_password); //
        
        if ($stmt->execute()) { //
            echo "<script>alert('가입 성공! 로그인해주세요.'); location.href='login.php';</script>"; //
        } else {
            echo "가입 실패: " . $conn->error; //
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>회원가입</title>
    <style>
        body { background-color: #ffffff; color: #000000; font-family: 'Arial', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .container { border: 2px solid #000; padding: 40px; width: 300px; text-align: center; }
        h2 { letter-spacing: 2px; margin-bottom: 30px; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #000; box-sizing: border-box; outline: none; }
        button { width: 100%; padding: 12px; background: #000; color: #fff; border: none; cursor: pointer; font-weight: bold; margin-top: 10px; }
        button:hover { background: #333; }
        a { color: #0