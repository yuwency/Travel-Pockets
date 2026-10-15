<?php
session_start();
if (!isset($_SESSION["login_session"]) || $_SESSION["login_session"] !== true) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $place_name = trim($_POST["place_name"] ?? '');
    $location = trim($_POST["location"] ?? '');
    $region = trim($_POST["region"] ?? '');
    $type = trim($_POST["type"] ?? '');
    $cost = floatval($_POST["cost"] ?? 0);

    if (!empty($place_name) && !empty($location) && !empty($region) && !empty($type)) {
        $link = mysqli_connect("localhost", "root", "", "travel_pockets");
        if (!$link) {
            die("資料庫連線失敗：" . mysqli_connect_error());
        }

        $sql = "INSERT INTO places (place_name, location, region, type, cost)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "ssssd", $place_name, $location, $region, $type, $cost);
        mysqli_stmt_execute($stmt);

        echo "<script>alert('新增成功！'); window.location.href='place.php';</script>";
        exit;
    } else {
        echo "<script>alert('請填寫所有欄位'); history.back();</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>新增景點</title>
    <style>
        body {
            font-family: '微軟正黑體';
            background-color: #f9f5f2;
            padding: 40px;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 500px;
            margin: auto;
        }
        h2 {
            color: #5e5b52;
            text-align: center;
        }
        label {
            display: block;
            margin-top: 12px;
            color: #5e5b52;
        }
        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-top: 6px;
        }
        .submit-btn {
            background-color: #a89f91;
            color: white;
            padding: 10px;
            margin-top: 20px;
            border: none;
            border-radius: 8px;
            width: 100%;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #92897a;
        }
        .back-btn {
            display: block;
            width: 150px;
            margin: 30px auto 0;
            background-color: #c94f4f;
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
    <h2>新增景點資料</h2>
    <form method="post">
        <label>景點名稱：</label>
        <input type="text" name="place_name" required>

        <label>縣市：</label>
        <input type="text" name="location" required>

        <label>地區（北 / 中 / 南 / 東）：</label>
        <input type="text" name="region" required>

        <label>分類（如：景點、購物、美食等）：</label>
        <input type="text" name="type" required>

        <label>預估花費（元）：</label>
        <input type="number" name="cost" step="1" min="0" required>

        <button type="submit" class="submit-btn">新增</button>
    </form>

    <a href="place.php" class="back-btn">返回景點管理</a>
</div>

</body>
</html>