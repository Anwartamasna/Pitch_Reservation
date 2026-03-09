<?php
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$db = getDB();

// Stats
$totalUsers = $db->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$totalPitches = $db->query("SELECT COUNT(*) FROM pitches")->fetchColumn();
$totalReservations = $db->query("SELECT COUNT(*) FROM reservations")->fetchColumn();
$todayReservations = $db->query("SELECT COUNT(*) FROM reservations WHERE reservation_date = CURRENT_DATE")->fetchColumn();
$upcomingReservations = $db->query("SELECT COUNT(*) FROM reservations WHERE reservation_date >= CURRENT_DATE")->fetchColumn();

// Recent reservations
$recent = $db->query(
  'SELECT r.*, u.name AS user_name, u.email AS user_email, p.name AS pitch_name, p.location AS pitch_location
     FROM reservations r
     JOIN users u ON u.id = r.user_id
     JOIN pitches p ON p.id = r.pitch_id
     ORDER BY r.created_at DESC
     LIMIT 10'
)->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-header" data-aos="fade-up">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h1><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</h1>
      <p class="text-muted mb-0">Overview of the reservation system</p>
    </div>
    <div class="d-flex gap-2">
      <a href="reservations.php" class="btn btn-primary-gradient"><i class="bi bi-calendar-check me-2"></i>All
        Reservations</a>
      <a href="users.php" class="btn btn-outline-light"><i class="bi bi-people me-2"></i>Manage Users</a>
    </div>
  </div>
</section>

<!-- Stats Cards -->
<section data-aos="fade-up" data-aos-delay="100">
  <div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
      <div class="admin-stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #38bdf8, #818cf8)"><i
            class="bi bi-people-fill"></i></div>
        <div>
          <div class="stat-value">
            <?= $totalUsers ?>
          </div>
          <div class="stat-label">Registered Users</div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="admin-stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #22d3ee, #38bdf8)"><i
            class="bi bi-dribbble"></i></div>
        <div>
          <div class="stat-value">
            <?= $totalPitches ?>
          </div>
          <div class="stat-label">Pitches</div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="admin-stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #34d399, #22d3ee)"><i
            class="bi bi-calendar-check-fill"></i></div>
        <div>
          <div class="stat-value">
            <?= $totalReservations ?>
          </div>
          <div class="stat-label">Total Reservations</div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="admin-stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #fbbf24, #f97316)"><i
            class="bi bi-clock-fill"></i></div>
        <div>
          <div class="stat-value">
            <?= $todayReservations ?>
          </div>
          <div class="stat-label">Today's Bookings</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Recent Reservations -->
<section data-aos="fade-up" data-aos-delay="200">
  <div class="admin-panel">
    <div class="admin-panel-header">
      <h3><i class="bi bi-clock-history me-2"></i>Recent Reservations</h3>
      <a href="reservations.php" class="btn btn-sm btn-outline-light">View All</a>
    </div>
    <?php if (empty($recent)): ?>
      <p class="text-muted p-3">No reservations yet.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table reservation-table admin-table align-middle mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>User</th>
              <th>Pitch</th>
              <th>Date</th>
              <th>Time</th>
              <th>Booked On</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recent as $r): ?>
              <?php
              $startLabel = date('g:i A', strtotime($r['start_time']));
              $endLabel = ($r['end_time'] === '00:00:00' || $r['end_time'] === '00:00') ? '12:00 AM' : date('g:i A', strtotime($r['end_time']));
              ?>
              <tr>
                <td class="text-muted">
                  <?= $r['id'] ?>
                </td>
                <td>
                  <strong>
                    <?= htmlspecialchars($r['user_name']) ?>
                  </strong><br>
                  <small class="text-muted">
                    <?= htmlspecialchars($r['user_email']) ?>
                  </small>
                </td>
                <td><i class="bi bi-dribbble me-1 text-primary"></i>
                  <?= htmlspecialchars($r['pitch_name']) ?>
                </td>
                <td>
                  <?= date('D, M j, Y', strtotime($r['reservation_date'])) ?>
                </td>
                <td>
                  <?= $startLabel ?> –
                  <?= $endLabel ?>
                </td>
                <td><small class="text-muted">
                    <?= date('M j, g:i A', strtotime($r['created_at'])) ?>
                  </small></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>