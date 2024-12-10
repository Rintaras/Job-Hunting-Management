<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="hero-section">
  <div class="container">
    <h1 class="fw-bold">就活生同士でつながる、学び成長する</h1>
    <p>就職活動の情報共有・進捗管理・ネットワーキングをここで。</p>
    <?php if (!isLoggedIn()): ?>
      <a href="register.php" class="btn btn-primary btn-lg me-2">新規登録</a>
      <a href="login.php" class="btn btn-success btn-lg">ログイン</a>
    <?php else: ?>
      <a href="dashboard.php" class="btn btn-primary btn-lg">マイページへ</a>
    <?php endif; ?>
  </div>

  <div id="carouselExample" class="carousel slide mb-5" data-bs-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active text-center p-5">
      <h5>利用者の声1</h5>
      <p class="text-muted">「他の就活生のプロフィールや状況を見て、自分の軸も見つかりました。とても刺激的！」 - Aさん</p>
    </div>
    <div class="carousel-item text-center p-5">
      <h5>利用者の声2</h5>
      <p class="text-muted">「ES提出や面接日程を一元管理できて助かります。忙しい就活中には手放せないサイト！」 - Bさん</p>
    </div>
    <div class="carousel-item text-center p-5">
      <h5>利用者の声3</h5>
      <p class="text-muted">「志望業界が似ている人と交流できるので情報共有がスムーズ。」 - Cさん</p>
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>
</div>

<div class="row g-4 mb-5">
  <div class="col-md-4">
    <div class="card h-100 text-center p-3">
      <div class="card-body">
        <h5 class="card-title">他の就活生との繋がり</h5>
        <p class="card-text">プロフィールを公開して仲間とつながろう。</p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card h-100 text-center p-3">
      <div class="card-body">
        <h5 class="card-title">詳細な就活状況管理</h5>
        <p class="card-text">志望業界や内定・選考中企業情報を管理しやすく。</p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card h-100 text-center p-3">
      <div class="card-body">
        <h5 class="card-title">スケジュール&時間管理</h5>
        <p class="card-text">面接日程やES締切を日付・時間で管理可能。</p>
      </div>
    </div>
  </div>
</div>

<div class="text-center mb-5">
  <h2 class="mb-3">コミュニティに参加しよう</h2>
  <p class="fs-5">他の就活生の動向から学び、知識を共有し、就活を前進させよう。</p>
  <a href="dashboard.php" class="btn btn-outline-primary btn-lg">マイページを確認</a>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
