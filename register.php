<?php
session_start();
header('Content-Type: application/json');
include('connectionSession.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 從請求中解析活動名稱和學號
    $data = json_decode(file_get_contents('php://input'), true);
    $activityName = isset($data['activityId']) ? mysqli_real_escape_string($conn, $data['activityId']) : null;
    $sid = isset($_SESSION['user']['sid']) ? mysqli_real_escape_string($conn, $_SESSION['user']['sid']) : null;


    // 檢查是否提供了活動名稱和學號
    if ($activityName && $sid) {
        // 檢查是否已經報名過該活動
        $checkQuery = "SELECT * FROM participants WHERE sid = '$sid' AND activity = '$activityName'";
        $checkResult = mysqli_query($conn, $checkQuery);

        if (mysqli_num_rows($checkResult) > 0) {
            echo json_encode(['success' => false, 'message' => '您已經報名過該活動！']);
            exit;
        }

        // 檢查報名人數是否已滿
        $limitQuery = "
            SELECT maxPeople, 
                   (SELECT COUNT(*) FROM participants WHERE activity = '$activityName') AS currentCount 
            FROM activity 
            WHERE name = '$activityName'";
        $result = mysqli_query($conn, $limitQuery);
        $row = mysqli_fetch_assoc($result);

        if ($row && $row['currentCount'] >= $row['maxPeople']) {
            // 報名人數已滿
            echo json_encode(['success' => false, 'message' => '報名人數已滿！']);
        } else {
            // 插入報名資料
            $query = "INSERT INTO participants VALUES ('$sid', '$activityName')";
            $insertResult = mysqli_query($conn, $query);

            if ($insertResult) {
                // 報名成功
                echo json_encode(['success' => true, 'message' => '報名成功！']);
            } else {
                // 插入失敗，可能是重複報名
                echo json_encode(['success' => false, 'message' => '您已經報名過該活動！(若尚未報名卻顯示此訊息請洽詢管理員)']);
            }
        }
    } else {
        // 缺少活動名稱或學號
        echo json_encode(['success' => false, 'message' => '活動名稱或使用者資料未提供！']);
    }
} else {
    // 請求方法不是 POST
    echo json_encode(['success' => false, 'message' => '請使用 POST 方法！']);
}
?>
