<?php require_once __DIR__ . '/session.php'; ?>
<?php require_once __DIR__ . '/functions.php'; ?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
<title>就活管理サイト</title>
</head>
<body>

<?php $unread_count = (isLoggedIn()) ? getUnreadMessageCount($_SESSION['user_id']) : 0; ?>
<script>
var current_user_id = <?php echo isLoggedIn() ? (int)$_SESSION['user_id'] : 0; ?>;
</script>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">就活管理サイト</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
      data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" 
      aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <?php if (isLoggedIn()): ?>
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">ダッシュボード</a></li>
        <li class="nav-item"><a class="nav-link" href="edit_profile.php">プロフィール編集</a></li>
        <li class="nav-item"><a class="nav-link" href="edit_status.php">就活状況編集</a></li>
        <li class="nav-item"><a class="nav-link" href="edit_schedule.php">スケジュール編集</a></li>
        <li class="nav-item"><a class="nav-link" href="account.php">アカウント管理</a></li>
        <li class="nav-item">
          <a class="nav-link" href="dm.php">
            DM <?php if ($unread_count > 0): ?><span class="badge bg-danger"><?php echo $unread_count; ?></span><?php endif; ?>
          </a>
        </li>
      </ul>
      <div class="d-flex">
        <a href="logout.php" class="btn btn-outline-light">ログアウト</a>
      </div>
      <?php else: ?>
      <div class="ms-auto">
        <a href="login.php" class="btn btn-outline-light me-2">ログイン</a>
        <a href="register.php" class="btn btn-primary">ユーザー登録</a>
      </div>
      <?php endif; ?>
    </div>
  </div>
</nav>

<div class="container mt-4">
