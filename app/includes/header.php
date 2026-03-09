<?php require_once __DIR__ . '/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>
    <?= htmlspecialchars($pageTitle ?? 'Pitch Reservation') ?> — Football Pitch Reservation
  </title>
  <meta name="description" content="Reserve football pitches online – choose your favourite pitch and time slot.">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap"
    rel="stylesheet">

  <!-- Bootstrap 5 CSS (CDN) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <!-- AOS Animations -->
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

  <!-- Custom Styles -->
  <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>

  <!-- ============ Navbar ============ -->
  <nav class="navbar navbar-expand-lg fixed-top glass-nav">
    <div class="container">
      <a class="navbar-brand fw-bold" href="index.php">
        <i class="bi bi-dribbble me-2"></i>PitchBook
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav ms-auto align-items-center gap-1">
          <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-house-door me-1"></i>Home</a></li>
          <?php if (isLoggedIn()): ?>
            <li class="nav-item"><a class="nav-link" href="pitches.php"><i class="bi bi-grid me-1"></i>Pitches</a></li>
            <li class="nav-item"><a class="nav-link" href="my_reservations.php"><i
                  class="bi bi-calendar-check me-1"></i>My Reservations</a></li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle me-1"></i>
                <?= htmlspecialchars(currentUserName()) ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end glass-dropdown">
                <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
              </ul>
            </li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="login.php"><i
                  class="bi bi-box-arrow-in-right me-1"></i>Login</a></li>
            <li class="nav-item"><a class="nav-link btn-nav-register" href="register.php">Register</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <main class="main-content">
    <div class="container">
      <?= renderFlash() ?>