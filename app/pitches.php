<?php
$pageTitle = 'Pitches';
require_once __DIR__ . '/includes/functions.php';
requireLogin();

$db = getDB();
$stmt = $db->query('SELECT * FROM pitches ORDER BY name');
$pitches = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-header" data-aos="fade-up">
  <h1><i class="bi bi-grid me-2"></i>Available Pitches</h1>
  <p class="text-muted">Choose a pitch and reserve your slot</p>
</section>

<section data-aos="fade-up" data-aos-delay="100">
  <div class="row g-4">
    <?php foreach ($pitches as $pitch): ?>
      <div class="col-lg-6 col-xl-3">
        <div class="pitch-card">
          <div class="pitch-card-visual">
            <i class="bi bi-dribbble"></i>
          </div>
          <div class="pitch-card-body">
            <h3>
              <?= htmlspecialchars($pitch['name']) ?>
            </h3>
            <p class="pitch-location"><i class="bi bi-geo-alt me-1"></i>
              <?= htmlspecialchars($pitch['location']) ?>
            </p>
            <div class="pitch-price">
              <span class="price-value">
                <?= number_format($pitch['price_per_hour'], 2) ?> MAD
              </span>
              <span class="price-unit">/ hour</span>
            </div>
            <a href="reserve.php?pitch_id=<?= $pitch['id'] ?>" class="btn btn-primary-gradient w-100 mt-3">
              <i class="bi bi-calendar-plus me-2"></i>Reserve Now
            </a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>