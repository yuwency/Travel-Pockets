<?php
session_start();
if (!isset($_SESSION["login_session"]) || $_SESSION["login_session"] !== true) {
    header("Location: login.php");
    exit;
}

$link = mysqli_connect("localhost", "root", "", "travel_pockets");
if (!$link) {
    die("資料庫連線失敗：" . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 接收修改資料
    $place_id = intval($_POST["place_id"]);
    $place_name = trim($_POST["place_name"]);
    $location = trim($_POST["location"]);
    $region = trim($_POST["region"]);
    $type = trim($_POST["type"]);
    $cost = intval($_POST["cost"]);

    $sql = "UPDATE places SET place_name=?, location=?, region=?, type=?, cost=? WHERE place_id=?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "ssssii", $place_name, $location, $region, $type, $cost, $place_id);
    mysqli_stmt_execute($stmt);

    echo "<script>alert('修改成功！'); window.location.href='place.php';</script>";
    exit;
}

// 載入原本資料
if (isset($_GET["id"])) {
    $place_id = intval($_GET["id"]);
    $result = mysqli_query($link, "SELECT * FROM places WHERE place_id = $place_id");
    $place = mysqli_fetch_assoc($result);
    if (!$place) {
        echo "<script>alert('找不到該景點'); window.location.href='place.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('無效的請求'); window.location.href='place.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>修改景點</title>
    <style>
        body {
            font-family: '微軟正黑體';
            background-color: #f9f5f2;
            padding: 40px;
        }
        .container {
            width: 500px;
            margin: auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #5e5b52;
        }
        label {
            display: block;
            margin-top: 15px;
            color: #5e5b52;
        }
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-top: 5px;
        }
        .btn-submit {
            background-color: #6c8b72;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            margin-top: 25px;
            width: 100%;
            cursor: pointer;
        }
        .btn-submit:hover {
            background-color: #5b7a62;
        }
        .back-btn {
            display: block;
            width: 150px;
            margin: 30px auto 0;
            background-color: #92897a;
            text-align: center;
            text-decoration: none;
            color: white;
            padding: 10px;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>修改景點資料</h2>
    <form method="post">
        <input type="hidden" name="place_id" value="<?= $place["place_id"] ?>">

        <label>景點名稱：</label>
        <input type="text" name="place_name" value="<?= htmlspecialchars($place["place_name"]) ?>" required>

        <label>縣市：</label>
        <input type="text" name="location" value="<?= htmlspecialchars($place["location"]) ?>" required>

        <label>地區：</label>
        <input type="text" name="region" value="<?= htmlspecialchars($place["region"]) ?>" required>

        <label>分類：</label>
        <input type="text" name="type" value="<?= htmlspecialchars($place["type"]) ?>" required>

        <label>預估花費：</label>
        <input type="number" name="cost" value="<?= htmlspecialchars($place["cost"]) ?>" required>

        <button type="submit" class="btn-submit">確定修改</button>
    </form>

    <a href="place.php" class="back-btn">返回景點管理</a>
</div>

</body>
</html>