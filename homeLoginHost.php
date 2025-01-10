<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['userType'] !== 'host') {
    header("Location: homeLoginHost.html"); // 未登入或非主辦方則跳轉到登入頁面
    exit();
}
$user = $_SESSION['user'];
include 'connection.php';
?>


<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NUK 主辦方活動管理</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            overflow: hidden;
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
        main {
            padding: 10px 20px 20px; /* 調整 padding 以避免 header 遮擋 */
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 80px); /* 確保主區域大小固定，不隨視窗改變 */
            overflow: auto;
        }

        .login-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            color: white;
            text-decoration: none;
            font-weight: bold;
            font-size: 20px;
            z-index: 1000;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .login-container {
            display: flex;
            justify-content: center;
            gap: 40px;
            padding: 20px;
            width: 1200px; /* 固定寬度 */
        }

        .login-box {
            width: 400px;
            height: 450px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.2s;
        }

        .login-box:hover {
            transform: scale(1.05);
        }

        .login-box img {
            width: 250px;
            height: 250px;
            margin-bottom: 30px;
            filter: grayscale(100%);
            transition: filter 0.3s ease-in-out;
        }

        .login-box button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background-color:rgb(54, 181, 209);
            color: white;
            transition: background-color 0.3s;
        }
        .login-box:hover img {
            filter: grayscale(0%);
        }

        .login-box button:hover {
            background-color:rgb(38, 149, 177);
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

    <main>
        <div class="login-container">
            <!-- Box 1 -->
            <div class="login-box">
                <a href="activityAdd.php"><img src="key.png" alt="新增活動"></a>
                <h1>新增活動</h1>
            </div>
            <!-- Box 2 -->
            <div class="login-box">
                <a href="activityModify.php"><img src="book.png" alt="修改活動"></a>
                <h1>修改活動</h1>
            </div>
            <!-- Box 3 -->
            <div class="login-box">
                <a href="applicant.php"><img src="hand.png" alt="參與者資料"></a>
                <h1>參與者資料</h1>
            </div>
        </div>

    </main>
</body>
</html>
