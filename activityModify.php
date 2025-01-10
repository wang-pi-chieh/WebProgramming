<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['userType'] !== 'host') {
    header("Location: whoEnter.html"); // 未登入或非主辦方則跳轉到登入頁面
    exit();
}
$user = $_SESSION['user'];
include 'connection.php';

// 只查詢當前登入主辦方所屬活動 (建議這樣做，更安全)
$sql = "SELECT * FROM activity WHERE host = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user['name']);
$stmt->execute();
$result = $stmt->get_result();
$activities = $result->fetch_all(MYSQLI_ASSOC);

// 檢查是否有表單提交
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name'])) {
    $name = $_POST['name'];
    $place = $_POST['place'];
    $date = $_POST['date'];
    $host = $user['name'];
    $fee = $_POST['fee'];
    $dueDate = $_POST['dueDate'];
    $maxPeople = $_POST['maxPeople'];
    $intro = $_POST['intro'];
    $remark = !empty($_POST['remark']) ? $_POST['remark'] : null; // 如果空值則設為 NULL


    // 更新前先檢查：該活動是否確實屬於當前主辦方
    // 以避免前端有人惡意傳遞不屬於自己的 activityId
    $checkSql = "SELECT name FROM activity WHERE name = ? AND host = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("ss", $name, $user['name']);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows === 0) {
        // 活動不屬於該主辦方，或活動不存在
        echo "<script>alert('無權限修改該活動!'); window.location.href='homeLoginHost.php';</script>";
        exit();
    }

    // 若確認活動是自己的，才執行更新
    $updateSql = "UPDATE activity 
                  SET place=?, date=?, fee=?, dueDate=?, maxPeople=?, intro=?, remark=? 
                  WHERE name=? AND host=?";
    $updateStmt = $conn->prepare($updateSql);
        // 根據變數的實際類型設置類型字串
    // 假設 fee 和 maxPeople 為整數，其餘為字串
    $updateStmt->bind_param("ssiisssss", 
        $place,   // s
        $date,    // s
        $fee,     // s (如果是數字，改為 "i" 或 "d")
        $dueDate, // s
        $maxPeople, // i
        $intro,   // s
        $remark,  // s
        $name,    // s
        $host     // s
    );

    if ($updateStmt->execute()) {
        if ($updateStmt->affected_rows > 0) {
            echo "<script>alert('活動修改成功!'); window.location.href='homeLoginHost.php';</script>";
        } else {
            echo "<script>alert('修改失敗：未找到匹配的記錄!');</script>";
        }
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
    <title>NUK 主辦方活動管理</title>
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
        .accordion {
            width: 90%;
            margin: 20px auto;
        }
        .accordion-item {
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #f9f9f9;
        }
        .accordion-header {
            padding: 15px;
            cursor: pointer;
            background-color: #143848;
            color: white;
            font-weight: bold;
            border-radius: 4px 4px 0 0;
        }
        .accordion-body {
            display: none;
            padding: 20px;
            background-color: white;
            border-top: 1px solid #ccc;
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
        修改活動
        <div class="user-menu">
            <span id="user-menu-toggle"><?php echo htmlspecialchars($user['name']); ?> ▼</span>
            <ul>
                <li><a href="hostInformation.php">主辦方資訊</a></li>
                <li><a href="home.php">登出</a></li>
            </ul>
        </div>
    </div>

    <!-- 活動清單 -->
    <div class="accordion">
        <?php foreach ($activities as $activity) : ?>
            <div class="accordion-item">
                <div class="accordion-header" onclick="toggleAccordion(this)">
                    <?php echo htmlspecialchars($activity['name']); ?>
                </div>
                <div class="accordion-body">
                    <form method="POST" action="">
                        <label for="name">活動名稱</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($activity['name']); ?>" required>

                        <label for="place">活動地點</label>
                        <input type="text" id="place" name="place" value="<?php echo htmlspecialchars($activity['place']); ?>" required>

                        <label for="date">活動日期</label>
                        <input type="text" id="date" name="date" value="<?php echo htmlspecialchars($activity['date']); ?>" required>

                        <label for="fee">活動費用</label>
                        <input type="text" id="fee" name="fee" value="<?php echo htmlspecialchars($activity['fee']); ?>" required>

                        <label for="dueDate">報名截止時間</label>
                        <input type="text" id="dueDate" name="dueDate" value="<?php echo htmlspecialchars($activity['dueDate']); ?>" required>

                        <label for="maxPeople">報名人數上限</label>
                        <input type="text" id="maxPeople" name="maxPeople" value="<?php echo htmlspecialchars($activity['maxPeople']); ?>" required>

                        <label for="intro">活動介紹</label>
                        <input type="text" id="intro" name="intro" value="<?php echo htmlspecialchars($activity['intro']); ?>" required>

                        <label for="remark">備註</label>
                        <input type="text" id="remark" name="remark" value="<?php echo htmlspecialchars($activity['remark']); ?>">

                        <button type="submit">儲存修改</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        function toggleAccordion(header) {
            const body = header.nextElementSibling;
            if (body.style.display === "block") {
                body.style.display = "none";
            } else {
                body.style.display = "block";
            }
        }
    </script>
</body>
</html>


