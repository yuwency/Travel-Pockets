<?php
session_start();
if (!isset($_SESSION["login_session"]) || $_SESSION["login_session"] !== true) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$link = mysqli_connect("localhost", "root", "", "travel_pockets");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $member_id = intval($_POST["member_id"]);
    $name = trim($_POST["member_name"]);
    $relation = trim($_POST["member_relationship_name"]);

    $sql = "UPDATE members SET member_name=?, member_relationship_name=? WHERE member_id=? AND user_id=?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "ssii", $name, $relation, $member_id, $user_id);
    mysqli_stmt_execute($stmt);

    echo "<script>alert('修改成功！'); window.location.href = 'member.php';</script>";
    exit;
} else if (isset($_GET["member_id"])) {
    $member_id = intval($_GET["member_id"]);
    $result = mysqli_query($link, "SELECT * FROM members WHERE member_id = $member_id AND user_id = $user_id");
    $row = mysqli_fetch_assoc($result);
    if (!$row) {
        echo "找不到此成員"; exit;
    }
} else {
    echo "無效操作"; exit;
}
?>

<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>修改成員</title></head>
<body>
    <h2>修改成員</h2>
    <form method="post">
        <input type="hidden" name="member_id" value="<?= $row['member_id'] ?>">
        <label>名稱：</label>
        <input type="text" name="member_name" value="<?= htmlspecialchars($row['member_name']) ?>" required><br>
        <label>關係：</label>
        <input type="text" name="member_relationship_name" value="<?= htmlspecialchars($row['member_relationship_name']) ?>" required><br>
        <button type="submit">送出</button>
        <a href="member.php">返回</a>
    </form>
</body>
</html>
<style>
    body {
        font-family: '微軟正黑體';
        background-color: #f9f5f2;
        padding: 40px;
        margin: 0;
    }

    .container {
        background-color: #ffffff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        width: 600px;
        margin: auto;
    }

    h2 {
        color: #5e5b52;
        text-align: center;
        margin-bottom: 30px;
    }

    label {
        display: block;
        margin: 10px 0 5px;
        color: #5e5b52;
        font-weight: bold;
    }

    input[type="text"], input[type="hidden"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #dcd4cd;
        border-radius: 8px;
        background-color: #f4f1ed;
    }

    button, .btn {
        display: inline-block;
        padding: 10px 20px;
        background-color: #a89f91;
        color: #fff;
        border: none;
        border-radius: 8px;
        text-decoration: none;
        cursor: pointer;
        margin-top: 10px;
        transition: background-color 0.3s;
    }

    button:hover, .btn:hover {
        background-color: #92897a;
    }

    .btn-danger {
        background-color: #c94f4f;
    }

    .btn-danger:hover {
        background-color: #a13d3d;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-top: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background-color: #fff;
        margin-top: 20px;
    }

    th, td {
        padding: 10px;
        text-align: center;
        border: 1px solid #ddd;
    }

    th {
        background-color: #eae3dc;
        color: #5e5b52;
    }

    tr:nth-child(even) {
        background-color: #f7f3f0;
    }
</style>