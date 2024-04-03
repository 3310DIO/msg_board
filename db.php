<?php

$host = 'localhost'; // MySQL server ip
$dbname = 'message_board'; // MySQL 資料庫名稱
$user = 'user_id_1'; // MySQL 使用者帳號
$password = '3310'; // MySQL密碼

try{
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password); // 建立MySQL伺服器連接和開啟資料庫 

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // 指定PDO錯誤模式和錯誤處理
}catch(PDOException $e){
    echo '數據庫連接失敗: ' . $e->getMessage(); // 捕捉例外物件資訊
}
?>
