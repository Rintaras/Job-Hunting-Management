<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = getPdoConnection();

$step = 1;
$account_id = '';
$secret_question = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['check_account'])) {
        // Step1: アカウントID確認
        $account_id = $_POST['account_id'] ?? '';
        if ($account_id) {
            $stmt = $pdo->prepare("SELECT secret_question FROM users WHERE account_id=?");
            $stmt->execute([$account_id]);
            $user = $stmt->fetch();
            if ($user && !empty($user['secret_question'])) {
                $secret_question = $user['secret_question'];
                $step = 2;
            } else {
                $error = 'このアカウントIDは登録がないか、秘密の質問が設定されていません。';
            }
        } else {
            $error = 'アカウントIDを入力してください。';
        }
    } elseif (isset($_POST['reset_password'])) {
        // Step2: 合言葉の回答と新パスワード確認
        $account_id = $_POST['account_id'] ?? '';
        $secret_answer = $_POST['secret_answer'] ?? '';
        $new_password = $_POST['new_password'] ?? '';

        $stmt = $pdo->prepare("SELECT id, secret_answer_hash FROM users WHERE account_id=?");
        $stmt->execute([$account_id]);
        $user = $stmt->fetch();
        if ($user && password_verify($secret_answer, $user['secret_answer_hash'])) {
            // パスワード更新
            if (!empty($new_password)) {
                $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password_hash=?, updated_at=NOW() WHERE id=?");
                $stmt->execute([$new_hash, $user['id']]);
                // パスワード再設定完了後、ログインページへ
                redirect('login.php');
            } else {
                $error = '新しいパスワードを入力してください。';
                $step = 2;
                // secret_question再取得
                $stmt = $pdo->prepare("SELECT secret_question FROM users WHERE account_id=?");
                $stmt->execute([$account_id]);
                $user2 = $stmt->fetch();
                if ($user2) {
                    $secret_question = $user2['secret_question'];
                }
            }
        } else {
            $error = '秘密の合言葉が正しくありません。';
            $step = 2;
            // secret_question再取得
            $stmt = $pdo->prepare("SELECT secret_question FROM users WHERE account_id=?");
            $stmt->execute([$account_id]);
            $user2 = $stmt->fetch();
            if ($user2) {
                $secret_question = $user2['secret_question'];
            }
        }
    }
}
?>

<h1>パスワード再設定</h1>
<div class="form-container">
<?php if ($error): ?>
<div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>

<?php if ($step === 1): ?>
<form method="post">
  <input type="hidden" name="check_account" value="1">
  <div class="mb-3">
    <label class="form-label">登録アカウントID</label>
    <input type="text" name="account_id" class="form-control" required>
  </div>
  <button type="submit" class="btn btn-primary w-100">秘密の質問を確認</button>
</form>
<?php elseif ($step === 2): ?>
<form method="post">
  <input type="hidden" name="reset_password" value="1">
  <input type="hidden" name="account_id" value="<?php echo htmlspecialchars($account_id, ENT_QUOTES, 'UTF-8'); ?>">
  <div class="mb-3">
    <label class="form-label">秘密の質問</label>
    <input type="text" class="form-control" value="<?php echo htmlspecialchars($secret_question, ENT_QUOTES, 'UTF-8'); ?>" disabled>
  </div>
  <div class="mb-3">
    <label class="form-label">秘密の合言葉（回答）</label>
    <input type="text" name="secret_answer" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">新しいパスワード</label>
    <input type="password" name="new_password" class="form-control" required>
  </div>
  <button type="submit" class="btn btn-primary w-100">パスワード再設定</button>
</form>
<?php endif; ?>

<p class="mt-3"><a href="login.php">ログイン画面へ戻る</a></p>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
