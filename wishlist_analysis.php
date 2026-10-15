<?php
session_start();
if (!isset($_SESSION["login_session"]) || $_SESSION["login_session"] !== true) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$link = mysqli_connect("localhost", "root", "", "travel_pockets");
if (!$link) {
    die("連線失敗：" . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <title>成員想去景點總和排名</title>
  <style>
    body { font-family: '微軟正黑體'; background-color: #f9f5f2; padding: 40px; }
    h2 { text-align: center; color: #5e5b52; margin-bottom: 20px;}
    table { width: 100%; border-collapse: collapse; background-color: #fff; margin-top: 10px; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
    th { background-color: #d8cfc4; }
    tr:nth-child(even) { background-color: #f0ede8; }
    .no-data { text-align: center; color: #999; margin-top: 30px; }
    .back-btn { display: inline-block; margin-top: 30px; padding: 8px 16px; background-color: #a39887; color: white; text-decoration: none; border-radius: 4px; }
    .back-btn:hover { background-color: #8a7f6f; }
  </style>
</head>
<body>
  <h2>成員想去景點總和排名</h2>

<?php
$sql = "
    SELECT 
        mw.place_id,
        mw.place_name,
        p.region,
        p.type,
        COUNT(*) AS want_count
    FROM members_wishlist mw
    JOIN members m ON mw.member_id = m.member_id
    JOIN places p ON mw.place_id = p.place_id
    WHERE mw.want_go = '想去' AND m.user_id = ?
    GROUP BY mw.place_id, mw.place_name, p.region, p.type
    ORDER BY want_count DESC, mw.place_name ASC
";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        echo "<table>";
        echo "<tr>
                <th>排名</th>
                <th>景點名稱</th>
                <th>想去人數</th>
                <th>地區</th>
                <th>類型</th>
              </tr>";
        $rank = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$rank}</td>
                    <td>" . htmlspecialchars($row['place_name']) . "</td>
                    <td>{$row['want_count']} 人</td>
                    <td>" . htmlspecialchars($row['region']) . "</td>
                    <td>" . htmlspecialchars($row['type']) . "</td>
                  </tr>";
            $rank++;
        }
        echo "</table>";
    } else {
        echo "<p class='no-data'>目前沒有成員想去的景點資料。</p>";
    }
    mysqli_stmt_close($stmt);
} else {
    echo "<p class='no-data'>查詢失敗：" . mysqli_error($link) . "</p>";
}

mysqli_close($link);
?>

<a href="members_wishlist.php" class="back-btn">返回</a>
</body>
</html>