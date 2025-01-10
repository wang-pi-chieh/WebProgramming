<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['userType'] !== 'host') {
    header("Location: whoEnter.html"); // 未登入或非主辦方則跳轉到登入頁面
    exit();
}
$user = $_SESSION['user'];
include 'connection.php';

// 檢查是否有表單提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $place = $_POST['place'];
    $date = $_POST['date'];
    $host = $_POST['host'];
    $fee = $_POST['fee'];
    $dueDate = $_POST['dueDate'];
    $maxPeople = $_POST['maxPeople'];
    $intro = $_POST['intro'];
    $remark = !empty($_POST['remark']) ? $_POST['remark'] : null; // 如果空值則設為 NULL

    // 插入資料到資料庫
    $sql = "INSERT INTO activity (name, place, date, host, fee, dueDate, maxPeople, intro, remark) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssissss", $name, $place, $date, $host, $fee, $dueDate, $maxPeople, $intro, $remark);    if ($stmt->execute()) {
        echo "<script>alert('活動新增成功!'); window.location.href='homeLoginHost.php';</script>";
    } else {
        echo "<script>alert('新增失敗，請稍後再試!');</script>";
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
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            width: 50%;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input{
            width: 95%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        select{
            width: 98%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 98%;
            padding: 10px;
            background-color: rgb(52, 155, 181);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: rgb(48, 129, 163);
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
        新增活動
        <div class="user-menu">
            <span id="user-menu-toggle"><?php echo htmlspecialchars($user['name']); ?> ▼</span>
            <ul>
                <li><a href="hostInformation.php">主辦方資訊</a></li>
                <li><a href="home.php">登出</a></li>
            </ul>
        </div>
    </div>

    <h1>新增活動場次</h1>
    <form method="POST" action="">
        <label for="name">活動名稱</label>
        <input type="text" id="name" name="name" required>

        <label for="type">活動地點</label>
         <!--<input type="text" id="place" name="活動地點" required>-->

        <select name="place" required>
            <option value="" disabled selected>選擇活動地點</option>
            <option value="B01-101">B01-101</option>
            <option value="B01-102">B01-102</option>
            <option value="B01-103">B01-103</option>
            <option value="B01-105">B01-105</option>
            <option value="B01-110">B01-110</option>
            <option value="B01-201">B01-201</option>
            <option value="B01-202">B01-202</option>
            <option value="B01-203">B01-203</option>
            <option value="B01-204">B01-204</option>
            <option value="C01-105">C01-105</option>
            <option value="C01-200">C01-200</option>
            <option value="C01-201">C01-201</option>
            <option value="C01-202">C01-202</option>
            <option value="C01-203">C01-203</option>
            <option value="C01-204">C01-204</option>
            <option value="C01-208">C01-208</option>
            <option value="C02-104">C02-104</option>
            <option value="C02-105">C02-105</option>
            <option value="C02-301">C02-301</option>
            <option value="C02-302">C02-302</option>
            <option value="K01-103">K01-103</option>
            <option value="K01-104">K01-104</option>
            <option value="L01-103">L01-103</option>
            <option value="L01-2F大廳">L01-2F大廳</option>
            <option value="L02-101">L02-101</option>
            <option value="H1-H2-105">H1-H2-105</option>
            <option value="H1-H2-200">H1-H2-200</option>
            <option value="H1-H2-201">H1-H2-201</option>
            <option value="H1-H2-206">H1-H2-206</option>
            <option value="H1-H2-207">H1-H2-207</option>
            <option value="H1-H2-208">H1-H2-208</option>
            <option value="學生活動中心">學生活動中心</option>
            <option value="洪四川籃球場">洪四川籃球場</option>
            <option value="第一排球場">第一排球場</option>
        </select>

        <label for="maxPeople">活動日期</label>
        <input type="text" id="date" name="date" placeholder="YYYY-MM-DD" required>

        <label for="host">主辦方</label>
        <input type="text" id="host" name="host" value="<?php echo htmlspecialchars($user['name']); ?>" readonly>

        <label for="region">活動費用</label>
        <input type="text" id="fee" name="fee" required>

        <label for="startTime">報名截止時間</label>
        <input type="text" id="dueDate" name="dueDate" placeholder="YYYY-MM-DD" required>

        <label for="endTime">報名人數上限</label>
        <input type="text" id="maxPeople" name="maxPeople" required>

        <label for="endTime">活動介紹</label>
        <input type="text" id="intro" name="intro" required>

        <label for="endTime">備註</label>
        <input type="text" id="remark" name="remark">
        <button type="submit">儲存</button>
    </form>
</body>
</html>