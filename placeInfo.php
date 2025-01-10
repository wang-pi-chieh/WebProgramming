<?php
session_start();

// 防止會話固定攻擊
if (!isset($_SESSION['user'])) {
    header("Location: whoEnter.html"); // 未登入則跳轉到登入頁面
    exit();
}

$user = $_SESSION['user'];
$sid = htmlspecialchars($user['sid'], ENT_QUOTES, 'UTF-8'); // 確保輸出安全
$name = htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); // 確保輸出安全

include('connectionSession.php');

// 使用準備好的語句查詢主辦方資訊
$stmt = $conn->prepare("SELECT host, office, phone, mail FROM host");
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>地點資訊</title>
    <!-- 引入 Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* 全局樣式 */
        body {
            background-color: rgba(156,187,179,1);
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        header {
            background-color: #143848;
            color: white;
            text-align: center;
            padding: 20px 0;
            font-size: 24px;
            font-weight: bold;
            position: relative;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }
        .logo {
            position: absolute;
            top: 10px;
            left: 10px;
            width: 100px;
            height: 50px;
            object-fit: cover;
            cursor: pointer;
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
            top: 35px;
            right: 0;
            background-color: white;
            list-style: none;
            padding: 0;
            margin: 0;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            z-index: 1001;
            min-width: 150px;
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
        main {
            flex: 1;
            display: flex;
            flex-direction: row;
            overflow: hidden;
        }
        .map-container {
            flex: 7;
            padding: 10px;
            position: sticky;
            top: 0;
            overflow: hidden;
        }
        .map-container img {
            width: 100%;
            height: 110%;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .table-container {
            flex: 3;
            padding: 10px;
            overflow-y: auto;
            border-left: 2px solid #ccc;
            background-color: rgba(255, 255, 255, 0.8);
        }
        .table-container h3 {
            margin-bottom: 20px;
            color: #143848;
        }
        .table-container table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }
        .table-container th {
            background-color: rgb(148, 195, 166);
            color: white;
            padding: 10px;
            border: 1px solid #ddd;
        }
        .table-container td {
            background-color: rgb(218, 236, 222);
            padding: 10px;
            border: 1px solid #ddd;
        }
        @media (max-width: 768px) {
            main {
                flex-direction: column;
            }
            .map-container,
            .table-container {
                flex: 1;
                height: 50vh;
                position: relative;
            }
            .map-container {
                position: relative;
            }
        }
    </style>
</head>
<body>
    <header>
        <a href="homeLogin.php">
            <img src="logo.png" alt="網站Logo" class="logo">
        </a>
        地點資訊
        <div class="user-menu">
            <span id="user-menu-toggle"><?php echo $name; ?> ▼</span>
            <ul>
                <li><a href="activityInfo.php">活動詳情</a></li>
                <li><a href="studentAttend.php">已報名資訊</a></li>
                <li><a href="hostInfo.php">活動方資訊</a></li>
                <li><a href="studentInfo.php">個人資訊</a></li>
                <li><a href="home.php">登出</a></li>
            </ul>
        </div>
    </header>
    <main>
        <!-- 左側地圖 -->
        <div class="map-container">
            <img src="NUKmap.png" alt="NUK Map">
        </div>

        <!-- 資料表 -->
        <div class="table-container">
            <h3>地點資訊</h3>
            <?php 
            // SQL 查詢
        $query = "SELECT * FROM place";
        $query_run = $conn->query($query);

        if ($query_run && $query_run->num_rows > 0) {
        ?>
            <table>
                <thead>
                    <tr>
                        <th>地點</th>
                        <th>院別</th>
                        <th>樓層</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $query_run->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['place']) . "</td>
                                <td>" . htmlspecialchars($row['department']) . "</td>
                                <td>" . htmlspecialchars($row['floor']) . "</td>
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

    <!-- 引入 Bootstrap JS 和依賴 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // 工具欄功能
        document.addEventListener('DOMContentLoaded', () => {
            const toggleButton = document.getElementById('user-menu-toggle');
            const menu = document.querySelector('.user-menu ul');

            toggleButton.addEventListener('click', (e) => {
                e.stopPropagation(); // 防止事件冒泡
                menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
            });

            // 點擊其他區域時收回工具欄
            document.addEventListener('click', (e) => {
                if (!toggleButton.contains(e.target) && !menu.contains(e.target)) {
                    menu.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
<?php
// 關閉資料庫連接
$stmt->close();
$conn->close();
?>