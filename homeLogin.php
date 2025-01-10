<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: whoEnter.html"); // 未登入則跳轉到登入頁面
    exit();
}
$user = $_SESSION['user'];
?>

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
        }

        .carousel-container {
            position: relative;
            width: 75%;
            height: 30vh; /* 修改高度為視窗的 30% */
            overflow: hidden;
            margin: 0 auto;
            background-color: rgba(189, 212, 206, 0.5);
            z-index: 0;
        }

        .carousel {
            position: relative;
            width: 100%;
            height: 100%;
            background-color: rgba(189, 212, 206, 0.5);
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
            top: 10px;
            right: 10px;
            color: white;
            text-decoration: none;
            font-weight: bold;
            font-size: 20px;
            z-index: 1000;
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

        .activities {
            display: none;
            margin-top: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .activities.show {
            display: block;
        }

        main {
            background-color: rgba(156,187,179,1);
            padding: 20px;
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

        document.addEventListener('DOMContentLoaded', () => {
            const majors = document.querySelectorAll('.major');
            const activityContainers = document.querySelectorAll('.activities');

            majors.forEach((major, index) => {
                major.addEventListener('click', () => {
                    activityContainers.forEach((container, i) => {
                        if (i === index) {
                            container.classList.add('show');
                        } else {
                            container.classList.remove('show');
                        }
                    });
                });
            });
        });
    </script>
</head>
<body>
    <div class="header">
        <a href="homeLogin.php">
            <img src="logo.png" alt="網站Logo" class="logo">
        </a>
        NUK活動報名系統
        <div class="user-menu">
            <span id="user-menu-toggle"><?php echo htmlspecialchars($user['name']); ?> ▼</span>
            <ul>
                <li><a href="studentAttend.php">已報名資訊</a></li>
                <li><a href="activityInfo.php">活動詳情</a></li>
                <li><a href="hostInfo.php">活動方資訊</a></li>
                <li><a href="placeInfo.php">地點資訊</a></li>
                <li><a href="studentInfo.php">個人資訊</a></li>
                <li><a href="home.php">登出</a></li>
            </ul>
        </div>
    </div>

    <main>
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

        <div class="container">
            <div class="major">
                <img src="major1.jpeg" alt="應用物理系">
                <div class="details">
                    <a href="#">應用物理系學會活動詳情請點我</a>
                </div>
            </div>
            <div class="major">
                <img src="major2.jpg" alt="應用化學系">
                <div class="details">
                    <a href="#">應用化學系學會活動詳情請點我</a>
                </div>
            </div>
            <div class="major">
                <img src="major3.jpg" alt="土木工程系">
                <div class="details">
                    <a href="#">土木工程系學會活動詳情請點我</a>
                </div>
            </div>
            <div class="major">
                <img src="major4.jpg" alt="資訊工程系">
                <div class="details">
                    <a href="#">資訊工程系學會活動詳情請點我</a>
                </div>
            </div>
            <div class="major">
                <img src="major5.jpg" alt="電機工程系">
                <div class="details">
                    <a href="#">電機工程系學會活動詳情請點我</a>
                </div>
            </div>
        </div>

        <div id="activities-1" class="activities">
            <?php
            include('connectionSession.php');
            $result = $conn->query("SELECT * FROM activity WHERE host='應物系學會'");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='activity'>";
                    echo "<p><strong>活動名稱：</strong>" . htmlspecialchars($row['name']) . "</p>";
                    echo "<p><strong>地點：</strong>" . htmlspecialchars($row['place']) . "</p>";
                    echo "<p><strong>舉辦時間：</strong>" . htmlspecialchars($row['date']) . "</p>";
                    echo "<p><strong>主辦方：</strong>" . htmlspecialchars($row['host']) . "</p>";
                    ?>
                    <form action="activityInfo.php" method="POST" style="display: inline;">
                    <input type="hidden" name="activity_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" style="background: none; border: none; color: rgba(108,165,179,1); text-decoration: underline; cursor: pointer;">
                    點我前往報名畫面
                    </button>
                    </form>
                    <?php
                    echo "</div>";
                }
            } else {
                echo "<p>目前無活動。</p>";
            }
            ?>
        </div>

        <div id="activities-2" class="activities">
            <?php
            $result = $conn->query("SELECT * FROM activity WHERE host='應化系學會'");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='activity'>";
                    echo "<p><strong>活動名稱：</strong>" . htmlspecialchars($row['name']) . "</p>";
                    echo "<p><strong>地點：</strong>" . htmlspecialchars($row['place']) . "</p>";
                    echo "<p><strong>舉辦時間：</strong>" . htmlspecialchars($row['date']) . "</p>";
                    echo "<p><strong>主辦方：</strong>" . htmlspecialchars($row['host']) . "</p>";
                    ?>
                    <form action="activityInfo.php" method="POST" style="display: inline;">
                    <input type="hidden" name="activity_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" style="background: none; border: none; color: rgba(108,165,179,1); text-decoration: underline; cursor: pointer;">
                    點我前往報名畫面
                    </button>
                    </form>
                    <?php
                    echo "</div>";
                }
            } else {
                echo "<p>目前無活動。</p>";
            }
            ?>
        </div>

        <div id="activities-3" class="activities">
            <?php
            $result = $conn->query("SELECT * FROM activity WHERE host='土木系學會'");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='activity'>";
                    echo "<p><strong>活動名稱：</strong>" . htmlspecialchars($row['name']) . "</p>";
                    echo "<p><strong>地點：</strong>" . htmlspecialchars($row['place']) . "</p>";
                    echo "<p><strong>舉辦時間：</strong>" . htmlspecialchars($row['date']) . "</p>";
                    echo "<p><strong>主辦方：</strong>" . htmlspecialchars($row['host']) . "</p>";
                    ?>
                    <form action="activityInfo.php" method="POST" style="display: inline;">
                    <input type="hidden" name="activity_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" style="background: none; border: none; color: rgba(108,165,179,1); text-decoration: underline; cursor: pointer;">
                    點我前往報名畫面
                    </button>
                    </form>
                    <?php
                    echo "</form>";
                    echo "</div>";
                }
            } else {
                echo "<p>目前無活動。</p>";
            }
            ?>
        </div>

        <div id="activities-4" class="activities">
            <?php
            $result = $conn->query("SELECT * FROM activity WHERE host='資工系學會'");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='activity'>";
                    echo "<p><strong>活動名稱：</strong>" . htmlspecialchars($row['name']) . "</p>";
                    echo "<p><strong>地點：</strong>" . htmlspecialchars($row['place']) . "</p>";
                    echo "<p><strong>舉辦時間：</strong>" . htmlspecialchars($row['date']) . "</p>";
                    echo "<p><strong>主辦方：</strong>" . htmlspecialchars($row['host']) . "</p>";
                    ?>
                    <form action="activityInfo.php" method="POST" style="display: inline;">
                    <input type="hidden" name="activity_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" style="background: none; border: none; color: rgba(108,165,179,1); text-decoration: underline; cursor: pointer;">
                    點我前往報名畫面
                    </button>
                    </form>
                    <?php
                    echo "</div>";
                }
            } else {
                echo "<p>目前無活動。</p>";
            }
            ?>
        </div>

        <div id="activities-5" class="activities">
            <?php
            $result = $conn->query("SELECT * FROM activity WHERE host='電機系學會'");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='activity'>";
                    echo "<p><strong>活動名稱：</strong>" . htmlspecialchars($row['name']) . "</p>";
                    echo "<p><strong>地點：</strong>" . htmlspecialchars($row['place']) . "</p>";
                    echo "<p><strong>舉辦時間：</strong>" . htmlspecialchars($row['date']) . "</p>";
                    echo "<p><strong>主辦方：</strong>" . htmlspecialchars($row['host']) . "</p>";
                    ?>
                    <form action="activityInfo.php" method="POST" style="display: inline;">
                    <input type="hidden" name="activity_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" style="background: none; border: none; color: rgba(108,165,179,1); text-decoration: underline; cursor: pointer;">
                    點我前往報名畫面
                    </button>
                    </form>
                    <?php
                    echo "</div>";
                }
            } else {
                echo "<p>目前無活動。</p>";
            }
            ?>
        </div>
    </main>
</body>
</html>
