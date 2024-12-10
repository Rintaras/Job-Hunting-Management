<?php
require_once __DIR__ . '/../includes/header.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account_id = $_POST['account_id'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($account_id && $password) {
        $pdo = getPdoConnection();
        $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE account_id = ?");
        $stmt->execute([$account_id]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            redirect('dashboard.php');
        } else {
            $error = 'アカウントIDまたはパスワードが正しくありません。';
        }
    } else {
        $error = 'アカウントIDとパスワードを入力してください。';
    }
}
?>

<h1>ログイン</h1>
<div class="form-container">
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
    <button type="submit" class="btn btn-success w-100">ログイン</button>
</form>
<p><a href="index.php">トップへ戻る</a></p>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
