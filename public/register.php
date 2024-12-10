<?php
require_once __DIR__ . '/../includes/header.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account_id = $_POST['account_id'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($account_id && $password) {
        $pdo = getPdoConnection();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE account_id = ?");
        $stmt->execute([$account_id]);
        if ($stmt->fetch()) {
            $error = '既に使用されているアカウントIDです。';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (email, account_id, password_hash, created_at, updated_at) VALUES (NULL, ?, ?, NOW(), NOW())");
            $stmt->execute([$account_id, $hash]);
            $user_id = $pdo->lastInsertId();

            $_SESSION['user_id'] = $user_id;
            redirect('dashboard.php');
        }
    } else {
        $error = 'アカウントIDとパスワードを入力してください。';
    }
}
?>

<h1>ユーザー登録</h1>
<?php if ($error): ?>
<div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>
<form method="post" class="mb-3">
  <div class="mb-3">
    <label class="form-label">アカウントID</label>
    <input type="text" name="account_id" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">パスワード</label>
    <input type="password" name="password" class="form-control" required>
  </div>
  <button type="submit" class="btn btn-primary">登録</button>
</form>
<p><a href="index.php">トップへ戻る</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
