<?php
//確保使用者有登入
session_start();
if (!isset($_SESSION["login_session"]) || $_SESSION["login_session"] !== true) {
    header("Location: login.php");
    exit;
}

// 可以連接資料庫拿更多使用者資料
$link = mysqli_connect("localhost", "root", "", "travel_pockets");
$user_id = $_SESSION["user_id"];
$sql = "SELECT * FROM users WHERE user_id = $user_id";
$result = mysqli_query($link, $sql);
$user = mysqli_fetch_assoc($result);

// 是否是表單送回
if (isset($_POST["Update"])) {
   // 開啟MySQL的資料庫連接
   $link = @mysqli_connect("localhost","root","")
         or die("無法開啟MySQL資料庫連接!<br/>");
   mysqli_select_db($link, "travel_pockets");  // 選擇資料庫
   // 建立更新記錄的SQL指令字串
    $sql = "UPDATE users SET ";
    $sql .= "username='" . $_POST["username"] . "', ";
    $sql .= "password='" . $_POST["password"] . "' ";
    $sql .= "WHERE user_id='" . $_POST["user_id"] . "'";
   echo "<b>SQL指令: $sql</b><br/>";
   //送出UTF8編碼的MySQL指令
   mysqli_query($link, 'SET NAMES utf8'); 
   if ( mysqli_query($link, $sql) ){ // 執行SQL指令
      echo "資料庫更新記錄成功, 影響記錄數: ". 
           mysqli_affected_rows($link) . "<br/>";
   }else{
      die("資料庫更新記錄失敗<br/>");
   mysqli_close($link);    // 關閉資料庫連接
}  
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>使用者帳戶管理</title>
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
        .info {
            font-size: 18px;
            margin: 20px 0;
        }
        .bbb {
            display: block;
            width: 100%;
            padding: 10px;
            text-align: center;
            background-color: #a89f91;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 10px;
        }
        .bbb:hover {
            background-color: #92897a;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>歡迎，<?php echo htmlspecialchars($user["username"]); ?>！</h2>

    <div class="info">帳號：<?php echo htmlspecialchars($user["username"]); ?></div>
    <div class="info">密碼：<?php echo htmlspecialchars($user["password"]); ?></div>
	
    <a href="update_user_data.php" class="bbb">修改我的個人資料</a>
    <a href="member.php" class="bbb">管理我的成員資料</a>	
	<a href="function.php" class="bbb">選擇功能</a>	
    <a href="logout.php" class="bbb" 
	style="background-color:#c94f4f;">登出</a>
</div>

</body>
</html>