<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登入</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        header {
            background-color: #143848;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);

        }

        main {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80vh;
        }

        .login-container {
            display: flex;
            gap: 40px;
        }

        .login-box {
            width: 400px;
            height: 450px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.2s;
        }

        .login-box:hover {
            transform: scale(1.05);
        }

        .login-box img {
            width: 250px;
            height: 250px;
            margin-bottom: 30px;
            border-radius: 50%;
        }

        .login-box button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background-color:rgb(54, 181, 209);
            color: white;
            transition: background-color 0.3s;
        }

        .login-box button:hover {
            background-color:rgb(38, 149, 177);
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
    <header>
        選擇登入
    </header>
    <main>
        <div class="login-container">
            <div class="login-box">
                <img src="host.jpg" alt="主辦方圖示">
                <button onclick="location.href='hostEnter.php'">主辦方登入</button>
            </div>
            <div class="login-box">
                <img src="students.jpg" alt="報名者圖示">
                <button onclick="location.href='studentEnter.php'">報名者登入</button>
            </div>
        </div>
    </main>
</body>
</html>
