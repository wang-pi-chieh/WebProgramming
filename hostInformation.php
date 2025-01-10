<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['userType'] !== 'host') {
    header("Location: whoEnter.html"); // 未登入或非主辦方則跳轉到登入頁面
    exit();
}
$user = $_SESSION['user'];
include 'connection.php';

// 獲取當前主辦方資訊
$sql = "SELECT * FROM host WHERE account = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $user['account']); // 假設帳號是 session 中的 'account'
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("無法找到主辦方資訊，請確認資料正確性");
}

$hostInfo = $result->fetch_assoc();

// 檢查是否有表單提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $office = $_POST['office'];
    $phone = $_POST['phone'];
    $mail = $_POST['mail'];
    $password = $_POST['password'];

    // 更新資料庫資料
    $sql = "UPDATE host SET office=?, phone=?, mail=?, password=? WHERE account=?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssss", $office, $phone, $mail, $password, $user['account']);

    if ($stmt->execute()) {
        echo "<script>alert('主辦方資訊修改成功!'); window.location.href='homeLoginHost.php';</script>";
    } else {
        echo "<script>alert('修改失敗，請稍後再試!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>主辦方資訊管理</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #143848;
            color: white;
            text-align: center;
            padding: 20px 0;
            font-size: 24px;
            font-weight: bold;
            position: relative;
            z-index: 0;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);

        }
        .form-container {
            width: 90%;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #f9f9f9;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input {
            width: 95%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[readonly] {
            background-color: #e9ecef;
            cursor: not-allowed;
        }
        .user-menu {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .user-menu span {
            cursor: pointer;
            color: white;
            font-size: 16px;
            font-weight: bold;
        }


        .user-menu ul {
            display: none;
            position: absolute;
            top: 45px;
            right: 0;
            background-color: white;
            list-style: none;
            padding: 0;
            margin: 0;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            z-index: 1001;
            width: auto; 
            text-align: center;
            min-width: 150px; /* 最小寬度避免文字換行 */
            /*text-align: left;  調整文字靠左對齊 */
            white-space: nowrap; /* 禁止文字換行 */
        }

        .user-menu ul li {
            border-bottom: 1px solid #ddd;
        }

        .user-menu ul li:last-child {
            border-bottom: none;
        }

        .user-menu ul li a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: black;
            font-size: 14px;
        }

        .user-menu ul li a:hover {
            background-color: #f1f1f1;
        }

        .user-menu:hover ul {
            display: none;
        }
        button {
            padding: 8px 12px;
            background-color: rgb(52, 155, 181);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: rgb(48, 129, 163);
        }
        .logo {
            position: absolute;
            top: 10px;
            left: 10px;
            width: 100px; /* 調整 logo 的寬度 */
            height: 50px; /* 調整 logo 的高度 */
            object-fit: cover; /* 確保圖片不變形 */
            cursor: pointer; /* 滑鼠移上時變成手型 */
            z-index: 1001; /* 確保 logo 在最上層 */
        }
    </style>
    <script>
        //工具欄功能
        document.addEventListener('DOMContentLoaded', () => {
        const toggleButton = document.getElementById('user-menu-toggle');
        const menu = document.querySelector('.user-menu ul');

        toggleButton.addEventListener('click', () => {
            if (menu.style.display === 'block') {
                menu.style.display = 'none'; // 收回工具欄
            } else {
                menu.style.display = 'block'; // 打開工具欄
            }
        });
        // 點擊其他區域時收回工具欄
        document.addEventListener('click', (e) => {
            if (!toggleButton.contains(e.target) && !menu.contains(e.target)) {
                menu.style.display = 'none';
            }
        });
    });

    </script>
</head>
<body>
<div class="header">
        <a href="homeLoginHost.php">
            <img src="logo.png" alt="網站Logo" class="logo">
        </a>
        主辦方管理界面
        <div class="user-menu">
            <span id="user-menu-toggle"><?php echo htmlspecialchars($user['name']); ?> ▼</span>
            <ul>
                <li><a href="hostInformation.php">主辦方資訊</a></li>
                <li><a href="home.php">登出</a></li>
            </ul>
        </div>
    </div>

    <div class="form-container">
        <form method="POST" action="">
            <label for="host">主辦方名稱</label>
            <input type="text" id="host" name="host" value="<?php echo htmlspecialchars($hostInfo['host']); ?>" readonly>

            <label for="office">辦公室</label>
            <input type="text" id="office" name="office" value="<?php echo htmlspecialchars($hostInfo['office']); ?>" required>

            <label for="phone">聯絡電話</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($hostInfo['phone']); ?>" required>

            <label for="mail">聯絡信箱</label>
            <input type="email" id="mail" name="mail" value="<?php echo htmlspecialchars($hostInfo['mail']); ?>" required>

            <label for="account">帳號</label>
            <input type="text" id="account" name="account" value="<?php echo htmlspecialchars($hostInfo['account']); ?>" readonly>

            <label for="password">密碼</label>
            <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($hostInfo['password']); ?>" required>

            <button type="submit">儲存修改</button>
        </form>
    </div>
</body>
</html>