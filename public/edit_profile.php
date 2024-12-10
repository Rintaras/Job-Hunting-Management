<?php
require_once __DIR__ . '/../includes/header.php';
if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$profile = getUserProfile($user_id);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $university = $_POST['university'] ?? '';
    $major = $_POST['major'] ?? '';
    $github_url = $_POST['github_url'] ?? '';
    $twitter_url = $_POST['twitter_url'] ?? '';
    $homepage_url = $_POST['homepage_url'] ?? '';
    $is_status_public = isset($_POST['is_status_public']) ? 1 : 0;
    $account_id = $_POST['account_id'] ?? '';

    $icon_image = $profile['icon_image'] ?? null;
    if (isset($_FILES['icon_image']) && $_FILES['icon_image']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['icon_image']['tmp_name'];
        $original_name = basename($_FILES['icon_image']['name']);
        $ext = pathinfo($original_name, PATHINFO_EXTENSION);
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($ext), $allowed_ext)) {
            $new_filename = uniqid('icon_', true) . '.' . $ext;
            $target = __DIR__ . '/uploads/' . $new_filename;
            if (move_uploaded_file($tmp_name, $target)) {
                $icon_image = $new_filename;
            } else {
                $error = '画像アップロードに失敗しました。';
            }
        } else {
            $error = 'サポートされていない画像形式です。';
        }
    }

    if (!$error) {
      $pdo = getPdoConnection();
      if ($profile) {
          $stmt = $pdo->prepare("UPDATE profiles SET name=?, university=?, major=?, github_url=?, twitter_url=?, homepage_url=?, icon_image=?, is_status_public=?, updated_at=NOW() WHERE user_id=?");
          $stmt->execute([$name, $university, $major, $github_url, $twitter_url, $homepage_url, $icon_image, $is_status_public, $user_id]);
      } else {
          $stmt = $pdo->prepare("INSERT INTO profiles (user_id, name, university, major, github_url, twitter_url, homepage_url, icon_image, is_status_public, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
          $stmt->execute([$user_id, $name, $university, $major, $github_url, $twitter_url, $homepage_url, $icon_image, $is_status_public]);
      }

      if (!empty($account_id)) {
          $stmt = $pdo->prepare("SELECT id FROM users WHERE account_id=? AND id!=?");
          $stmt->execute([$account_id, $user_id]);
          if ($stmt->fetch()) {
              $error = 'そのアカウントIDは既に使用されています。';
          } else {
              $stmt = $pdo->prepare("UPDATE users SET account_id=? WHERE id=?");
              $stmt->execute([$account_id, $user_id]);
          }
      }

      if (!$error) {
          redirect('dashboard.php');
      }
  }
}
?>

<h1>プロフィール編集</h1>
<?php if ($error): ?>
<div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>

<form method="post" class="mb-3" enctype="multipart/form-data">
  <div class="mb-3">
    <label class="form-label">氏名</label>
    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($profile['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">大学</label>
    <input type="text" name="university" class="form-control" value="<?php echo htmlspecialchars($profile['university'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">専攻</label>
    <input type="text" name="major" class="form-control" value="<?php echo htmlspecialchars($profile['major'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">アイコン画像</label><br>
    <?php if (!empty($profile['icon_image'])): ?>
      <img src="uploads/<?php echo htmlspecialchars($profile['icon_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="icon" style="max-width:100px;"><br>
    <?php endif; ?>
    <input type="file" name="icon_image" class="form-control">
  </div>
  <div class="mb-3">
    <label class="form-label">GitHub URL</label>
    <input type="url" name="github_url" class="form-control" value="<?php echo htmlspecialchars($profile['github_url'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">X(Twitter) URL</label>
    <input type="url" name="twitter_url" class="form-control" value="<?php echo htmlspecialchars($profile['twitter_url'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">HP URL</label>
    <input type="url" name="homepage_url" class="form-control" value="<?php echo htmlspecialchars($profile['homepage_url'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
  </div>
  <div class="mb-3 form-check">
    <input type="checkbox" name="is_status_public" class="form-check-input" id="statusPublic" <?php echo (!empty($profile['is_status_public'])) ? 'checked' : ''; ?>>
    <label class="form-check-label" for="statusPublic">就活状況を他ユーザーへ公開する</label>
  </div>
  <div class="mb-3">
    <label class="form-label">アカウントID</label>
    <?php
    $pdo = getPdoConnection();
    $stmt = $pdo->prepare("SELECT account_id FROM users WHERE id=?");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch();
    $current_account_id = $user_data['account_id'] ?? '';
    ?>
    <input type="text" name="account_id" class="form-control" value="<?php echo htmlspecialchars($current_account_id, ENT_QUOTES, 'UTF-8'); ?>">
  </div>
  <button type="submit" class="btn btn-primary">更新</button>
</form>
<p><a href="dashboard.php" class="btn btn-link">戻る</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
