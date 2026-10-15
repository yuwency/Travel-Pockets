<?php
session_start();
if (!isset($_SESSION["login_session"]) || $_SESSION["login_session"] !== true) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$link = new mysqli("localhost", "root", "", "travel_pockets");
if ($link->connect_error) {
    die("連線失敗：" . $link->connect_error);
}

function get_wishlist_places($link, $user_id) {
    $sql = "
        SELECT 
            mw.place_id,
            p.place_name,
            p.region,
            p.type,
            p.cost,
            COUNT(*) AS want_count
        FROM members_wishlist mw
        JOIN members m ON mw.member_id = m.member_id
        JOIN places p ON mw.place_id = p.place_id
        WHERE mw.want_go = '想去' AND m.user_id = ?
        GROUP BY mw.place_id, p.place_name, p.region, p.type, p.cost
        ORDER BY want_count DESC, p.place_name ASC
    ";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $places = [];
    while ($row = $result->fetch_assoc()) {
        $places[] = $row;
    }
    $stmt->close();
    return $places;
}

function get_random_places_by_region($link, $region, $exclude_place_ids = []) {
    $place_ids_str = "";
    if (!empty($exclude_place_ids)) {
        $place_ids_str = implode(",", array_map('intval', $exclude_place_ids));
        $sql = "SELECT place_id, place_name, region, type, cost FROM places WHERE region = ? AND place_id NOT IN ($place_ids_str) ORDER BY RAND()";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("s", $region);
    } else {
        $sql = "SELECT place_id, place_name, region, type, cost FROM places WHERE region = ? ORDER BY RAND()";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("s", $region);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $places = [];
    while ($row = $result->fetch_assoc()) {
        $places[] = $row;
    }
    $stmt->close();
    return $places;
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8" />
    <title>行程模擬器</title>
    <style>
        body { font-family: '微軟正黑體'; background-color: #f4f1ed; padding: 40px; }
        h2, h3 { text-align: center; color: #5e5b52; }
        form, .plan { background-color: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin: auto; width: 60%; }
        input, button { padding: 5px; margin: 5px 0; width: 100%; border-radius: 8px; border: 1px solid #ccc; }
        table { width: 100%; border-collapse: collapse; margin-top:  20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #e6e0d4; }
        .back-btn { display: inline-block; margin-top: 30px; padding: 8px 16px; background-color: #a39887; color: white; text-decoration: none; border-radius: 4px; }
        .back-btn:hover { background-color: #8a7f6f; }
    </style>
</head>
<body>
<h2>行程模擬器</h2>

<form method="post">
    <label>總預算：</label>
    <input type="number" name="total_budget" required>
    <label>旅遊天數：</label>
    <input type="number" name="total_days" required>
    <button type="submit" name="generate">生成行程</button>
</form>

<?php
if (isset($_POST['generate'])) {
    $budget = intval($_POST['total_budget']);
    $days = intval($_POST['total_days']);
    if ($days <= 0) {
        echo "<p style='color:red; text-align:center;'>旅遊天數必須大於0</p>";
        exit;
    }
    $daily_budget = $budget / $days;

    $wishlist_places = get_wishlist_places($link, $user_id);

    $places_by_region = [];
    foreach ($wishlist_places as $place) {
        $places_by_region[$place['region']][] = $place;
    }

    $plan = [];
    $total_cost = 0;
    $day = 1;
    $max_budget = $budget + 1000;

    foreach ($places_by_region as $region => $places) {
        foreach ($places as $place) {
            if (!isset($plan[$day])) $plan[$day] = [];

            $current_day_cost = array_sum(array_column($plan[$day], 'cost'));
            $current_day_count = count($plan[$day]);

            if (
                ($current_day_cost + $place['cost'] > $daily_budget) ||
                ($current_day_count >= 7) ||
                ($total_cost + $place['cost'] > $max_budget)
            ) {
                $day++;
                if ($day > $days) break;
                if (!isset($plan[$day])) $plan[$day] = [];
            }

            if ($day > $days) break;

            $plan[$day][] = $place;
            $total_cost += $place['cost'];
        }
        if ($day > $days) break;
    }

    foreach ($places_by_region as $region => $_) {
        $added_place_ids = [];
        foreach ($plan as $day_places) {
            foreach ($day_places as $p) {
                $added_place_ids[] = $p['place_id'];
            }
        }

        $random_places = get_random_places_by_region($link, $region, $added_place_ids);

        for ($d = 1; $d <= $days; $d++) {
            if (!isset($plan[$d])) $plan[$d] = [];

            while (count($plan[$d]) < 7 && !empty($random_places)) {
                $candidate = array_shift($random_places);
                $current_day_cost = array_sum(array_column($plan[$d], 'cost'));

                if (
                    ($current_day_cost + $candidate['cost'] <= $daily_budget) &&
                    ($total_cost + $candidate['cost'] <= $max_budget)
                ) {
                    $plan[$d][] = $candidate;
                    $total_cost += $candidate['cost'];
                }
            }
        }
    }

    echo "<div class='plan'>";
    echo "<h3>推薦行程（每日最多7個景點）</h3>";
    echo "<table><tr><th>天數</th><th>景點名稱</th><th>地區</th><th>類型</th><th>費用</th></tr>";
    foreach ($plan as $d => $places) {
        foreach ($places as $i => $place) {
            echo "<tr>";
            echo $i === 0 ? "<td rowspan='" . count($places) . "'>第 {$d} 天</td>" : "";
            echo "<td>" . htmlspecialchars($place['place_name']) . "</td>";
            echo "<td>" . htmlspecialchars($place['region']) . "</td>";
            echo "<td>" . htmlspecialchars($place['type']) . "</td>";
            echo "<td>" . htmlspecialchars($place['cost']) . "</td>";
            echo "</tr>";
        }
    }
    echo "</table>";
    echo "<p style='text-align:center; margin-top: 1px;'>總預算：{$budget}，總花費：{$total_cost}</p>";
	echo "<form method='post' style='text-align:center; margin-top: 20px;'>
        <input type='hidden' name='total_budget' value='{$budget}'>
        <input type='hidden' name='total_days' value='{$days}'>
        <button type='submit' name='generate'>重新生成行程</button>
      </form>";
    echo "</div>";
}

$link->close();
?>

<a href="function.php" class="back-btn">返回選擇功能</a>

</body>
</html>