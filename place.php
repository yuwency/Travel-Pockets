<?php
// 驗證登入
session_start();
if (!isset($_SESSION["login_session"]) || $_SESSION["login_session"] !== true) {
    header("Location: login.php");
    exit;
}

$link = mysqli_connect("localhost", "root", "", "travel_pockets");
if (!$link) {
    die("資料庫連線失敗：" . mysqli_connect_error());
}

// 查詢所有景點
$result = mysqli_query($link, "SELECT * FROM places ORDER BY place_id ASC");

$all_places = [];
while ($row = mysqli_fetch_assoc($result)) {
    $all_places[] = $row;
}

// 預先建立推薦資料
$recommend_map = [];
foreach ($all_places as $place) {
    $place_id = $place['place_id'];
    $location = $place['location'];

    $recommend_map[$place_id] = [];
    foreach ($all_places as $other) {
        if ($other['location'] === $location && $other['place_id'] !== $place_id) {
            $recommend_map[$place_id][] = htmlspecialchars($other['place_name']);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>景點資料管理</title>
    <style>
        body {
            font-family: '微軟正黑體';
            background-color: #f9f5f2;
            padding: 40px;
        }
        h2 {
            text-align: center;
            color: #5e5b52;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background-color: #fff;
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
        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            color: #fff;
            text-decoration: none;
        }
        .add-btn {
            background-color: #a89f91;
            margin-bottom: 20px;
            display: inline-block;
            padding: 10px 20px;
        }
        .update-btn {
            background-color: #6c8b72;
        }
        .delete-btn {
            background-color: #c94f4f;
        }
        .back-btn {
            display: block;
            width: 150px;
            margin: 40px auto 0;
            background-color: #92897a;
            text-align: center;
            text-decoration: none;
            color: white;
            padding: 10px;
            border-radius: 8px;
        }
        .recommend-row {
            background-color: #f0f0f0;
            text-align: left;
            padding: 10px;
            font-size: 14px;
            display: none;
        }
    </style>
</head>
<body>

<h2>景點資料管理</h2>
<a href="+place.php" class="add-btn">+ 新增景點</a>
<a href="classplace.php" class="add-btn">依分類查詢景點</a>

<table>
    <tr>
        <th>ID</th>
        <th>景點名稱</th>
        <th>縣市</th>
        <th>地區</th>
        <th>分類</th>
        <th>預估花費</th>
        <th>操作</th>
    </tr>

    <?php foreach ($all_places as $row): ?>
    <tr>
        <td><?= $row["place_id"] ?></td>
        <td><?= htmlspecialchars($row["place_name"]) ?></td>
        <td><?= htmlspecialchars($row["location"]) ?></td>
        <td><?= htmlspecialchars($row["region"]) ?></td>
        <td><?= htmlspecialchars($row["type"]) ?></td>
        <td><?= htmlspecialchars($row["cost"]) ?></td>
        <td>
            <a href="updateplace.php?id=<?= $row["place_id"] ?>" class="btn update-btn">編輯</a>
            <a href="-place.php?id=<?= $row["place_id"] ?>" class="btn delete-btn" onclick="return confirm('確定要刪除嗎？')">刪除</a>
            <button class="btn add-btn" onclick="toggleReco(<?= $row['place_id'] ?>)">同縣市推薦</button>
        </td>
    </tr>
    <tr id="reco-<?= $row['place_id'] ?>" class="recommend-row">
        <td colspan="7">
            <strong>同縣市推薦（<?= htmlspecialchars($row['location']) ?>）：</strong><br>
            <?php if (count($recommend_map[$row['place_id']]) > 0): ?>
                <?= implode('　', $recommend_map[$row['place_id']]) ?>
            <?php else: ?>
                沒有其他推薦。
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<a href="function.php" class="back-btn">返回功能選單</a>

<script>
function toggleReco(id) {
    const row = document.getElementById("reco-" + id);
    const content = row.innerText.trim();

    if (content.includes("沒有其他推薦")) {
        alert("無同縣市推薦!!");
        return;
    }

    row.style.display = (row.style.display === "none" || row.style.display === "") ? "table-row" : "none";
}
</script>

</body>
</html>