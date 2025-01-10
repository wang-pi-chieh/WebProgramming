<?php
session_start(); // 啟用 Session
include('connectionSession.php');

// 接收表單數據
$userType = $_POST['userType'];
$account = $_POST['account'];
$password = $_POST['password'];

// 根據 userType 選擇資料表
if ($userType === 'student') {
    $table = 'students';
    $nameField = 'name'; // 假設學生表中的名稱欄位為 name
} elseif ($userType === 'host') {
    $table = 'host';
    $nameField = 'host'; // 假設主辦方表中的名稱欄位為 host
} else {
    // 如果 userType 無效，返回錯誤
    echo "<script>
        alert('用戶類型無效，請重新選擇！');
        history.back();
    </script>";
    exit();
}

// 查詢資料庫
$sql = "SELECT * FROM $table WHERE account = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $account);
$stmt->execute();
$result = $stmt->get_result();

// 檢查查詢結果
if ($result->num_rows === 0) {
    // 帳號不存在
    if ($userType === 'host') {
        echo "<script>
            alert('帳號或密碼錯誤，請重新輸入！');
            history.back();
        </script>";
    } else {
        echo "<script>
            alert('帳號不存在，請確認帳號或前往註冊！');
            if (confirm('是否前往註冊頁面？')) {
                window.location.href = 'studentRegister.php';
            } else {
                history.back();
            }
        </script>";
    }
    exit();
}

$row = $result->fetch_assoc();
if ($row['password'] !== $password) {
    // 密碼錯誤
    if ($userType === 'host') {
        echo "<script>
            alert('帳號或密碼錯誤，請重新輸入！');
            history.back();
        </script>";
    } else {
        echo "<script>
            alert('密碼錯誤，請重新輸入！');
            history.back();
        </script>";
    }
    exit();
}

// 帳號與密碼正確，保存登入者信息到 Session
$_SESSION['user'] = [
    'name' => $row[$nameField], // 根據用戶類型提取正確的名稱欄位
    'account' => $row['account'],
    'sid' => $row['sid'],
    'userType' => $userType
];

// 根據 userType 導向不同頁面
if ($userType === 'host') {
    header("Location: homeLoginHost.php");
} else {
    header("Location: homeLogin.php");
}

?>
