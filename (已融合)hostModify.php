<?php
// 啟用 Session
session_start();
include 'connection.php';

// 確認是否已登入
if (!isset($_SESSION['user'])) {
    header("Location: hostEnter.php");
    exit();
}

// 從資料庫加載舊資料（若 Session 不完整）
if (!isset($_SESSION['user']['name']) || !isset($_SESSION['user']['major']) || !isset($_SESSION['user']['phone']) || !isset($_SESSION['user']['mail'])) {
    $account = $_SESSION['user']['account'];
    $stmt = $conn->prepare("SELECT * FROM host WHERE account = ?");
    $stmt->bind_param("s", $account);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
        $_SESSION['user'] = $userData; // 將資料庫資料存入 Session
    } else {
        echo "找不到該使用者的資料！";
        exit();
    }
}

// 確認是否提交表單
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account = $_SESSION['user']['account']; // 確保帳號不可被修改
    $originalData = $_SESSION['user']; // 獲取原始資料

    // 檢查並處理每個表單欄位
    // 如果用戶沒有輸入新的資料，則保留原始資料
    $hostName = !empty($_POST['host']) ? $_POST['host'] : $originalData['host'];
    $office = !empty($_POST['office']) ? $_POST['office'] : $originalData['office'];
    $phone = !empty($_POST['phone']) ? $_POST['phone'] : $originalData['phone'];
    $mail = !empty($_POST['mail']) ? $_POST['mail'] : $originalData['mail'];

    // 動態生成 SQL 更新語句
    $fields = [];
    $params = [];
    $types = "";

    // 檢查是否修改其他欄位
    if ($office !== $originalData['office']) {
        $fields[] = "office = ?";
        $params[] = $office;
        $types .= "s";
    }
    if ($phone !== $originalData['phone']) {
        $fields[] = "phone = ?";
        $params[] = $phone;
        $types .= "s";
    }
    if ($mail !== $originalData['mail']) {
        $fields[] = "mail = ?";
        $params[] = $mail;
        $types .= "s";
    }

    // 確認有字段需要更新
    if (count($fields) > 0) {
        $params[] = $account; // 加入帳號作為條件
        $types .= "s";
        $sql = "UPDATE host SET " . implode(", ", $fields) . " WHERE account = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            // 更新成功，重新載入最新的資料到 Session
            $_SESSION['user'] = array_merge($originalData, [
                'host' => $hostName,
                'office' => $office,
                'phone' => $phone,
                'mail' => $mail
            ]); // 更新 Session 資料
            echo "<script>
                alert('資料修改成功！');
                window.location.href = 'hostInformation.php';
            </script>";
            exit();
        } else {
            echo "修改失敗：" . $conn->error;
        }

        $stmt->close();
    } else {
        echo "<script>alert('資料未變更！'); history.back();</script>";
    }
}

// 如果是 GET 請求，顯示表單
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>修改主辦方資料</title>
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
            width: 300px;
        }
        input, button {
            margin: 10px 0;
            padding: 10px;
            font-size: 16px;
        }
        input[readonly] {
            background-color: #e9ecef;
            cursor: not-allowed;
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
    <h1>修改主辦方資料</h1>
    <form action="hostModify.php" method="post">
        <input type="text" name="account" value="<?php echo htmlspecialchars($_SESSION['user']['account']); ?>" readonly>
        <!-- 設置 host 欄位為只讀 -->
        <input type="text" name="host" placeholder="主辦方名稱" value="<?php echo htmlspecialchars($_SESSION['user']['host']); ?>" readonly>
        <input type="text" name="office" placeholder="辦公室" value="<?php echo htmlspecialchars($_SESSION['user']['office']); ?>">
        <input type="text" name="phone" placeholder="電話" value="<?php echo htmlspecialchars($_SESSION['user']['phone']); ?>">
        <input type="email" name="mail" placeholder="電子郵件" value="<?php echo htmlspecialchars($_SESSION['user']['mail']); ?>">
        <button type="submit">保存修改</button>
    </form>
</body>
</html>
