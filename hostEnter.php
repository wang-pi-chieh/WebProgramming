<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> <!-- 引入 Font Awesome -->
    <title>Host Enter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            height: 100vh;
            background: url('background.jpg') no-repeat center center fixed; /* 背景圖像 */
            background-size: cover;
            display: flex;
            flex-direction: column;
        }
        .overlay {
            background-color: rgba(255, 255, 255, 0.8); /* 半透明白色覆蓋層 */
            width: 60%; /* 調整為頁面右半邊 */
            height: 100%;
            position: absolute;
            right: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            animation: slide-in 0.8s ease-out; /* 添加滑入動畫 */
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
            margin-bottom: 15px;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #fff; /* 表單背景色 */
            padding: 70px;
            border-radius: 8px; /* 圆角边框 */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* 添加阴影 */
            width: 100%;
            max-width: 400px;
        }
        input {
            margin: 10px 0;
            padding: 10px;
            font-size: 19px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 4px; /* 圆角输入框 */
        }
        .password-container {
            position: relative;
            width: 105%;
        }
        .password-container input {
            width: 94.5%;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }
        button {
            width: 105%; /* 按钮与输入框同宽 */
            margin: 10px 0;
            padding: 10px 20px;
            font-size: 19px;
            background-color: rgb(38, 149, 177);
            color: white;
            border: none;
            border-radius: 4px; /* 圆角按钮 */
            cursor: pointer;
            transition: background-color 0.3s ease; /* 添加平滑过渡效果 */
        }
        button:hover {
            background-color: rgb(54, 181, 209); /* 鼠标悬停效果 */
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
    <!-- 按logo會回主頁 
        .logo {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 100px;
            height: auto;
            cursor: pointer;
        }   
    -->
</head>
<body>
    <a href="home.php"><img src="logo.png" alt="Logo" class="logo"></a> <!-- 左上角的Logo -->
    <!-- 按logo會回主頁 
    <a href="home.php"><img src="logo.png" alt="Logo" class="logo"></a> 
    -->

    <div class="overlay">
        <h1>主辦方登入</h1>
        <form action="Login.php" method="post">
            <input type="hidden" name="userType" value="host">
            <input type="text" name="account" placeholder="帳號" required>
            <div class="password-container">
                <input type="password" name="password" placeholder="密碼" required id="password">
                <i class="fas fa-eye-slash toggle-password" id="togglePassword" onclick="togglePasswordVisibility()"></i>
            </div>
            <!-- <input type="password" name="password" placeholder="密碼" required> -->
            <button type="submit">登入</button>
        </form>
    </div>
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePassword');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            }
        }
    </script>
</body>
</html>
