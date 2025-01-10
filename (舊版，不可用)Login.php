<?php
include 'connection.php'; // 引入資料庫連線

// 接收表單數據
$userType = $_POST['userType'];
$account = $_POST['account'];
$password = $_POST['password'];

// 根據 userType 選擇資料表
$table = ($userType === 'student') ? 'students' : 'host';

// 查詢資料庫
$sql = "SELECT * FROM $table WHERE account = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $account);
$stmt->execute();
$result = $stmt->get_result();

// 檢查查詢結果
if ($result->num_rows === 0) {
    // 帳號不存在，跳轉到註冊頁面
    header("Location: StudentRegister.html");
    exit();
}

$row = $result->fetch_assoc();
if ($row['password'] !== $password) {
    // 密碼錯誤，彈窗提示
    echo "<script>
        alert('密碼錯誤，請重新輸入！');
        history.back();
    </script>";
    exit();
}

// 帳號與密碼正確，跳轉到對應頁面
if ($userType === 'student') {
    header("Location: homeLogin.php");
} else {
    header("Location: home.php");
}
exit();
?>
