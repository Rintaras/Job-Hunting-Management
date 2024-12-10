<?php
require_once __DIR__ . '/../includes/header.php';
if (!isLoggedIn()) {
    redirect('login.php');
}

$pdo = getPdoConnection();
$user_id = $_SESSION['user_id'];

$pass_error = '';
if (isset($_POST['change_password'])) {
    $current_pass = $_POST['current_password'] ?? '';
    $new_pass = $_POST['new_password'] ?? '';
    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id=?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    if ($user && password_verify($current_pass, $user['password_hash'])) {
        if (strlen($new_pass) > 0) {
            $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash=?, updated_at=NOW() WHERE id=?");
            $stmt->execute([$new_hash, $user_id]);
        } else {
            $pass_error = '新しいパスワードを入力してください。';
        }
    } else {
        $pass_error = '現在のパスワードが正しくありません。';
    }
}

$delete_error = '';
if (isset($_POST['delete_account'])) {
    $del_pass = $_POST['delete_password'] ?? '';
    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id=?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    if ($user && password_verify($del_pass, $user['password_hash'])) {
        $pdo->prepare("DELETE FROM profiles WHERE user_id=?")->execute([$user_id]);
        $pdo->prepare("DELETE FROM job_status WHERE user_id=?")->execute([$user_id]);
        $pdo->prepare("DELETE FROM schedules WHERE user_id=?")->execute([$user_id]);
        $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$user_id]);

        session_destroy();
        redirect('index.php');
    } else {
        $delete_error = 'パスワードが正しくありません。';
    }
}

$secret_error = '';
if (isset($_POST['set_secret'])) {
    $question = $_POST['secret_question'] ?? '';
    $answer = $_POST['secret_answer'] ?? '';
    if ($question && $answer) {
        $answer_hash = password_hash($answer, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET secret_question=?, secret_answer_hash=?, updated_at=NOW() WHERE id=?");
        $stmt->execute([$question, $answer_hash, $user_id]);
    } else {
        $secret_error = '合言葉と質問を入力してください。';
    }
}

$stmt = $pdo->prepare("SELECT secret_question FROM users WHERE id=?");
$stmt->execute([$user_id]);
$current_secret = $stmt->fetch();

?>

<h1>アカウント管理</h1>

<h2 class="h4 mt-4">パスワード変更</h2>
<?php if ($pass_error): ?>
<div class="alert alert-danger"><?php echo htmlspecialchars($pass_error, ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>
<form method="post" class="mb-3">
  <input type="hidden" name="change_password" value="1">
  <div class="mb-3">
    <label class="form-label">現在のパスワード</label>
    <input type="password" name="current_password" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">新しいパスワード</label>
    <input type="password" name="new_password" class="form-control" required>
  </div>
  <button type="submit" class="btn btn-primary">変更</button>
</form>

<h2 class="h4 mt-4">アカウント削除</h2>
<?php if ($delete_error): ?>
<div class="alert alert-danger"><?php echo htmlspecialchars($delete_error, ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>
<p class="text-danger">アカウントを削除すると、全てのデータが消去され、復元できません。</p>
<form method="post" class="mb-3">
  <input type="hidden" name="delete_account" value="1">
  <div class="mb-3">
    <label class="form-label">パスワードを入力して削除確認</label>
    <input type="password" name="delete_password" class="form-control" required>
  </div>
  <button type="submit" class="btn btn-danger">アカウント削除</button>
</form>

<h2 class="h4 mt-4">秘密の合言葉設定/変更</h2>
<?php if ($secret_error): ?>
<div class="alert alert-danger"><?php echo htmlspecialchars($secret_error, ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>
<form method="post" class="mb-3">
  <input type="hidden" name="set_secret" value="1">
  <div class="mb-3">
    <label class="form-label">秘密の質問</label>
    <input type="text" name="secret_question" class="form-control" value="<?php echo htmlspecialchars($current_secret['secret_question'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">秘密の答え</label>
    <input type="text" name="secret_answer" class="form-control" required>
  </div>
  <button type="submit" class="btn btn-primary">設定</button>
</form>

<p><a href="dashboard.php" class="btn btn-link">ダッシュボードに戻る</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
