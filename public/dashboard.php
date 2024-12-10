<?php
require_once __DIR__ . '/../includes/header.php';
if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['global_message'])) {
    $msg = $_POST['global_message'] ?? '';
    if ($msg !== '') {
        insertGlobalChatMessage($user_id, $msg);
        redirect('dashboard.php');
    }
}

$user_id = $_SESSION['user_id'];
$profile = getProfileData($user_id);
$status = getJobStatus($user_id);
$schedules = getSchedules($user_id);
$other_users = getOtherUserProfiles($user_id);
$global_chat = getGlobalChatMessages();
?>

<h1 class="mb-4">マイページ</h1>

<div class="row g-4 mb-4">
  <div class="col-md-4">
    <div class="card text-center p-3">
      <div class="card-header bg-primary text-white">就活プロフィール</div>
      <div class="card-body">
        <?php if ($profile): ?>
          <?php if (!empty($profile['icon_image'])): ?>
            <img src="uploads/<?php echo htmlspecialchars($profile['icon_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="icon" class="profile-icon">
          <?php else: ?>
            <div class="mb-2 text-muted">No Image</div>
          <?php endif; ?>
          <h5 class="fw-bold"><?php echo htmlspecialchars($profile['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h5>
          <?php if (!empty($profile['account_id'])): ?>
            <p class="mb-1 text-muted">@<?php echo htmlspecialchars($profile['account_id'], ENT_QUOTES, 'UTF-8'); ?></p>
          <?php endif; ?>
          <p class="mb-1"><?php echo htmlspecialchars($profile['university'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
          <p class="mb-1"><?php echo htmlspecialchars($profile['major'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
        <?php else: ?>
          <p class="text-muted">プロフィール未設定</p>
        <?php endif; ?>
        <a href="edit_profile.php" class="btn btn-outline-primary btn-sm mt-3">
          <i class="bi bi-pencil-square"></i> プロフィール編集
        </a>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card p-3">
      <div class="card-header bg-success text-white">就活状況</div>
      <div class="card-body">
        <?php if ($status): ?>
          <p><strong>選考状況:</strong> <?php echo htmlspecialchars($status['status_selection'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
          <p><strong>志望業界:</strong> <?php echo htmlspecialchars($status['desired_industry'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
          <?php if (!empty($status['decided_companies'])): ?>
            <p><strong>内定済み企業:</strong><br><?php echo nl2br(htmlspecialchars($status['decided_companies'], ENT_QUOTES, 'UTF-8')); ?></p>
          <?php endif; ?>
          <?php if (!empty($status['in_process_companies'])): ?>
            <p><strong>選考中企業:</strong><br><?php echo nl2br(htmlspecialchars($status['in_process_companies'], ENT_QUOTES, 'UTF-8')); ?></p>
          <?php endif; ?>
          <?php if (!empty($status['es_not_submitted_companies'])): ?>
            <p><strong>ES未提出企業:</strong><br><?php echo nl2br(htmlspecialchars($status['es_not_submitted_companies'], ENT_QUOTES, 'UTF-8')); ?></p>
          <?php endif; ?>
          <?php if (!empty($status['pivot_points'])): ?>
            <p><strong>企業選びの軸3選:</strong><br><?php echo nl2br(htmlspecialchars($status['pivot_points'], ENT_QUOTES, 'UTF-8')); ?></p>
          <?php endif; ?>
        <?php else: ?>
          <p class="text-muted">就活状況未設定</p>
        <?php endif; ?>
        <a href="edit_status.php" class="btn btn-outline-success btn-sm mt-3">
          <i class="bi bi-pencil"></i> 就活状況編集
        </a>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card p-3">
      <div class="card-header bg-info text-white">スケジュール</div>
      <div class="card-body">
        <?php if ($schedules): ?>
          <ul class="list-group list-group-flush mb-3">
            <?php foreach ($schedules as $sch): ?>
            <li class="list-group-item">
              <i class="bi bi-calendar-event"></i> 
              <?php echo htmlspecialchars($sch['event_date'], ENT_QUOTES, 'UTF-8'); ?>
              <?php echo $sch['event_time'] ? htmlspecialchars($sch['event_time'], ENT_QUOTES, 'UTF-8') : ''; ?>
              : <?php echo htmlspecialchars($sch['event_description'], ENT_QUOTES, 'UTF-8'); ?>
            </li>
            <?php endforeach; ?>
          </ul>
          <a href="download_csv.php" class="btn btn-secondary btn-sm"><i class="bi bi-download"></i> CSVダウンロード</a>
        <?php else: ?>
          <p class="text-muted mb-3">スケジュール未登録</p>
          <a href="download_csv.php" class="btn btn-secondary btn-sm" disabled>CSVダウンロード</a>
        <?php endif; ?>
        <a href="edit_schedule.php" class="btn btn-outline-info btn-sm mt-3">
          <i class="bi bi-pencil"></i> スケジュール編集/追加
        </a>
      </div>
    </div>
  </div>
</div>

<hr class="my-5">

<h2>他のユーザーのプロフィール</h2>
<div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
  <?php foreach ($other_users as $ou): ?>
    <?php 
      $ou_name = $ou['name'] ?? '匿名';
      $ou_acid = $ou['account_id'] ?? '';
      if ($ou_acid !== '') {
          $ou_acid = '('.$ou_acid.')';
      }
    ?>
    <div class="col">
      <a href="#" class="text-decoration-none text-dark" data-bs-toggle="modal" data-bs-target="#profileModal" data-userid="<?php echo $ou['user_id']; ?>">
        <div class="card h-100">
          <?php if (!empty($ou['icon_image'])): ?>
            <img src="uploads/<?php echo htmlspecialchars($ou['icon_image'], ENT_QUOTES, 'UTF-8'); ?>" class="card-img-top" alt="ユーザーアイコン" style="max-height:200px; object-fit:cover;">
          <?php else: ?>
            <div class="bg-light d-flex align-items-center justify-content-center" style="height:200px;">
              <span class="text-muted">No Image</span>
            </div>
          <?php endif; ?>
          <div class="card-body">
            <h5 class="card-title fw-bold"><?php echo htmlspecialchars($ou_name.$ou_acid, ENT_QUOTES, 'UTF-8'); ?></h5>
            <?php if (!empty($ou['university'])): ?>
              <p class="mb-1"><?php echo htmlspecialchars($ou['university'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <?php if (!empty($ou['major'])): ?>
              <p class="mb-1"><?php echo htmlspecialchars($ou['major'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <p class="small text-muted mb-0">詳しく見る</p>
          </div>
        </div>
      </a>
    </div>
  <?php endforeach; ?>
</div>

<hr class="my-5">

<h2>全体チャット</h2>
<div class="mb-3">
  <form method="post" class="d-flex">
    <input type="text" name="global_message" class="form-control me-2" placeholder="メッセージを入力" required>
    <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> 送信</button>
  </form>
</div>

<ul class="list-group">
  <?php foreach ($global_chat as $gc_msg): ?>
    <?php 
      $gc_name = $gc_msg['name'] ?? '名無し';
      $gc_acid = $gc_msg['account_id'] ?? '';
      if ($gc_acid !== '') {
          $gc_acid = '('.$gc_acid.')';
      }
    ?>
    <li class="list-group-item">
      <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#profileModal" data-userid="<?php echo $gc_msg['user_id']; ?>">
        <strong><?php echo htmlspecialchars($gc_name.$gc_acid, ENT_QUOTES, 'UTF-8'); ?></strong>
      </a>： <?php echo nl2br(htmlspecialchars($gc_msg['message'], ENT_QUOTES, 'UTF-8')); ?>
      <br><small class="text-muted"><?php echo htmlspecialchars($gc_msg['created_at'], ENT_QUOTES, 'UTF-8'); ?></small>
    </li>
  <?php endforeach; ?>
</ul>

<!-- プロフィール表示用モーダル -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" id="profileModalContent">
      <div class="modal-header">
        <h5 class="modal-title">ユーザープロフィール</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>読み込み中...</p>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function(){
  var profileModal = document.getElementById('profileModal');
  profileModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var userId = button.getAttribute('data-userid');
    var modalBody = profileModal.querySelector('.modal-body');
    modalBody.innerHTML = '読み込み中...';

    fetch('get_profile.php?user_id=' + userId)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          var html = '';
          if (data.profile.icon_image) {
            html += '<img src="uploads/'+ data.profile.icon_image +'" style="max-width:100px; border-radius:50%;" class="mb-2"><br>';
          }
          html += '名前: '+(data.profile.name ? data.profile.name : '未設定')+'<br>';
          if (data.profile.account_id) {
            html += 'アカウントID: '+data.profile.account_id+'<br>';
          }
          if (data.profile.university) {
            html += '大学: '+data.profile.university+'<br>';
          }
          if (data.profile.major) {
            html += '専攻: '+data.profile.major+'<br>';
          }
          if (data.profile.github_url) {
            html += 'GitHub: <a href="'+data.profile.github_url+'" target="_blank">'+data.profile.github_url+'</a><br>';
          }
          if (data.profile.twitter_url) {
            html += 'X(Twitter): <a href="'+data.profile.twitter_url+'" target="_blank">'+data.profile.twitter_url+'</a><br>';
          }
          if (data.profile.homepage_url) {
            html += 'HP: <a href="'+data.profile.homepage_url+'" target="_blank">'+data.profile.homepage_url+'</a><br>';
          }

          // 就活状況表示(公開されている場合)
          if (data.profile.is_status_public == 1 && data.job_status) {
            html += '<hr><h6>就活状況</h6>';
            if (data.job_status.status_selection) {
              html += '選考状況: '+data.job_status.status_selection+'<br>';
            }
            if (data.job_status.desired_industry) {
              html += '志望業界: '+data.job_status.desired_industry+'<br>';
            }
            if (data.job_status.decided_companies) {
              html += '内定済み企業:<br>'+data.job_status.decided_companies.replace(/\n/g,"<br>")+'<br>';
            }
            if (data.job_status.in_process_companies) {
              html += '選考中企業:<br>'+data.job_status.in_process_companies.replace(/\n/g,"<br>")+'<br>';
            }
            if (data.job_status.es_not_submitted_companies) {
              html += 'ES未提出企業:<br>'+data.job_status.es_not_submitted_companies.replace(/\n/g,"<br>")+'<br>';
            }
            if (data.job_status.pivot_points) {
              html += '企業選びの軸3選:<br>'+data.job_status.pivot_points.replace(/\n/g,"<br>")+'<br>';
            }
          }

          if (data.profile.uid && current_user_id && data.profile.uid != current_user_id) {
            html += '<hr><a href="dm.php?with='+data.profile.uid+'" class="btn btn-primary">DMを送る</a>';
          }
          modalBody.innerHTML = html;
        } else {
          modalBody.innerHTML = 'プロフィール情報を取得できませんでした。';
        }
      });
  });
});
</script>