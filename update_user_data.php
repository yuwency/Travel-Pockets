<?php
//確保使用者有登入
session_start();
if (!isset($_SESSION["login_session"]) || $_SESSION["login_session"] !== true) {
    header("Location: login.php");
    exit;
}

// 連接資料庫
$link = mysqli_connect("localhost", "root", "", "travel_pockets");
$user_id = $_SESSION["user_id"];
$sql = "SELECT * FROM users WHERE user_id = $user_id";
$result = mysqli_query($link, $sql);
$user = mysqli_fetch_assoc($result);

// 是否是表單送回
if (isset($_POST["Update"])) {
    $username = mysqli_real_escape_string($link, $_POST["username"]);
    $password = mysqli_real_escape_string($link, $_POST["password"]);

    $sql = "UPDATE users SET username='$username', password='$password' WHERE user_id=$user_id";
   mysqli_query($link, 'SET NAMES utf8'); 
    if (mysqli_query($link, $sql)) {
        echo "<script>
            alert('資料更新成功 ! !');
            window.location.href = 'user_account.php';
        </script>";
        exit;
    } else {
        die("更新失敗<br/>");
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>修改個人資料</title>
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
            margin-top: 10px;
            font-size: 16px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        input[type="submit"] {
            margin-top: 20px;
            padding: 10px;
            width: 100%;
            background-color: #a89f91;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #92897a;
        }
		
    </style>

<!DOCTYPE html>
</head>
<body>

</head>
<div class="container">
    <h2>修改個人資料</h2>
    <form method="post" action="update_user_data.php">
        <label>使用者帳號：</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

        <label>使用者密碼：</label>
        <input type="password" name="password" 
        placeholder="請輸入新密碼" required>
        <input type="submit" name="Update" value="更新資料">
		<a href="login.php">登出</a>
        
    </form>
</div>

</body>
</html>
