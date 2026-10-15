<?php
session_start();
if (!isset($_SESSION["login_session"]) || $_SESSION["login_session"] !== true) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$link = mysqli_connect("localhost", "root", "", "travel_pockets");
$result = mysqli_query($link, "SELECT * FROM members WHERE user_id = $user_id");
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
<head>
    <meta charset="utf-8">
    <title>成員管理</title>
</head>
<body>
    <h2>我的成員</h2>
    <a href="+member.php"> + 新增成員</a>
    <table border="1">
        <tr>
            <th>成員名稱</th>
            <th>關係</th>
            <th>操作</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= htmlspecialchars($row["member_name"]) ?></td>
            <td><?= htmlspecialchars($row["member_relationship_name"]) ?></td>
            <td>
                <a href="updatemember.php?member_id=<?= $row["member_id"] ?>">修改</a>
                |
                <form method="post" action="-member.php" style="display:inline;" onsubmit="return confirm('確定刪除？');">
                    <input type="hidden" name="member_id" value="<?= $row['member_id'] ?>">
                    <button type="submit">刪除</button><br>
					
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
	<a href="user_account.php">返回</a>
</body>
</html>