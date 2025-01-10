<?php
session_start();

// 確認使用者是否已登入
if (!isset($_SESSION['user'])) {
    header("Location: whoEnter.html"); // 未登入則跳轉到登入頁面
    exit();
}

// 獲取登入者資訊
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">

    <style>
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


        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            width: 100%;
            height: 100vh;
        }

        .table-container {
            width: 100%;
            padding: 10px;
            overflow-y: auto;
        }

        .table-container table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-container th {
            background-color: rgb(148, 195, 166);
            color: white;
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .table-container td {
            background-color:rgb(218, 236, 222);
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            vertical-align: top;
        }
        
        .table-container a {
            color:rgb(255, 255, 255);
        }

        .table-container a:hover {
            color:rgb(148, 137, 197);
        }

        /* 活動介紹欄位設置 */
        .table-container td.intro {
            max-width: 22.9em; /* 固定寬度 */
            max-height: 7em; /* 限制高度為 3 行 */
            min-height: 7em; /* 設定最小高度為 2 行 */
            overflow-y: auto; /* 垂直滾動條 */
            display: -webkit-box; /* 支援多行截斷 */
            -webkit-box-orient: vertical; /* 垂直方向排列 */
            word-wrap: break-word; /* 允許文字換行 */
            line-height: 1.5em; /* 行高 */
            box-sizing: border-box; /* 包括邊框和填充 */
            padding: 5px; /* 合理內邊距 */
        }

        .signup-btn {
            margin-top: 5px;
            padding: 5px 10px;
            background-color:rgb(110, 148, 189);
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 14px;
        }

        .signup-btn:hover {
            background-color:rgb(148, 137, 197);
        }


        /* 小螢幕適配 */
        @media (max-width: 768px) {
            .table-container td.intro {
                width: 100%; /* 小螢幕下全寬 */
            }
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

        function registerActivity(activityId) {
            fetch('register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ activityId: activityId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload(); // 報名成功後刷新頁面
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('報名過程中出現錯誤，請稍後再試。');
            });
        }
    </script>
</head>
<body>
<div class="header">
        <a href="homeLogin.php">
            <img src="logo.png" alt="網站Logo" class="logo">
        </a>
        NUK BEN活動詳情
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
    <div class="table-container">
        <?php
        include('connection.php');

        $query = "SELECT a.*, 
                  (SELECT COUNT(*) FROM participants p WHERE p.activity = a.name) AS currentCount FROM activity a";
        $query_run = $conn->query($query);

        if ($query_run && $query_run->num_rows > 0) {
        ?>
            <table>
                <thead>
                    <tr>
                        <th>活動名稱</th>
                        <th><a href='placeInfo.php'>活動地點</a></th>
                        <th>活動日期</th>
                        <th><a href='hostInfo.php'>主辦方</a></th>
                        <th>活動費用</th>
                        <th>報名截止日</th>
                        <th>報名人數上限</th>
                        <th>活動介紹</th>
                        <th>備註</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $query_run->fetch_assoc()) {
                        $currentCount = $row['currentCount'];
                        $maxPeople = $row['maxPeople'];
                        echo "<tr>
                                 <td>" . htmlspecialchars($row['name']) . "<br> 
                                    <button class='signup-btn' onclick='registerActivity(\"" . $row['name'] . "\")'>立即報名</button> </td>
                                <td>" . htmlspecialchars($row['place']) . "</td>
                                <td>" . htmlspecialchars($row['date']) . "</td>
                                <td>" . htmlspecialchars($row['host']) . "</td>
                                <td>" . htmlspecialchars($row['fee']) . "</td>
                                <td>" . htmlspecialchars($row['dueDate']) . "</td>
                                <td>" . htmlspecialchars($currentCount ."/". $row['maxPeople']) . "</td>
                                <td class='intro'>" . htmlspecialchars($row['intro']) . "</td>
                                <td>" . htmlspecialchars($row['remark']) . "</td>
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
</body>
</html>
