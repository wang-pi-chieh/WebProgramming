<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NUK 活動報名系統</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: rgba(156,187,179,1);
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

        .login-btn {
            position: absolute;
            top: 20px;
            right: 25px;
            color: white;
            text-decoration: none;
            font-weight: bold;
            font-size: 20px;
            z-index: 1000;
        }

        .carousel-container {
            position: relative;
            width: 75%;
            height: 30vh; /* 修改高度為視窗的 30% */
            overflow: hidden;
            margin: 0 auto;
            background-color: rgba(189, 212, 206, 0.5); /* 設定背景色 */
            z-index: 0;
        }

        .carousel {
            position: relative;
            width: 100%;
            height: 100%;
            background-color: rgba(189, 212, 206, 0.5); /* 設定背景色 */

        }

        .carousel img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }

        .carousel img.active {
            display: block;
        }

        .carousel-buttons {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
        }

        .carousel-buttons button {
            width: 10px;
            height: 10px;
            border: none;
            border-radius: 50%;
            background-color: #ddd;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .carousel-buttons button.active {
            background-color: #333;
        }

        .carousel-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            cursor: pointer;
            padding: 10px;
            font-size: 20px;
            z-index: 1;
        }

        .carousel-arrow.left {
            left: 10px;
        }

        .carousel-arrow.right {
            right: 10px;
        }

        main {
            background-color: rgba(156,187,179,1);
            padding: 20px;
        }

        .container {
            display: flex;
            justify-content: space-around;
            align-items: center;
            width: 100%;
            height: 300px;
            overflow: hidden;
            gap: 10px; /* 增加圖片間距 */
            margin-top: 20px;
        }

        .major {
            flex: 1;
            height: 100%;
            overflow: hidden;
            position: relative;
            transition: flex 0.6s ease; /* 放慢動畫速度 */
            cursor: pointer;
        }

        .major img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: saturate(1.5);
            transition: filter 0.6s ease; /* 放慢動畫速度 */
        }

        .major:hover {
            flex: 4;
        }

        .major:not(:hover) {
            flex: 1;
        }

        .major:hover img {
            filter: saturate(1);
        }

        .major .details {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background: rgba(189, 212, 206, 0.5);
            color: white;
            text-align: center;
            padding: 10px 0;
            font-size: 16px;
            opacity: 0;
            transform: translateY(100%);
            transition: all 0.6s ease; /* 放慢動畫速度 */
        }

        .major:hover .details {
            opacity: 1;
            transform: translateY(0);
        }

        .major .details a {
            color: rgb(29, 40, 95);
            text-decoration: none;
            font-weight: bold;
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
    //輪播
    document.addEventListener('DOMContentLoaded', () => {
        const images = document.querySelectorAll('.carousel img');
        const buttons = document.querySelectorAll('.carousel-buttons button');
        const leftArrow = document.querySelector('.carousel-arrow.left');
        const rightArrow = document.querySelector('.carousel-arrow.right');
        let currentIndex = 0;

        const updateCarousel = (index) => {
            images.forEach((img, i) => {
                img.classList.toggle('active', i === index);
                buttons[i].classList.toggle('active', i === index);
            });
        };

        const nextImage = () => {
            currentIndex = (currentIndex + 1) % images.length;
            updateCarousel(currentIndex);
        };

        const prevImage = () => {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            updateCarousel(currentIndex);
        };

        buttons.forEach((button, index) => {
            button.addEventListener('click', () => {
                currentIndex = index;
                updateCarousel(currentIndex);
            });
        });

        rightArrow.addEventListener('click', nextImage);
        leftArrow.addEventListener('click', prevImage);

        // 自動輪播功能
        setInterval(nextImage, 10000); // 每 10 秒自動切換
    });

    //debug
    function alertAndRedirect(event) {
        event.preventDefault(); // 防止預設行為
        alert("請先登入！");
        setTimeout(() => {
            window.location.href = 'whoEnter.php';
        }, 500); // 延遲切換頁面，讓使用者看到彈窗
    }
    </script>

</head>
<body>
    <!-- 頁首標頭 -->
    <div class="header">
        <a href="home.php">
            <img src="logo.png" alt="網站Logo" class="logo">
        </a>
        想要找活動? NUK報名活動系統!
    </div>

    <!-- 登入按鈕 -->
    <a href="whoEnter.php" class="login-btn">登入</a>

    
    

    <main>
    <!-- 輪播區 -->
    <div class="carousel-container">
        <div class="carousel">
            <img src="1.jpg" alt="活動照片 1" class="active">
            <img src="2.jpg" alt="活動照片 2">
            <img src="3.jpg" alt="活動照片 3">
            <img src="4.jpg" alt="活動照片 4">
        </div>
        <button class="carousel-arrow left">&lt;</button>
        <button class="carousel-arrow right">&gt;</button>
        <div class="carousel-buttons">
            <button class="active"></button>
            <button></button>
            <button></button>
            <button></button>
        </div>
    </div>
        <!-- 替換活動展示欄為系學會展示區 -->
        <div class="container">
            <!-- 第一個學系 -->
            <div class="major">
                <img src="introMajor1.jpg" alt="系學會圖片 1">
                <div class="details">
                    <a href="#" onclick="alertAndRedirect(event)">應用物理系學會活動詳情請點我</a>
                </div>
            </div>

            <!-- 第二個學系 -->
            <div class="major">
                <img src="introMajor2.jpg" alt="系學會圖片 2">
                <div class="details">
                    <a href="#" onclick="alertAndRedirect(event)">應用化學系學會活動詳情請點我</a>
                </div>
            </div>

            <!-- 第三個學系 -->
            <div class="major">
                <img src="introMajor3.jpg" alt="系學會圖片 3">
                <div class="details">
                    <a href="#" onclick="alertAndRedirect(event)">土木工程系學會活動詳情請點我</a>
                </div>
            </div>

            <!-- 第四個學系 -->
            <div class="major">
                <img src="introMajor4.jpg" alt="系學會圖片 4">
                <div class="details">
                    <a href="#" onclick="alertAndRedirect(event)">資訊工程系學會活動詳情請點我</a>
                </div>
            </div>

            <!-- 第五個學系 -->
            <div class="major">
                <img src="introMajor5.jpg" alt="系學會圖片 5">
                <div class="details">
                    <a href="#" onclick="alertAndRedirect(event)">電機工程系學會活動詳情請點我</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>