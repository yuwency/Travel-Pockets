<?php
session_start(); // 啟用 session
session_unset(); // 清除所有 session 變數
session_destroy(); // 銷毀 session
header("Location: login.php"); // 轉回登入頁
exit;
?>
<a href="logout.php" class="btn" style="background-color:#c94f4f;">登出</a>
