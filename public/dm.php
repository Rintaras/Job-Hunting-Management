<?php
require_once __DIR__ . '/../includes/header.php';
if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$other_id = $_GET['with'] ?? null;
$other_id = $other_id ? (int)$other_id : null;

$pdo = getPdoConnection();

if (isset($_GET['delete_msg_id'])) {
    $msg_id = (int)$_GET['delete_msg_id'];
    deleteMessage($msg_id, $user_id);
    redirect("dm.php?with={$other_id}");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $other_id) {
    if (isset($_POST['new_message'])) {
        $message = $_POST['message'] ?? '';
        $image_path = null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['image']['tmp_name'];
            $original_name = basename($_FILES['image']['name']);
            $ext = pathinfo($original_name, PATHINFO_EXTENSION);
            $allowed_ext = ['jpg','jpeg','png','gif'];
            if (in_array(strtolower($ext), $allowed_ext)) {
                $new_filename = uniqid('msg_',true).'.'.$ext;
                $target = __DIR__.'/uploads/messages/'.$new_filename;
                if (move_uploaded_file($tmp_name,$target)) {
                    $image_path = 'uploads/messages/'.$new_filename;
                }
            }
        }

        if ($message !== '' || $image_path !== null) {
            $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message, image_path, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$user_id, $other_id, $message, $image_path]);
        }
        redirect("dm.php?with={$other_id}");
    }
}

if ($other_id) {
    $stmt = $pdo->prepare("UPDATE messages SET is_read=1 WHERE receiver_id=? AND sender_id=? AND is_read=0");
    $stmt->execute([$user_id, $other_id]);
}

$dm_users = getDMUsers($user_id);
$other_user = $other_id ? getUserById($other_id) : null;
$messages = $other_id ? getMessages($user_id, $other_id) : [];

$ou_profile = $other_id ? getProfileData($other_id) : null;

$display_name = $ou_profile['name'] ?? ($other_user['email'] ?? '');
$display_id = '';
if (!empty($ou_profile['account_id'])) {
    $display_id = '('.$ou_profile['account_id'].')';
}

?>

<h1>DM</h1>
<div class="row">
  <div class="col-md-3">
    <div class="list-group">
      <?php 
      foreach ($dm_users as $du) {
          $ouid = $du['other_id'];
          $oudata = getUserById($ouid);
          $ouprof = getProfileData($ouid);
          if (!$oudata) continue;
          $uname = $ouprof['name'] ?? $oudata['email'];
          $uid = '';
          if (!empty($ouprof['account_id'])) {
              $uid = ' ('.$ouprof['account_id'].')';
          }
          $active = ($other_id === $ouid) ? 'active' : '';
          echo '<a href="dm.php?with='.$ouid.'" class="list-group-item list-group-item-action '.$active.'">'.htmlspecialchars($uname.$uid, ENT_QUOTES, 'UTF-8').'</a>';
      } 
      ?>
    </div>
  </div>
  <div class="col-md-9">
    <?php if ($other_user): ?>
      <div class="card" style="height:600px; display:flex; flex-direction:column;">
        <div class="card-header">
          <?php echo htmlspecialchars($display_name, ENT_QUOTES, 'UTF-8'); ?> <?php echo htmlspecialchars($display_id, ENT_QUOTES, 'UTF-8'); ?> さんとのDM
        </div>
        <div class="card-body bg-light overflow-auto" style="flex:1; padding:10px;">
          <?php foreach ($messages as $msg): ?>
            <?php 
            $is_me = ($msg['sender_id'] === $user_id);
            $msg_class = $is_me ? 'bg-primary text-white' : 'bg-white border';
            $alignment = $is_me ? 'text-end ms-auto' : 'text-start me-auto';
            ?>
            <div class="d-flex mb-2" style="max-width:80%;">
              <?php if (!$is_me): ?>
                <div class="me-2"><span class="badge bg-secondary">相手</span></div>
              <?php endif; ?>
              <div class="p-2 rounded <?php echo $msg_class; ?> <?php echo $alignment; ?>">
                <?php if ($msg['message']): ?>
                  <?php echo nl2br(htmlspecialchars($msg['message'], ENT_QUOTES, 'UTF-8')); ?><br>
                <?php endif; ?>
                <?php if ($msg['image_path']): ?>
                  <img src="<?php echo htmlspecialchars($msg['image_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="画像" style="max-width:200px;"><br>
                <?php endif; ?>
                <small class="text-muted"><?php echo htmlspecialchars($msg['created_at'], ENT_QUOTES, 'UTF-8'); ?></small>
                <?php if ($is_me): ?>
                  <br><a href="dm.php?with=<?php echo $other_id; ?>&delete_msg_id=<?php echo $msg['id']; ?>" class="<?php echo $is_me?'text-white':''; ?>" onclick="return confirm('メッセージを削除しますか？');">送信取消</a>
                <?php endif; ?>
              </div>
              <?php if ($is_me): ?>
                <div class="ms-2"><span class="badge bg-info">自分</span></div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="card-footer">
          <form method="post" class="d-flex" enctype="multipart/form-data">
            <input type="text" name="message" class="form-control me-2" placeholder="メッセージを入力">
            <input type="file" name="image" class="form-control me-2" style="max-width:200px;">
            <button type="submit" name="new_message" class="btn btn-success">送信</button>
          </form>
        </div>
      </div>
    <?php else: ?>
      <p class="text-muted">相手を選択してください。</p>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
