<?php
$link = mysqli_connect("localhost", "root", "", "travel_pockets");
if (!$link) {
    die("連線錯誤: " . mysqli_connect_error());
}
mysqli_set_charset($link, "utf8");

// 模擬登入使用者 ID，正式用時改用 session
$user_id = 1;

$edit_mode = false;
$edit_wishlist_id = 0;
$edit_data = [
    'member_id' => '',
    'place_id' => '',
    'want_go' => '想去',
    'been_there' => '沒去過',
];

// 處理刪除
if (isset($_GET['delete'])) {
    $wishlist_id = intval($_GET['delete']);

    $stmt = $link->prepare("
        SELECT 1 FROM members_wishlist mw
        JOIN members m ON mw.member_id = m.member_id
        WHERE mw.wishlist_id = ? AND m.user_id = ?
    ");
    $stmt->bind_param("ii", $wishlist_id, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt_del = $link->prepare("DELETE FROM members_wishlist WHERE wishlist_id = ?");
        $stmt_del->bind_param("i", $wishlist_id);
        $stmt_del->execute();
    }
    header("Location: members_wishlist.php");
    exit;
}

// 編輯模式
if (isset($_GET['edit'])) {
    $edit_wishlist_id = intval($_GET['edit']);
    $stmt = $link->prepare("
        SELECT member_id, place_id, want_go, been_there
        FROM members_wishlist
        WHERE wishlist_id = ?
    ");
    $stmt->bind_param("i", $edit_wishlist_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $edit_mode = true;
        $edit_data = $res->fetch_assoc();

        // 驗證該成員是否屬於當前用戶
        $stmt_check = $link->prepare("SELECT 1 FROM members WHERE member_id = ? AND user_id = ?");
        $stmt_check->bind_param("ii", $edit_data['member_id'], $user_id);
        $stmt_check->execute();
        $stmt_check->store_result();
        if ($stmt_check->num_rows === 0) {
            header("Location: members_wishlist.php");
            exit;
        }
    } else {
        header("Location: members_wishlist.php");
        exit;
    }
}

// 新增/更新
if (isset($_POST['submit'])) {
    $member_id = intval($_POST['member_id']);
    $place_id = intval($_POST['place_id']);
    $want_go = $_POST['want_go'];
    $been_there = $_POST['been_there'];
    $wishlist_id_post = isset($_POST['wishlist_id']) ? intval($_POST['wishlist_id']) : 0;

    // 驗證成員是否屬於登入用戶
    $stmt = $link->prepare("SELECT 1 FROM members WHERE member_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $member_id, $user_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
        echo "<script>alert('無效的成員！'); location.href='members_wishlist.php';</script>";
        exit;
    }

    // 取得 place_name
    $stmt = $link->prepare("SELECT place_name FROM places WHERE place_id = ?");
    $stmt->bind_param("i", $place_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 0) {
        echo "<script>alert('無效的景點！'); location.href='members_wishlist.php';</script>";
        exit;
    }
    $place_data = $res->fetch_assoc();
    $place_name = $place_data['place_name'];

    if ($wishlist_id_post > 0) {
        // 更新同時更新 place_name
        $stmt = $link->prepare("UPDATE members_wishlist SET member_id = ?, place_id = ?, place_name = ?, want_go = ?, been_there = ? WHERE wishlist_id = ?");
        $stmt->bind_param("iisssi", $member_id, $place_id, $place_name, $want_go, $been_there, $wishlist_id_post);
        $stmt->execute();
    } else {
        // 新增時同時存 place_name
        $stmt = $link->prepare("INSERT INTO members_wishlist (member_id, place_id, place_name, want_go, been_there) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $member_id, $place_id, $place_name, $want_go, $been_there);
        $stmt->execute();
    }
    header("Location: members_wishlist.php");
    exit;
}

// 取成員清單
$members = [];
$stmt = $link->prepare("SELECT member_id, member_name FROM members WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $members[] = $row;
}

// 取願望清單，直接用 members_wishlist.place_name 不 JOIN places
$query = "
SELECT mw.wishlist_id, m.member_name, mw.place_name, mw.want_go, mw.been_there
FROM members_wishlist mw
JOIN members m ON mw.member_id = m.member_id
WHERE m.user_id = ?
ORDER BY mw.wishlist_id ASC
";
$stmt = $link->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
<meta charset="UTF-8" />
<title>成員願望清單管理</title>
<style>
    body {
        font-family: '微軟正黑體';
        background-color: #f9f5f2;
        padding: 40px;
        color: #5e5b52;
    }
    .container {
        max-width: 900px;
        margin: auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    h2 {
        text-align: center;
        color: #5e5b52;
        margin-bottom: 30px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    th, td {
        border: 1px solid #ccc;
        padding: 12px;
        text-align: center;
        color: #5e5b52;
    }
    th {
        background-color: #d8cfc4;
        color: #3e3a36;
    }
    tr:nth-child(even) {
        background-color: #f1ece6;
    }
    a {
        color: #5e5b52;
        text-decoration: none;
        font-weight: 600;
    }
    a:hover {
        text-decoration: underline;
        color: #3e3a36;
    }
    form {
        margin-top: 30px;
        background-color: #fff;
        padding: 20px 25px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    label {
        display: block;
        margin-bottom: 15px;
        font-weight: 600;
        color: #5e5b52;
    }
    select, button {
        width: 100%;
        padding: 10px 14px;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 16px;
        background-color: #f9f5f2;
        color: #5e5b52;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    select:focus, button:hover {
        background-color: #a89f91;
        color: white;
        outline: none;
        border-color: #92897a;
    }
    button[type="submit"] {
        margin-top: 10px;
        font-weight: 700;
        background-color: #a89f91;
        border: none;
        color: white;
    }
    button[type="submit"]:hover {
        background-color: #92897a;
    }
    .btn-group {
        margin-top: 25px;
        display: flex;
        gap: 15px;
        justify-content: center;
    }
    .btn-group button {
        width: auto;
        padding: 10px 28px;
        background-color: #92897a;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
    }
    .btn-group button:hover {
        background-color: #7f796d;
    }
</style>
</head>
<body>

<div class="container">
<h2> 成員願望清單</h2>

<table>
  <tr>
    <th>成員</th>
    <th>景點</th>
    <th>想去</th>
    <th>去過</th>
    <th>操作</th>
  </tr>
  <?php while ($row = $result->fetch_assoc()) { ?>
  <tr>
    <td><?= htmlspecialchars($row['member_name']) ?></td>
    <td><?= htmlspecialchars($row['place_name']) ?></td>
    <td><?= htmlspecialchars($row['want_go']) ?></td>
    <td><?= htmlspecialchars($row['been_there']) ?></td>
    <td>
      <a href="?edit=<?= htmlspecialchars($row['wishlist_id']) ?>">編輯</a> |
      <a href="?delete=<?= htmlspecialchars($row['wishlist_id']) ?>" onclick="return confirm('確定刪除？')">刪除</a>
    </td>
  </tr>
  <?php } ?>
</table>

<form method="post" autocomplete="off">
  <input type="hidden" name="wishlist_id" value="<?= $edit_mode ? $edit_wishlist_id : '' ?>">
  
  <label for="member_id">成員：
    <select name="member_id" id="member_id" required>
      <option value="">請選擇成員</option>
      <?php foreach ($members as $m): ?>
        <?php $selected = ((int)$edit_data['member_id'] === (int)$m['member_id']) ? "selected" : ""; ?>
        <option value="<?= $m['member_id'] ?>" <?= $selected ?>>
          <?= htmlspecialchars($m['member_name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label>

  <label for="place_id">景點：
    <select name="place_id" id="place_id" required>
      <option value="">請選擇景點</option>
      <?php
      $places_res = mysqli_query($link, "SELECT place_id, place_name FROM places ORDER BY place_name ASC");
      while ($place = mysqli_fetch_assoc($places_res)) {
          $selected = ((int)$edit_data['place_id'] === (int)$place['place_id']) ? "selected" : "";
          echo "<option value='{$place['place_id']}' $selected>" . htmlspecialchars($place['place_name']) . "</option>";
      }
      ?>
    </select>
  </label>

  <label for="want_go">想去：
    <select name="want_go" id="want_go" required>
      <option value="想去" <?= $edit_data['want_go'] === '想去' ? 'selected' : '' ?>>想去</option>
      <option value="不想去" <?= $edit_data['want_go'] === '不想去' ? 'selected' : '' ?>>不想去</option>
    </select>
  </label>

  <label for="been_there">去過：
    <select name="been_there" id="been_there" required>
      <option value="沒去過" <?= $edit_data['been_there'] === '沒去過' ? 'selected' : '' ?>>沒去過</option>
      <option value="去過" <?= $edit_data['been_there'] === '去過' ? 'selected' : '' ?>>去過</option>
    </select>
  </label>

  <button type="submit" name="submit"><?= $edit_mode ? "更新" : "新增" ?></button>
  <?php if ($edit_mode): ?>
    <div class="btn-group">
      <button type="button" onclick="window.location.href='members_wishlist.php'">取消編輯</button>
    </div>
  <?php endif; ?>
</form>
<div class="btn-group">
  <button onclick="location.href='function.php'">返回選擇功能</button>
  <button onclick="location.href='wishlist_analysis.php'">需求整合分析</button>
</div>
</div>
</div>

</body>
</html>
