<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>login.php</title>
</head>
<body>
<?php
session_start();  // 啟用交談期
$username = "";  $password = "";
// 取得表單欄位值
if ( isset($_POST["username"]) )
   $username = $_POST["username"];
if ( isset($_POST["password"]) )
   $password = $_POST["password"];
// 檢查是否輸入使用者名稱和密碼
if ($username != "" && $password != "") {
   // 建立MySQL的資料庫連接 
   $link = mysqli_connect("localhost","root",
                          "","travel_pockets")
        or die("無法開啟MySQL資料庫連接!<br/>");
   // 建立SQL指令字串
   $sql = "SELECT * FROM users WHERE password='";
   $sql.= $password."' AND username='".$username."'";
   // 執行SQL查詢
   $result = mysqli_query($link, $sql);
   $total_records = mysqli_num_rows($result);
   // 是否有查詢到使用者記錄
   
   if ( $total_records > 0 ) {
      // 成功登入, 指定Session變數
	  $row = mysqli_fetch_assoc($result);
      $_SESSION["login_session"] = true;
	  $_SESSION["user_id"] = $row["user_id"];
      $_SESSION["username"] = $row["username"];
      header("Location: user_account.php");
   } else {  // 登入失敗
      echo "<center><font color='red'>";
      echo "使用者名稱或密碼錯誤!<br/>";
      echo "</font>";
      $_SESSION["login_session"] = false;
   }
   mysqli_close($link);  // 關閉資料庫連接  
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>登入</title>
    <style>
        body {
            font-family: '微軟正黑體', sans-serif;
            background-color: #eae5dc; /* 莫蘭迪奶茶色 */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        #welcome {
           position: absolute;
            top: 20px;
            left: 20px;
            color: #5e5b52;
            font-size: 22px;
            font-family: "黑體", "Microsoft JhengHei", sans-serif; /* 設為黑體 */
            padding: 0; /* 去掉 padding */
            margin: 0; /* 去掉 margin */
        }

        .login-container {
            background-color: #f7f5f2;
            border: 1px solid #d5c7bc;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 350px;
        }

        h2 {
            text-align: center;
            color: #5e5b52; /* 莫蘭迪灰褐 */
            margin-bottom: 30px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px 0;
            border: 1px solid #c2b6ac;
            border-radius: 8px;
            background-color: #fefefe;
            font-size: 16px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #a89f91; /* 莫蘭迪霧紫褐 */
            color: white;
            border: none;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #92897a;
        }

        .message {
            text-align: center;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .message.error {
            color: #c94f4f;
        }
    </style>
</head>
<body>

    <p id="welcome">歡迎進入，你的旅遊口袋名單～</p>

    <div class="login-container">
        <h2>使用者登入</h2>
        <?php if (isset($error)): ?>
            <p class="message error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="post" action="login.php">
		</label>使用者帳號：</label>
        <input type="text" name="username"
		placeholder="請輸入帳號名稱 ! ! "/>	
		</label>使用者密碼：</label>
        <input type="password" name="password" 
		placeholder="請輸入密碼 ! ! "/>
            <a href="register.php">註冊</a>
			
			<input type="submit" value="登入">
        </form>
    </div>

</body>
</html>