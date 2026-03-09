<?php
$pageTitle = 'Reserve';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/timeslots.php';
requireLogin();

$db = getDB();
$errors = [];
$success = false;

// Fetch the selected pitch
$pitch_id = (int) ($_GET['pitch_id'] ?? $_POST['pitch_id'] ?? 0);
$stmt = $db->prepare('SELECT * FROM pitches WHERE id = :id');
$stmt->execute([':id' => $pitch_id]);
$pitch = $stmt->fetch();

if (!$pitch) {
  setFlash('danger', 'Pitch not found.');
  header('Location: pitches.php');
  exit;
}

$timeSlots = generateTimeSlots();

// Handle reservation submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!verifyCSRF($_POST['csrf_token'] ?? '')) {
    $errors[] = 'Invalid form submission.';
  }

  $reservation_date = $_POST['reservation_date'] ?? '';
  $start_time = $_POST['start_time'] ?? '';
  $end_time = $_POST['end_time'] ?? '';

  // Validation
  if ($reservation_date === '')
    $errors[] = 'Date is required.';
  if ($start_time === '')
    $errors[] = 'Start time is required.';
  if ($end_time === '')
    $errors[] = 'End time is required.';

  // Ensure date is not in the past
  if ($reservation_date !== '' && $reservation_date < date('Y-m-d')) {
    $errors[] = 'Cannot reserve in the past.';
  }

  // Ensure start < end (handle midnight edge-case)
  if ($start_time !== '' && $end_time !== '' && $start_time >= $end_time && $end_time !== '00:00') {
    $errors[] = 'End time must be after start time.';
  }

  if (empty($errors)) {
    // Check for overlapping reservations
    $check = $db->prepare(
      'SELECT id FROM reservations
             WHERE pitch_id = :pid
               AND reservation_date = :rdate
               AND start_time < :etime
               AND end_time > :stime'
    );
    $check->execute([
      ':pid' => $pitch_id,
      ':rdate' => $reservation_date,
      ':etime' => $end_time,
      ':stime' => $start_time,
    ]);

    if ($check->fetch()) {
      $errors[] = 'This time slot is already reserved. Please choose a different time.';
    } else {
      $ins = $db->prepare(
        'INSERT INTO reservations (user_id, pitch_id, reservation_date, start_time, end_time)
                 VALUES (:uid, :pid, :rdate, :stime, :etime)'
      );
      $ins->execute([
        ':uid' => currentUserId(),
        ':pid' => $pitch_id,
        ':rdate' => $reservation_date,
        ':stime' => $start_time,
        ':etime' => $end_time,
      ]);
      $success = true;
      setFlash('success', 'Reservation confirmed! Enjoy your game.');
    }
  }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-header" data-aos="fade-up">
  <h1><i class="bi bi-calendar-plus me-2"></i>Reserve a Pitch</h1>
  <p class="text-muted">Book <strong>
      <?= htmlspecialchars($pitch['name']) ?>
    </strong> —
    <?= htmlspecialchars($pitch['location']) ?>
  </p>
</section>

<section data-aos="fade-up" data-aos-delay="100">
  <div class="row justify-content-center">
    <div class="col-lg-7">

      <?php if ($success): ?>
        <div class="success-box text-center">
          <div class="success-icon"><i class="bi bi-check-circle-fill"></i></div>
          <h2>Reservation Confirmed!</h2>
          <p>You have reserved <strong>
              <?= htmlspecialchars($pitch['name']) ?>
            </strong> on
            <strong>
              <?= htmlspecialchars($reservation_date) ?>
            </strong>
            from <strong>
              <?= date('g:i A', strtotime($start_time)) ?>
            </strong>
            to <strong>
              <?= ($end_time === '00:00' ? '12:00 AM' : date('g:i A', strtotime($end_time))) ?>
            </strong>.
          </p>
          <div class="d-flex justify-content-center gap-3 mt-3">
            <a href="my_reservations.php" class="btn btn-primary-gradient"><i class="bi bi-calendar-check me-2"></i>My
              Reservations</a>
            <a href="pitches.php" class="btn btn-outline-light"><i class="bi bi-grid me-2"></i>Browse Pitches</a>
          </div>
        </div>
      <?php else: ?>

        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger">
            <ul class="mb-0">
              <?php foreach ($errors as $e): ?>
                <li>
                  <?= htmlspecialchars($e) ?>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <div class="reserve-card">
          <div class="reserve-pitch-info">
            <div class="d-flex align-items-center gap-3">
              <div class="pitch-icon-sm"><i class="bi bi-dribbble"></i></div>
              <div>
                <h3 class="mb-0">
                  <?= htmlspecialchars($pitch['name']) ?>
                </h3>
                <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>
                  <?= htmlspecialchars($pitch['location']) ?> —
                  <?= number_format($pitch['price_per_hour'], 2) ?> MAD/hr
                </small>
              </div>
            </div>
          </div>

          <form method="POST" action="reserve.php" class="reserve-form">
            <?= csrfField() ?>
            <input type="hidden" name="pitch_id" value="<?= $pitch['id'] ?>">

            <div class="mb-3">
              <label for="reservation_date" class="form-label fw-semibold"><i
                  class="bi bi-calendar-event me-1"></i>Date</label>
              <input type="date" class="form-control form-control-lg" id="reservation_date" name="reservation_date"
                min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($reservation_date ?? date('Y-m-d')) ?>" required>
            </div>

            <div class="row g-3 mb-4">
              <div class="col-6">
                <label for="start_time" class="form-label fw-semibold"><i class="bi bi-clock me-1"></i>Start Time</label>
                <select class="form-select form-select-lg" id="start_time" name="start_time" required>
                  <option value="">Select…</option>
                  <?php foreach ($timeSlots as $slot): ?>
                    <option value="<?= $slot['start'] ?>" <?= (isset($start_time) && $start_time === $slot['start']) ? 'selected' : '' ?>>
                      <?= date('g:i A', strtotime($slot['start'])) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-6">
                <label for="end_time" class="form-label fw-semibold"><i class="bi bi-clock-fill me-1"></i>End Time</label>
                <select class="form-select form-select-lg" id="end_time" name="end_time" required>
                  <option value="">Select…</option>
                  <?php foreach ($timeSlots as $slot): ?>
                    <option value="<?= $slot['end'] ?>" <?= (isset($end_time) && $end_time === $slot['end']) ? 'selected' : '' ?>>
                      <?= ($slot['end'] === '00:00') ? '12:00 AM' : date('g:i A', strtotime($slot['end'])) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <button type="submit" class="btn btn-primary-gradient w-100 btn-lg">
              <i class="bi bi-check2-circle me-2"></i>Confirm Reservation
            </button>
          </form>
        </div>
      <?php endif; ?>

    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>