<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>學生註冊</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            height: 100vh;
            background: url('background.jpg') no-repeat center center fixed; /* 背景圖像 */
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden; /* 防止出現滾動條 */
        }
        .overlay {
            background-color: rgba(255, 255, 255, 0.8); /* 半透明白色覆蓋層 */
            width: 60%; /* 調整為頁面右半邊 */
            height: 102%;
            position: absolute;
            right: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            animation: slide-in 0.8s ease-out; /* 添加滑入動畫 */
            padding: 40px;
            border-radius: 8px; /* 圆角边框 */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* 添加阴影 */
        }
        @keyframes slide-in {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        h1 {
            font-size: 3.5rem;
            margin-bottom: 10px;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #fff; /* 表單背景色 */
            padding: 40px;
            border-radius: 8px; /* 圆角边框 */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* 添加阴影 */
            width: 100%;
            max-width: 400px;
        }
        input, select {
            margin: 10px 0;
            padding: 10px;
            font-size: 17px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 4px; /* 圆角输入框 */
            box-sizing: border-box; /* 确保宽度一致 */
        }
        button {
            width: 100%; /* 按钮与输入框同宽 */
            margin: 10px 0;
            padding: 10px 20px;
            font-size: 19px;
            background-color: rgb(54, 181, 209);
            color: white;
            border: none;
            border-radius: 4px; /* 圆角按钮 */
            cursor: pointer;
            transition: background-color 0.3s ease; /* 添加平滑过渡效果 */
        }
        button:hover {
            background-color: rgb(38, 149, 177); /* 鼠标悬停效果 */
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
        <a href="home.php">
            <img src="logo.png" alt="網站Logo" class="logo">
        </a>
    <div class="overlay">
        <h1>學生註冊</h1>
        <form action="studentRegister.php" method="post">
            <input type="text" name="account" placeholder="帳號" required>
            <input type="password" name="password" placeholder="密碼" required>
            <input type="text" name="name" placeholder="姓名" required>
            <input type="text" name="major" placeholder="科系" required>
            <input type="text" name="sid" placeholder="學號" required>
            <input type="text" name="phone" placeholder="電話" required>
            <input type="email" name="mail" placeholder="電子郵件" required>
            <select name="MorV" required>
                <option value="" disabled selected>請選擇飲食習慣</option>
                <option value="葷">葷食</option>
                <option value="素">素食</option>
            </select>
            <button type="submit">註冊</button>
        </form>
    </div>
    <?php
    // Include database connection if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        include('connectionSession.php');

        // 接收表單數據
        $account = $_POST['account'];
        $password = $_POST['password'];
        $name = $_POST['name'];
        $major = $_POST['major'];
        $sid = $_POST['sid'];
        $phone = $_POST['phone'];
        $mail = $_POST['mail'];
        $MorV = $_POST['MorV'];

        // 檢查帳號是否已存在
        $checkSql = "SELECT * FROM students WHERE account = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $account);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>
                alert('此帳號已被註冊');
                window.location.href = 'studentRegister.php'; // 將用戶返回到註冊頁面
            </script>";
        } else {
            // 新增到資料庫
            $sql = "INSERT INTO students (account, password, name, major, sid, phone, mail, MorV) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssss", $account, $password, $name, $major, $sid, $phone, $mail, $MorV);

            if ($stmt->execute()) {
                echo "<script>
                    alert('註冊成功，請重新登入！');
                    window.location.href = 'studentEnter.php';
                </script>";
            } else {
                echo "<script>
                    alert('註冊失敗，請稍後再試！');
                    window.location.href = 'studentRegister.php';
                </script>";
            }
            $stmt->close();
        }

        $checkStmt->close();
        $conn->close();
        exit; // Prevent further processing of the page after form submission
    }
    ?>
</body>
</html>
