<?php
session_start();
if (!isset($_SESSION["login_session"]) || $_SESSION["login_session"] !== true) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $member_name = trim($_POST["member_name"]);
    $relationship = trim($_POST["member_relationship_name"]);

    if (!empty($member_name)) {
        $link = mysqli_connect("localhost", "root", "", "travel_pockets");
        $sql = "INSERT INTO members (member_name, user_id, member_relationship_name)
                VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "sis", $member_name, $user_id, $relationship);
        mysqli_stmt_execute($stmt);
        echo "<script>alert('新增成功！'); window.location.href = 'member.php';</script>";
        exit;
    } else {
        echo "<script>alert('請填寫名稱'); history.back();</script>";
        exit;
    }
}
?>
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
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>新增成員</title></head>
<body>
    <h2>新增成員</h2>
    <form method="post">
        <label>名稱：</label>
        <input type="text" name="member_name" required><br>
        <label>關係：</label>
        <input type="text" name="member_relationship_name" required><br>
        <button type="submit">送出</button>
        <a href="member.php">返回</a>
    </form>
</body>
</html>