<?php
session_start();

$host = "localhost"; // 資料庫主機
$username = "root"; // 資料庫帳號
$password = "yes123ma"; // 資料庫密碼
$database = "activity registration system"; // 資料庫名稱

// 建立連線
$conn = new mysqli($host, $username, $password, $database);

// 檢查連線是否成功
if ($conn->connect_error) {
    die("無法連接資料庫: " . $conn->connect_error);
}
?>