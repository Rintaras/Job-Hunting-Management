<?php
require_once __DIR__ . '/../includes/header.php';
if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$pdo = getPdoConnection();

$edit_id = isset($_GET['edit_id']) ? (int)$_GET['edit_id'] : null;
$edit_schedule = null;

if ($edit_id) {
    $stmt = $pdo->prepare("SELECT * FROM schedules WHERE id=? AND user_id=?");
    $stmt->execute([$edit_id, $user_id]);
    $edit_schedule = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_schedule'])) {
        $event_date = $_POST['event_date'] ?? '';
        $event_time = $_POST['event_time'] ?? '';
        $event_description = $_POST['event_description'] ?? '';

        if ($event_date && $event_description) {
            if ($edit_schedule) {
                $stmt = $pdo->prepare("UPDATE schedules SET event_date=?, event_time=?, event_description=?, updated_at=NOW() WHERE id=? AND user_id=?");
                $stmt->execute([$event_date, $event_time, $event_description, $edit_id, $user_id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO schedules (user_id, event_date, event_time, event_description, updated_at) VALUES (?, ?, ?, ?, NOW())");
                $stmt->execute([$user_id, $event_date, $event_time, $event_description]);
            }
        }
    }

    redirect('edit_schedule.php');
}

if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM schedules WHERE id = ? AND user_id = ?");
    $stmt->execute([$delete_id, $user_id]);
    redirect('edit_schedule.php');
}

$schedules = getSchedules($user_id);
?>

<h1>スケジュール編集</h1>

<h2 class="h4 mt-4">現在のスケジュール</h2>
<?php if ($schedules): ?>
<ul class="list-group mb-3">
  <?php foreach ($schedules as $sch): ?>
    <li class="list-group-item d-flex justify-content-between align-items-start">
      <div>
        <strong><?php echo htmlspecialchars($sch['event_date'], ENT_QUOTES, 'UTF-8'); ?> <?php echo $sch['event_time'] ? htmlspecialchars($sch['event_time'], ENT_QUOTES, 'UTF-8') : ''; ?></strong><br>
        <?php echo nl2br(htmlspecialchars($sch['event_description'], ENT_QUOTES, 'UTF-8')); ?>
      </div>
      <div>
        <a href="?edit_id=<?php echo $sch['id']; ?>" class="btn btn-sm btn-secondary me-2">編集</a>
        <a href="?delete_id=<?php echo $sch['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('削除しますか？');">削除</a>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
<?php else: ?>
<p class="text-muted">まだスケジュールはありません。</p>
<?php endif; ?>

<h2 class="h4"><?php echo $edit_schedule ? 'スケジュール更新' : '新規スケジュール追加'; ?></h2>
<form method="post" class="mb-3">
  <input type="hidden" name="save_schedule" value="1">
  <div class="mb-3">
    <label class="form-label">日付</label>
    <input type="date" name="event_date" class="form-control" required value="<?php echo htmlspecialchars($edit_schedule['event_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">時間</label>
    <input type="time" name="event_time" class="form-control" value="<?php echo htmlspecialchars($edit_schedule['event_time'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">説明</label>
    <input type="text" name="event_description" class="form-control" required value="<?php echo htmlspecialchars($edit_schedule['event_description'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
  </div>
  <button type="submit" class="btn btn-primary"><?php echo $edit_schedule ? '更新' : '追加'; ?></button>
</form>

<p><a href="dashboard.php" class="btn btn-link">戻る</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
