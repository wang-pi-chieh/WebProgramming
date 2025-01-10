<?php
// 啟用 Session
session_start();
include 'connection.php';

// 確認是否已登入
if (!isset($_SESSION['user'])) {
    header("Location: studentEnter.php");
    exit();
}

// 從資料庫加載舊資料（若 Session 不完整）
if (!isset($_SESSION['user']['name']) || !isset($_SESSION['user']['major']) || !isset($_SESSION['user']['phone']) || !isset($_SESSION['user']['mail'])) {
    $account = $_SESSION['user']['account'];
    $stmt = $conn->prepare("SELECT * FROM students WHERE account = ?");
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
    $sid = $_SESSION['user']['sid'];         // 確保學號不可被修改
    $originalData = $_SESSION['user'];       // 獲取原始資料

    // 表單中的新資料
    $name = !empty($_POST['name']) ? $_POST['name'] : $originalData['name'];
    $major = !empty($_POST['major']) ? $_POST['major'] : $originalData['major'];
    $phone = !empty($_POST['phone']) ? $_POST['phone'] : $originalData['phone'];
    $mail = !empty($_POST['mail']) ? $_POST['mail'] : $originalData['mail'];
    $MorV = !empty($_POST['MorV']) ? $_POST['MorV'] : $originalData['MorV'];

    // 動態生成 SQL 更新語句
    $fields = [];
    $params = [];
    $types = "";

    if ($name !== $originalData['name']) {
        $fields[] = "name = ?";
        $params[] = $name;
        $types .= "s";
    }
    if ($major !== $originalData['major']) {
        $fields[] = "major = ?";
        $params[] = $major;
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
    if ($MorV !== $originalData['MorV']) {
        $fields[] = "MorV = ?";
        $params[] = $MorV;
        $types .= "s";
    }

    if (count($fields) > 0) {
        $params[] = $account; // 加入帳號作為條件
        $types .= "s";
        $sql = "UPDATE students SET " . implode(", ", $fields) . " WHERE account = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $_SESSION['user'] = array_merge($originalData, [
                'name' => $name,
                'major' => $major,
                'phone' => $phone,
                'mail' => $mail,
                'MorV' => $MorV
            ]);
            echo "<script>
                alert('資料修改成功！');
                window.location.href = 'studentInfo.php';
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

// 顯示表單
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>修改個人資料</title>
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
        input, select, button {
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
    <h1>修改個人資料</h1>
    <form action="studentModify.php" method="post">
        <input type="text" name="account" value="<?php echo htmlspecialchars($_SESSION['user']['account']); ?>" readonly>
        <input type="text" name="sid" value="<?php echo htmlspecialchars($_SESSION['user']['sid']); ?>" readonly>
        <input type="text" name="name" placeholder="姓名" value="<?php echo htmlspecialchars($_SESSION['user']['name']); ?>">
        <input type="text" name="major" placeholder="科系" value="<?php echo htmlspecialchars($_SESSION['user']['major']); ?>">
        <input type="text" name="phone" placeholder="電話" value="<?php echo htmlspecialchars($_SESSION['user']['phone']); ?>">
        <input type="email" name="mail" placeholder="電子郵件" value="<?php echo htmlspecialchars($_SESSION['user']['mail']); ?>">
        <select name="MorV">
            <option value="葷" <?php echo $_SESSION['user']['MorV'] === '葷' ? 'selected' : ''; ?>>葷食</option>
            <option value="素" <?php echo $_SESSION['user']['MorV'] === '素' ? 'selected' : ''; ?>>素食</option>
        </select>
        <button type="submit">保存修改</button>
    </form>
</body>
</html>
