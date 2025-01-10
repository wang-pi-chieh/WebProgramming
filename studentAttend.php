<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: whoEnter.php"); // 未登入則跳轉到登入頁面
    exit();
}

$user = $_SESSION['user'];
$sid = $user['sid']; // 假設 Session 中包含學號 sid
include('connectionSession.php');
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NUK 已報名活動</title>
    <style>
        body {
            background-color: rgba(156,187,179,1);
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
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

        main {
            background-color: #fffacd;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .activity h3 {
            margin-top: 0;
            color: #333;
        }

        .activity p {
            margin: 5px 0;
        }

        .cancel-btn {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
        }

        .cancel-btn:hover {
            background-color: #ff1a1a;
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
        main {
            background-color: rgba(156,187,179,1);
            padding: 20px;
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

        //取消報名功能
        function cancelRegistration(activityName) {
            if (confirm('確定要取消報名嗎？')) {
                fetch('cancel.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ activityName: activityName })
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('取消報名時發生錯誤，請稍後再試！');
                });
            }
        }
    </script>
</head>
<body>
    <header>
    <a href="homeLogin.php">
            <img src="logo.png" alt="網站Logo" class="logo">
        </a>
        NUK 已報名活動
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
        <div class="activity-list">
            <?php
            // 查詢登入者已報名的活動及其詳細資訊
            $stmt = $conn->prepare(
                "SELECT a.name, a.place, a.date, a.host 
                 FROM participants p 
                 JOIN activity a ON p.activity = a.name 
                 WHERE p.sid = ?"
            );
            if ($stmt === false) {
                die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
            }

            $stmt->bind_param("s", $sid);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='activity'>";
                    echo "<div>";
                    echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
                    echo "<p><strong>地點：</strong>" . htmlspecialchars($row['place']) . "</p>";
                    echo "<p><strong>舉辦時間：</strong>" . htmlspecialchars($row['date']) . "</p>";
                    echo "<p><strong>主辦方：</strong>" . htmlspecialchars($row['host']) . "</p>";
                    echo "</div>";
                    echo "<button class='cancel-btn' onclick=\"cancelRegistration('" . htmlspecialchars($row['name']) . "')\">取消報名</button>";
                    echo "</div>";
                }
            } else {
                echo "<p>您尚未報名任何活動。</p>";
            }

            $stmt->close();
            $conn->close();
            ?>
        </div>
    </main>
</body>
</html>
