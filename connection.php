<?php
// 資料庫連接設定
$location = "localhost"; // 資料庫伺服器
$account = "root"; // 使用者帳號
$password = "yes123ma"; // 使用者密碼
$database = "activity registration system"; // 資料庫名稱

// 建立資料庫連接
$conn = new mysqli($location, $account, $password, $database);

// 檢查資料庫連接是否成功
if ($conn->connect_error) {
    die("無法連接資料庫，請洽詢管理員。"); // 簡化錯誤訊息
}
?>
