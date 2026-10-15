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

$region = $_GET["region"] ?? '';
$type = $_GET["type"] ?? '';

$query = "SELECT * FROM places WHERE 1=1";
$params = [];

if (!empty($region)) {
    $query .= " AND region = ?";
    $params[] = $region;
}
if (!empty($type)) {
    $query .= " AND type = ?";
    $params[] = $type;
}

$stmt = mysqli_prepare($link, $query);
if (!empty($params)) {
    $types = str_repeat("s", count($params));
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>依分類查詢景點</title>
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
            max-width: 900px;
            margin: auto;
        }
        h2 {
            text-align: center;
            color: #5e5b52;
            margin-bottom: 30px;
        }
        form {
            text-align: center;
            margin-bottom: 30px;
        }
        select, button {
            padding: 8px 14px;
            margin: 0 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        button {
            background-color: #a89f91;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #92897a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #d8cfc4;
            color: #3e3a36;
        }
        tr:nth-child(even) {
            background-color: #f1ece6;
        }
        .back-btn {
            display: block;
            width: 180px;
            margin: 40px auto 0;
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
    <h2>依分類查詢景點</h2>

    <form method="get">
        <label>地區：</label>
        <select name="region">
            <option value="">全部</option>
            <option value="北部" <?= $region === "北部" ? "selected" : "" ?>>北部</option>
            <option value="中部" <?= $region === "中部" ? "selected" : "" ?>>中部</option>
            <option value="南部" <?= $region === "南部" ? "selected" : "" ?>>南部</option>
        </select>

        <label>類型：</label>
        <select name="type">
            <option value="">全部</option>
            <option value="購物" <?= $type === "購物" ? "selected" : "" ?>>購物</option>
            <option value="景點" <?= $type === "景點" ? "selected" : "" ?>>景點</option>
            <option value="美食" <?= $type === "美食" ? "selected" : "" ?>>美食</option>
        </select>

        <button type="submit">查詢</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>景點名稱</th>
            <th>縣市</th>
            <th>地區</th>
            <th>分類</th>
            <th>預估花費</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $row["place_id"] ?></td>
            <td><?= htmlspecialchars($row["place_name"]) ?></td>
            <td><?= htmlspecialchars($row["location"]) ?></td>
            <td><?= htmlspecialchars($row["region"]) ?></td>
            <td><?= htmlspecialchars($row["type"]) ?></td>
            <td><?= htmlspecialchars($row["cost"]) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <a href="place.php" class="back-btn">返回景點管理</a>
</div>
</body>
</html>