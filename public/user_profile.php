<?php
require_once __DIR__ . '/../includes/header.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$view_user_id = $_GET['user_id'] ?? null;
if (!$view_user_id) {
    redirect('dashboard.php');
}

$view_user_id = (int)$view_user_id;
$pdo = getPdoConnection();

$view_user = getUserById($view_user_id);
if (!$view_user) {
    echo "<p>ユーザーが存在しません。</p>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$view_profile = getUserProfile($view_user_id);
$view_status = getJobStatus($view_user_id);

?>

<h1>ユーザー詳細</h1>

<div class="card mb-3">
  <div class="card-header">プロフィール</div>
  <div class="card-body">
    <?php if ($view_profile): ?>
      <?php if (!empty($view_profile['icon_image'])): ?>
        <img src="uploads/<?php echo htmlspecialchars($view_profile['icon_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="icon" class="img-thumbnail mb-2" style="max-width:100px;">
      <?php endif; ?>
      <p>氏名: <?php echo htmlspecialchars($view_profile['name'] ?? '未設定', ENT_QUOTES, 'UTF-8'); ?></p>
      <p>大学: <?php echo htmlspecialchars($view_profile['university'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
      <p>専攻: <?php echo htmlspecialchars($view_profile['major'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
      <?php if (!empty($view_profile['github_url'])): ?>
        <p>GitHub: <a href="<?php echo htmlspecialchars($view_profile['github_url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank"><?php echo htmlspecialchars($view_profile['github_url'], ENT_QUOTES, 'UTF-8'); ?></a></p>
      <?php endif; ?>
      <?php if (!empty($view_profile['twitter_url'])): ?>
        <p>X(Twitter): <a href="<?php echo htmlspecialchars($view_profile['twitter_url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank"><?php echo htmlspecialchars($view_profile['twitter_url'], ENT_QUOTES, 'UTF-8'); ?></a></p>
      <?php endif; ?>
      <?php if (!empty($view_profile['homepage_url'])): ?>
        <p>HP: <a href="<?php echo htmlspecialchars($view_profile['homepage_url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank"><?php echo htmlspecialchars($view_profile['homepage_url'], ENT_QUOTES, 'UTF-8'); ?></a></p>
      <?php endif; ?>
    <?php else: ?>
      <p class="text-muted">プロフィール情報がありません。</p>
    <?php endif; ?>
  </div>
</div>

<div class="card mb-3">
  <div class="card-header">就活状況</div>
  <div class="card-body">
    <?php if ($view_profile && $view_profile['is_status_public']): ?>
      <?php if ($view_status): ?>
        <p>内部決定状況: <?php echo htmlspecialchars($view_status['status_internal'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
        <p>選考状況: <?php echo htmlspecialchars($view_status['status_selection'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
        <p>志望業界: <?php echo htmlspecialchars($view_status['desired_industry'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
        <?php if (!empty($view_status['decided_companies'])): ?>
          <p>内定済み企業:<br><?php echo nl2br(htmlspecialchars($view_status['decided_companies'], ENT_QUOTES, 'UTF-8')); ?></p>
        <?php endif; ?>
        <?php if (!empty($view_status['in_process_companies'])): ?>
          <p>選考中企業:<br><?php echo nl2br(htmlspecialchars($view_status['in_process_companies'], ENT_QUOTES, 'UTF-8')); ?></p>
        <?php endif; ?>
        <?php if (!empty($view_status['es_not_submitted_companies'])): ?>
          <p>ES未提出企業:<br><?php echo nl2br(htmlspecialchars($view_status['es_not_submitted_companies'], ENT_QUOTES, 'UTF-8')); ?></p>
        <?php endif; ?>
        <?php if (!empty($view_status['pivot_points'])): ?>
          <p>企業選びの軸3選:<br><?php echo nl2br(htmlspecialchars($view_status['pivot_points'], ENT_QUOTES, 'UTF-8')); ?></p>
        <?php endif; ?>
      <?php else: ?>
        <p class="text-muted">就活状況未設定</p>
      <?php endif; ?>
    <?php else: ?>
      <p class="text-muted">就活状況は非公開またはプロフィール未設定です。</p>
    <?php endif; ?>
  </div>
</div>

<?php if ($user_id !== $view_user_id): ?>
  <a href="dm.php?with=<?php echo $view_user_id; ?>" class="btn btn-primary">DMを送る</a>
<?php endif; ?>
<p><a href="dashboard.php" class="btn btn-link mt-3">戻る</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
