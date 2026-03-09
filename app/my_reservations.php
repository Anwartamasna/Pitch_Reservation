<?php
$pageTitle = 'My Reservations';
require_once __DIR__ . '/includes/functions.php';
requireLogin();

$db = getDB();
$stmt = $db->prepare(
  'SELECT r.*, p.name AS pitch_name, p.location AS pitch_location, p.price_per_hour
     FROM reservations r
     JOIN pitches p ON p.id = r.pitch_id
     WHERE r.user_id = :uid
     ORDER BY r.reservation_date DESC, r.start_time DESC'
);
$stmt->execute([':uid' => currentUserId()]);
$reservations = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-header" data-aos="fade-up">
  <h1><i class="bi bi-calendar-check me-2"></i>My Reservations</h1>
  <p class="text-muted">View and manage your upcoming bookings</p>
</section>

<section data-aos="fade-up" data-aos-delay="100">
  <?php if (empty($reservations)): ?>
    <div class="empty-state">
      <div class="empty-icon"><i class="bi bi-calendar-x"></i></div>
      <h3>No Reservations Yet</h3>
      <p class="text-muted">You haven't made any bookings. Browse our pitches and reserve your favourite!</p>
      <a href="pitches.php" class="btn btn-primary-gradient"><i class="bi bi-grid me-2"></i>Browse Pitches</a>
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table reservation-table align-middle">
        <thead>
          <tr>
            <th>Pitch</th>
            <th>Location</th>
            <th>Date</th>
            <th>Time</th>
            <th>Price</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($reservations as $r): ?>
            <?php
            $isFuture = $r['reservation_date'] >= date('Y-m-d');
            $startLabel = date('g:i A', strtotime($r['start_time']));
            $endLabel = ($r['end_time'] === '00:00:00' || $r['end_time'] === '00:00') ? '12:00 AM' : date('g:i A', strtotime($r['end_time']));
            // Calculate hours
            $sHour = (int) date('G', strtotime($r['start_time']));
            $eHour = (int) date('G', strtotime($r['end_time']));
            if ($eHour === 0)
              $eHour = 24;
            $hours = $eHour - $sHour;
            $total = $hours * (float) $r['price_per_hour'];
            ?>
            <tr class="<?= $isFuture ? '' : 'table-row-past' ?>">
              <td class="fw-semibold"><i class="bi bi-dribbble me-1 text-primary"></i>
                <?= htmlspecialchars($r['pitch_name']) ?>
              </td>
              <td><i class="bi bi-geo-alt me-1"></i>
                <?= htmlspecialchars($r['pitch_location']) ?>
              </td>
              <td>
                <?= date('D, M j, Y', strtotime($r['reservation_date'])) ?>
              </td>
              <td>
                <?= $startLabel ?> –
                <?= $endLabel ?>
              </td>
              <td>
                <?= number_format($total, 2) ?> MAD
              </td>
              <td>
                <?php if ($isFuture): ?>
                  <form method="POST" action="cancel_reservation.php" class="d-inline"
                    onsubmit="return confirm('Cancel this reservation?')">
                    <?= csrfField() ?>
                    <input type="hidden" name="reservation_id" value="<?= $r['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-circle me-1"></i>Cancel</button>
                  </form>
                <?php else: ?>
                  <span class="badge bg-secondary">Past</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>