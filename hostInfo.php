<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: whoEnter.html"); // 未登入則跳轉到登入頁面
    exit();
}

$user = $_SESSION['user'];
$sid = $user['sid']; // 假設 Session 中包含學號 sid
include('connectionSession.php');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>主辦方資訊</title>
    <!-- 引入 Bootstrap（可選） -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <style>
        /* 設定頁面佈局 */
        body {
            background-color: rgba(156,187,179,1);
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .table-container {
            width: 100%; /* 滿版寬度 */
            padding: 10px;
            overflow-y: auto; /* 若內容過多，啟用滾動條 */
        }

        .table-container table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        .table-container th {
            background-color: rgb(148, 195, 166); /* 標頭背景顏色 */
            color: white; /* 標頭文字顏色 */
            padding: 10px;
            border: 1px solid #ddd;
        }

        .table-container td {
            background-color:rgb(218, 236, 222);
            padding: 10px;
            border: 1px solid #ddd;
        }

        /* 表格在小螢幕適配 */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .table-container {
                flex: 1;
            }
        }
        main {
            background-color: rgba(156,187,179,1);
            padding: 20px;
        }

        .activity-list {
            max-width: 800px;
            margin: 0 auto;
        }

        .activity {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .activity h3 {
            margin-top: 0;
            color: #333;
        }

        .activity p {
            margin: 5px 0;
        }

        header {
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

        .container {
            display: flex;
            justify-content: space-around;
            align-items: center;
            width: 100%;
            height: 300px;
            overflow: hidden;
            gap: 10px; /* 增加圖片間距 */
            margin-top: 20px;
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
</head>
<body>
    <header>
    <div class="header">
        <a href="homeLogin.php">
            <img src="logo.png" alt="網站Logo" class="logo">
        </a>
        主辦方資訊
        <div class="user-menu">
            <span id="user-menu-toggle"><?php echo htmlspecialchars($user['name']); ?> ▼</span>
            <ul>
                <li><a href="studentAttend.php">已報名資訊</a></li>
                <li><a href="activityInfo.php">活動詳情</a></li>
                <li><a href="hostInfo.php">活動方資訊</a></li>
                <li><a href="placeInfo.php">地點資訊</a></li>
                <li><a href="studentInfo.php">個人資訊</a></li>
                <li><a href="home.php">登出</a></li>
            </ul>
        </div>
    </div>
    </header>
    <main>

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

        <!-- 資料表 -->
        <div class="table-container">
            <?php
            include('connectionSession.php');

            // SQL 查詢
            $query = "SELECT * FROM host";
            $query_run = $conn->query($query);

            if ($query_run && $query_run->num_rows > 0) {
            ?>
                <table>
                    <thead>
                        <tr>
                            <th>主辦方</th>
                            <th>辦公室</th>
                            <th>連絡電話</th>
                            <th>連絡信箱</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($row = $query_run->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['host']) . "</td>
                                    <td>" . htmlspecialchars($row['office']) . "</td>
                                    <td>" . htmlspecialchars($row['phone']) . "</td>
                                    <td>" . htmlspecialchars($row['mail']) . "</td>
                                </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            <?php
            } else {
                echo "<p>目前沒有資料可顯示。</p>";
            }
            ?>
        </div>
    </main>
</body>
</html>
