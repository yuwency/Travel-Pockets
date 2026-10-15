<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>功能選單</title>
    <style>
        body {
            font-family: '微軟正黑體';
            background-color: #f9f5f2;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px;
        }
        h1 {
            color: #5e5b52;
            margin-bottom: 40px;
        }
        .menu-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 40px;
        }
        .menu-item {
            text-align: center;
        }
        .menu-item img {
            width: 150px;
            height: 150px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .menu-item img:hover {
            transform: scale(1.05);
        }
        .menu-label {
            margin-top: 12px;
            color: #6e6658;
            font-size: 16px;
        }
        .back-button {
            margin-top: 50px;
            background-color: #a89f91;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
        }
        .back-button:hover {
            background-color: #92897a;
        }
    </style>
</head>
<body>
    <h1>請選擇功能</h1>

    <div class="menu-container">
        <div class="menu-item">
            <a href="place.php">
                <img src="景點資料管理.png" alt="景點管理">
            </a>
            <div class="menu-label">景點資料管理</div>
        </div>
        <div class="menu-item">
            <a href="members_wishlist.php">
                <img src="成員願望清單.png" alt="願望清單">
            </a>
            <div class="menu-label">成員願望清單</div>
        </div>
        
        <div class="menu-item">
            <a href="travel_ex.php">
                <img src="行程模擬器.png" alt="行程模擬器">
            </a>
            <div class="menu-label">行程模擬器</div>
        </div>
    </div>

    <a href="user_account.php" class="back-button">返回使用者頁面</a>
</body>
</html>