<?php
$pageTitle = 'Home';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section" data-aos="fade-up">
  <div class="row align-items-center gy-5">
    <div class="col-lg-6">
      <span class="hero-badge">⚽ Football Pitch Reservation</span>
      <h1 class="hero-title">Book Your Perfect <span class="gradient-text">Football Pitch</span></h1>
      <p class="hero-lead">
        Find and reserve premium football pitches in seconds.<br>
        Choose your time slot from <strong>9:00 AM</strong> to <strong>12:00 AM</strong> and play on your terms.
      </p>
      <div class="hero-cta d-flex flex-wrap gap-3">
        <?php if (isLoggedIn()): ?>
          <a href="pitches.php" class="btn btn-primary-gradient btn-lg"><i class="bi bi-grid me-2"></i>Browse Pitches</a>
          <a href="my_reservations.php" class="btn btn-outline-light btn-lg"><i class="bi bi-calendar-check me-2"></i>My
            Reservations</a>
        <?php else: ?>
          <a href="register.php" class="btn btn-primary-gradient btn-lg"><i class="bi bi-person-plus me-2"></i>Get
            Started</a>
          <a href="login.php" class="btn btn-outline-light btn-lg"><i class="bi bi-box-arrow-in-right me-2"></i>Sign
            In</a>
        <?php endif; ?>
      </div>
    </div>
    <div class="col-lg-6 text-center" data-aos="zoom-in" data-aos-delay="200">
      <div class="hero-illustration">
        <i class="bi bi-dribbble"></i>
      </div>
    </div>
  </div>
</section>

<!-- Features Section -->
<section class="features-section" data-aos="fade-up" data-aos-delay="100">
  <div class="row g-4">
    <div class="col-md-4">
      <div class="feature-card">
        <div class="feature-icon"><i class="bi bi-search"></i></div>
        <h3>Browse Pitches</h3>
        <p>Explore our premium football pitches across multiple cities with real-time availability.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-card">
        <div class="feature-icon"><i class="bi bi-clock-history"></i></div>
        <h3>Hourly Booking</h3>
        <p>Reserve in 1-hour blocks from 9 AM to midnight. Flexible scheduling for any team size.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-card">
        <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
        <h3>Instant Confirmation</h3>
        <p>Get immediate booking confirmation. No double-bookings — our system prevents conflicts.</p>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>