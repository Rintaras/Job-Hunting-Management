<?php
require_once __DIR__ . '/../includes/header.php';
if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$status = getJobStatus($user_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status_selection = $_POST['status_selection'] ?? '';
    $desired_industry = $_POST['desired_industry'] ?? '';
    $decided_companies = $_POST['decided_companies'] ?? '';
    $in_process_companies = $_POST['in_process_companies'] ?? '';
    $es_not_submitted_companies = $_POST['es_not_submitted_companies'] ?? '';
    $pivot_points = $_POST['pivot_points'] ?? '';

    $pdo = getPdoConnection();
    if ($status) {
        $stmt = $pdo->prepare("UPDATE job_status 
            SET status_selection=?, desired_industry=?, decided_companies=?, in_process_companies=?, es_not_submitted_companies=?, pivot_points=?, updated_at=NOW()
            WHERE user_id=?");
        $stmt->execute([$status_selection, $desired_industry, $decided_companies, $in_process_companies, $es_not_submitted_companies, $pivot_points, $user_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO job_status (user_id, status_selection, desired_industry, decided_companies, in_process_companies, es_not_submitted_companies, pivot_points, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$user_id, $status_selection, $desired_industry, $decided_companies, $in_process_companies, $es_not_submitted_companies, $pivot_points]);
    }
    redirect('dashboard.php');
}
?>

<h1>就活状況編集</h1>

<form method="post" class="mb-3">
  <div class="mb-3">
    <label class="form-label">就活状況</label>
    <input type="text" name="status_selection" class="form-control" value="<?php echo htmlspecialchars($status['status_selection'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">志望業界・分野</label>
    <input type="text" name="desired_industry" class="form-control" value="<?php echo htmlspecialchars($status['desired_industry'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">内定済み企業（改行で複数記入）</label>
    <textarea name="decided_companies" class="form-control" rows="3"><?php echo htmlspecialchars($status['decided_companies'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label">選考中企業（改行で複数記入）</label>
    <textarea name="in_process_companies" class="form-control" rows="3"><?php echo htmlspecialchars($status['in_process_companies'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label">ES未提出企業（改行で複数記入）</label>
    <textarea name="es_not_submitted_companies" class="form-control" rows="3"><?php echo htmlspecialchars($status['es_not_submitted_companies'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label">企業選びの軸3選（改行で3つ記入）</label>
    <textarea name="pivot_points" class="form-control" rows="3"><?php echo htmlspecialchars($status['pivot_points'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
  </div>
  <button type="submit" class="btn btn-primary">更新</button>
</form>
<p><a href="dashboard.php" class="btn btn-link">戻る</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
