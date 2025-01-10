<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['userType'] !== 'host') {
    header("Location: whoEnter.html");
    exit();
}
include 'connection.php';

$user = $_SESSION['user'];
$hostName = $user['name']; 

// ※ 只撈取 host = 當前登入者 的活動
$sql = "SELECT 
            a.name AS activity_name,
            a.maxPeople,
            COUNT(p.sid) AS currentPeople,
            SUM(CASE WHEN s.MorV = '葷' THEN 1 ELSE 0 END) AS meat_eaters,
            SUM(CASE WHEN s.MorV = '素' THEN 1 ELSE 0 END) AS vegetarians
        FROM activity a
        LEFT JOIN participants p ON a.name = p.activity
        LEFT JOIN students s ON p.sid = s.sid
        WHERE a.host = ?
        GROUP BY a.name";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $hostName);
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
    die("SQL Error: " . $conn->error);
}
$activities = $result->fetch_all(MYSQLI_ASSOC);

// 取得活動報名者詳細資訊 (POST 請求)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['activity'])) {
    $activityName = $_POST['activity'];
    
    // 這裡同樣要檢查該 activity 是否歸屬於當前登入 host
    // 以免有人惡意改動前端參數。
    $sql = "SELECT 
                s.sid, 
                s.name, 
                s.major, 
                s.phone, 
                s.mail, 
                s.MorV 
            FROM participants p
            JOIN students s ON p.sid = s.sid
            JOIN activity a ON a.name = p.activity
            WHERE p.activity = ? 
              AND a.host = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $activityName, $hostName);
    $stmt->execute();
    $details = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    echo json_encode($details);
    exit();
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>活動管理介面</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            align-items: center; /* 垂直置中 */
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
        .activity-table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 8px;
            overflow: hidden;
            text-align: center; /* 表格內文字水平居中 */

        }
        .activity-table th, .activity-table td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ccc;
            vertical-align: middle;
        }
        .activity-table th {
            background-color: #143848;
            color: white;
            font-weight: bold;
        }
        .chart-container {
            width: 200px;
            height: 200px;
            margin: 0 auto;
        }
        .chart-container .legend {
            text-align: left; /* 讓文字靠左對齊 */
            margin-top: 10px; /* 加一些間距 */
            font-size: 14px; /* 調整字體大小 */
        }
        .chart-container .legend span {
            display: inline-block; /* 確保色塊和文字水平對齊 */
            width: 20px; /* 色塊寬度 */
            height: 10px; /* 色塊高度 */
            margin-right: 5px; /* 色塊與文字間距 */
            vertical-align: middle; /* 垂直對齊 */
        }

        
        .hidden-row {
            display: none;
            text-align: center; /* 表格內文字水平居中 */
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
        .details-table {
            margin: 0 auto; /* 水平居中 */
            border: 1px solid #ccc;
            border-collapse: collapse;
            width: 100%; /* 表格寬度 */
            background: #fff; /* 表格背景色 */
            text-align: center;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); /* 可選，添加陰影效果 */
        }

        .details-table th, .details-table td {
            text-align: center; /* 文字水平居中 */
            padding: 10px;
            border: 1px solid #ddd;
        }
        .table-container {
            display: flex; /* 啟用 Flexbox */
            justify-content: center; /* 水平居中 */
            align-items: center; /* 垂直居中 */
            height: 100%; /* 父容器高度填滿 */
            width: 100%; /* 父容器寬度填滿 */
            padding: 20px; /* 可選：增加內邊距 */
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
        確認參與者資料
        <div class="user-menu">
            <span id="user-menu-toggle"><?php echo htmlspecialchars($user['name']); ?> ▼</span>
            <ul>
                <li><a href="hostInformation.php">主辦方資訊</a></li>
                <li><a href="home.php">登出</a></li>
            </ul>
        </div>
    </div>

    <table class="activity-table">
        <thead>
            <tr>
                <th>活動名稱</th>
                <th>報名人數</th>
                <th>報名進度</th>
                <th>飲食習慣</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$activities || count($activities) === 0): ?>
                <tr>
                    <td colspan="4">目前沒有任何活動資料。</td>
                </tr>
            <?php else: ?>
                <?php foreach ($activities as $index => $activity): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($activity['activity_name']); ?></td>
                        <td>
                            <?php echo $activity['currentPeople']; ?> / <?php echo $activity['maxPeople']; ?>
                        </td>
                        <td>
                            <div class="chart-container">
                                <canvas id="chart-<?php echo $index; ?>"></canvas>
                            </div>
                        </td>
                        <td>
                            <!-- 新增葷/素數量 -->
                            葷食: <?php echo $activity['meat_eaters']; ?><br>
                            素食: <?php echo $activity['vegetarians']; ?>
                        </td>
                        <td>
                            <button onclick="toggleDetails(this, '<?php echo htmlspecialchars($activity['activity_name']); ?>')">查看報名者</button>
                        </td>
                    </tr>
                    <tr class="hidden-row">
                        <td colspan="5">
                            <div class="table-container">
                                <table style="width: 100%; border-collapse: collapse;" class="details-table">
                                    <thead>
                                        <tr>
                                            <th>學號</th>
                                            <th>姓名</th>
                                            <th>科系</th>
                                            <th>電話</th>
                                            <th>信箱</th>
                                            <th>飲食習慣</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    <script>
                        // 確保 Chart.js 正確初始化
                        document.addEventListener("DOMContentLoaded", function() {
                            const ctx = document.getElementById('chart-<?php echo $index; ?>').getContext('2d');
                            new Chart(ctx, {
                                type: 'doughnut',
                                data: {
                                    labels: ['已報名', '剩餘名額'],
                                    datasets: [{
                                        data: [
                                            <?php echo $activity['currentPeople']; ?>, 
                                            <?php echo $activity['maxPeople'] - $activity['currentPeople']; ?>
                                        ],
                                        backgroundColor: ['pink', 'lightgray']
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: {
                                            display: true,
                                            position: 'bottom'
                                        }
                                    }
                                }
                            });
                        });
                    </script>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
        // 切換顯示/隱藏報名者資料
        function toggleDetails(button, activityName) {
            const hiddenRow = button.closest('tr').nextElementSibling;
            const tableBody = hiddenRow.querySelector('tbody');

            if (hiddenRow.style.display === 'table-row') {
                hiddenRow.style.display = 'none';
            } else {
                hiddenRow.style.display = 'table-row';
                if (tableBody.children.length > 0) return;

                fetch("", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `activity=${encodeURIComponent(activityName)}`
                })
                .then(response => response.json())
                .then(data => {
                    tableBody.innerHTML = "";
                    if (data.length === 0) {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `<td colspan="6">目前無報名者</td>`;
                        tableBody.appendChild(tr);
                    } else {
                        data.forEach(row => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${row.sid}</td>
                                <td>${row.name}</td>
                                <td>${row.major}</td>
                                <td>${row.phone}</td>
                                <td>${row.mail}</td>
                                <td>${row.MorV}</td>
                            `;
                            tableBody.appendChild(tr);
                        });
                    }
                });
            }
        }
    </script>
</body>
</html>



