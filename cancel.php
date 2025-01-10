<?php
session_start();
header('Content-Type: application/json');
include('connectionSession.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 確認使用者已登入
    if (!isset($_SESSION['user']) || !isset($_SESSION['user']['sid'])) {
        echo json_encode(['success' => false, 'message' => '使用者未登入！']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $sid = $_SESSION['user']['sid']; // 使用者學號
    $activityName = isset($data['activityName']) ? mysqli_real_escape_string($conn, $data['activityName']) : null;

    if (!$activityName) {
        echo json_encode(['success' => false, 'message' => '活動名稱未提供！']);
        exit;
    }

    // 刪除報名資料
    $deleteQuery = "DELETE FROM participants WHERE sid = '$sid' AND activity = '$activityName'";
    $deleteResult = mysqli_query($conn, $deleteQuery);

    if ($deleteResult && mysqli_affected_rows($conn) > 0) {
        echo json_encode(['success' => true, 'message' => '已成功取消報名！']);
    } else {
        echo json_encode(['success' => false, 'message' => '取消報名失敗，請確認是否已報名該活動！']);
    }
} else {
    echo json_encode(['success' => false, 'message' => '請使用 POST 方法！']);
}
?>
