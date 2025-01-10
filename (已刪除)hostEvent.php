<?php
// Include database connection and handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include('connectionSession.php');
    // 接收表單數據
    $name = $_POST['name'];
    $place = $_POST['place'];
    $date = $_POST['date']; // date 作為 VARCHAR
    $host = $_POST['host'];
    $fee = $_POST['fee'];
    $dueDate = $_POST['dueDate']; // dueDate 作為 VARCHAR
    $maxPeople = $_POST['maxPeople'];
    $intro = $_POST['intro'];
    $remark = $_POST['remark'];

    // 修改表名稱為正確的名稱，如 activity
    $sql = "INSERT INTO activity (name, place, date, host, fee, dueDate, maxPeople, intro, remark) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // 檢查 $stmt 是否有效
    if (!$stmt) {
        die("SQL 語法準備失敗: " . $conn->error);
    }

    // 綁定參數並執行
    $stmt->bind_param("ssssisiss", $name, $place, $date, $host, $fee, $dueDate, $maxPeople, $intro, $remark);

    if ($stmt->execute()) {
        echo "<script>
            alert('活動已成功提交！');
            window.location.href = 'FrontPage.html';
        </script>";
    } else {
        echo "活動提交失敗：" . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    exit; // Prevent further processing of the page after form submission
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>活動填寫</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        form {
            display: flex;
            flex-direction: column;
            width: 400px;
        }
        input, select, textarea, button {
            margin: 10px 0;
            padding: 10px;
            font-size: 16px;
        }
        button {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>活動填寫</h1>
    <form action="" method="post">
        <input type="text" name="name" placeholder="活動名稱" required>
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
        <label for="date">活動日期 (格式: yyyy-mm-dd):</label>
        <input type="text" id="date" name="date" placeholder="yyyy-mm-dd" required>
        <label for="dueDate">報名截止日期 (格式: yyyy-mm-dd):</label>
        <input type="text" id="dueDate" name="dueDate" placeholder="yyyy-mm-dd" required>
        <input type="text" name="host" placeholder="主辦單位" required>
        <input type="number" name="fee" placeholder="費用 (數字)" required>
        <input type="number" name="maxPeople" placeholder="人數上限 (數字)" required>
        <textarea name="intro" placeholder="活動介紹" rows="5" required></textarea>
        <textarea name="remark" placeholder="備註 (選填)" rows="3"></textarea>
        <button type="submit">提交活動</button>
    </form>
</body>
