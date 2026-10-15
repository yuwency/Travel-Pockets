<?php

session_start();

// 資料庫連線
$link = mysqli_connect("localhost", "root", "", "travel_pockets");
if (!$link) {
    die("資料庫連線失敗：" . mysqli_connect_error());
}

// 當表單送出
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
  

    // 簡單檢查
    if (empty($username) || empty($password) ) {
        $error = "所有欄位都必須填寫！";
    } else {
    // 密碼加密
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        // 檢查帳號是否已存在
        $check_sql = "SELECT * FROM users WHERE username = ?";
        $stmt = mysqli_prepare($link, $check_sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $error = "此帳號已被註冊，請使用其他帳號。";
        } 
          
          
            // 新增使用者
            $insert_sql ="INSERT INTO users (username, password) VALUES (?,?)";
            $stmt = mysqli_prepare($link, $insert_sql);
            mysqli_stmt_bind_param($stmt, "ss", $username,$password);
            if (mysqli_stmt_execute($stmt)) {
                $success = "註冊成功";
				header("Location: login.php");
                exit;
            } else {
                $error = "註冊失敗" . mysqli_error($link);
            }
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>註冊帳號</title>
</head>
<body>
    <h2>註冊帳號</h2>
    <?php
    if (isset($error)) echo "<p style='color:red;'>$error</p>";
    if (isset($success)) echo "<p style='color:green;'>$success</p>";
    ?>
    <form method="post" action="register.php">
	    <label>使用者帳號：</label>
        <input type="text" name="username"
		placeholder="請輸入帳號名稱 ! ! "required><br>
		<label>使用者密碼：</label>
        <input type="password" name="password"
		placeholder="請輸入密碼 ! ! " required><br>
        <input type="submit" value="註冊">
		<a href="login.php">返回</a>
    </form>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>註冊帳號</title>
    <style>
        
    </style>
</head>
<body>
