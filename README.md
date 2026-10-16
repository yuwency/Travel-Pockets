# Travel-Pockets(旅遊推薦與多人行程規劃管理系統)
本系統是一個以旅遊規劃為核心的旅遊推薦管理系統Travel Pockets，提供使用者註冊登入、個人資料管理、景點資料管理、旅遊成員管理及成員願望清單管理等功能。使用者可以記錄每位成員想去或不想去的景點，並透過景點分類查詢與「同縣市推薦」功能，快速尋找適合的旅遊地點。此外，系統結合成員的旅遊偏好、預算與旅遊天數，透過「行程模擬器」自動規劃推薦行程，安排每日景點順序並計算預估花費，讓使用者能更有效率地整合多人旅遊需求，減少規劃時間，提升旅遊安排的便利性。

![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)

---
# 💡 核心功能與特色 (Key Features)

  * 會員與個人資料管理 (Member Management)
  * 支援帳號註冊 (`register.php`)、安全登入/登出機制 (`login.php` / `logout.php`)。
  * 提供會員個人資料檢視與動態更新功能 (`update_user_data.php` / `updatemember.php`)。

  * 景點資料庫與多維度分類 (Place Management & Classification)
  * 建立完整景點資料庫 (`place.php` / `updateplace.php`)。
  * 支援多標籤與分類查詢 (`classplace.php`)，方便使用者依主題或地區快速檢索。

  * 多人成員願望清單與交叉分析 (Wishlist & Preference Analysis)
  * 每位成員可獨立建立並維護個人的想去/不想去景點清單 (`members_wishlist.php`)。
  * 具備願望清單交叉分析演算法 (`wishlist_analysis.php`)，自動統計團隊公約數，找出符合最多人喜好的最佳景點。

  * 同縣市/主題旅遊推薦系統 (Travel Recommendation)
  * 提供「同縣市/地區景點推薦」演算法 (`travel_ex.php`)，自動引導使用者探索周邊熱門景點與隱藏美食。

  * 智慧行程模擬器與預算試算 (Itinerary Simulator & Cost Estimation)
  * 整合團隊成員偏好、預算與旅遊天數。
  * 行程模擬器**自動編排每日景點最佳順序，並同步試算預估總花費，大幅節省人工排行程與拆帳的時間。

---

# 🛠️ 技術棧 (Tech Stack)

* 後端架構 (Backend)：PHP (OOP 邏輯封裝與模組化開發)
* 資料庫 (Database)：MySQL / MariaDB (U1233115_travel_pockets.sql)
* 資料庫連線 (Database Driver)：dbconnect.php` (PDO / mysqli 介面)
* 前端介面 (Frontend)：HTML5, CSS3, JavaScript

---

# 📂 專案檔案結構 (Project Structure)

`text
Travel-Pockets/
├── dbconnect.php             # 資料庫連線設定檔
├── function.php              # 通用邏輯與公用 Function 庫
├── register.php              # 會員註冊頁面
├── login.php / logout.php    # 登入與登出驗證邏輯
├── member.php                # 會員中心主頁
├── update_user_data.php      # 使用者資料修改邏輯
├── updatemember.php          # 會員資料更新處理
├── place.php                 # 景點列表與詳細資訊頁
├── classplace.php            # 景點多維度分類與篩選
├── updateplace.php           # 景點資料新增/修改頁面
├── members_wishlist.php      # 成員願望清單管理介面
├── wishlist_analysis.php     # 願望清單交叉分析與偏好統計模組
├── travel_ex.php             # 同縣市/主題景點推薦演算法模組
├── U1233115_travel_pockets.sql # 資料庫 Schema 與初始測試資料
├── 成員願望清單.png           # 系統功能 UI 截圖
├── 景點資料分類.png           # 系統功能 UI 截圖
├── 景點資料管理.png           # 系統功能 UI 截圖
└── 行程模擬器.png             # 系統功能 UI 截圖
