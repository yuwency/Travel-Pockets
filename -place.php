<?php
session_start();
if (!isset($_SESSION["login_session"]) || $_SESSION["login_session"] !== true) {
    header("Location: login.php");
    exit;
}

if (isset($_GET["id"])) {
    $place_id = intval($_GET["id"]);

    $link = mysqli_connect("localhost", "root", "", "travel_pockets");
    if (!$link) {
        die("資料庫連線失敗：" . mysqli_connect_error());
    }

    $sql = "DELETE FROM places WHERE place_id = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "i", $place_id);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo "<script>alert('景點已成功刪除！'); window.location.href='place.php';</script>";
    } else {
        echo "<script>alert('找不到該景點'); window.location.href='place.php';</script>";
    }

    exit;
} else {
    echo "<script>alert('無效的請求'); window.location.href='place.php';</script>";
    exit;
}
