<?php
$pageTitle = 'Manage Reservations';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$db = getDB();

// Filters
$filterPitch = $_GET['pitch'] ?? '';
$filterDate = $_GET['date'] ?? '';
$filterUser = $_GET['user'] ?? '';

$where = [];
$params = [];

if ($filterPitch !== '') {
  $where[] = 'r.pitch_id = :pitch';
  $params[':pitch'] = (int) $filterPitch;
}
if ($filterDate !== '') {
  $where[] = 'r.reservation_date = :rdate';
  $params[':rdate'] = $filterDate;
}
if ($filterUser !== '') {
  $where[] = '(u.name ILIKE :uname OR u.email ILIKE :uemail)';
  $params[':uname'] = "%$filterUser%";
  $params[':uemail'] = "%$filterUser%";
}

$sql = 'SELECT r.*, u.name AS user_name, u.email AS user_email, p.name AS pitch_name, p.location AS pitch_location, p.price_per_hour
        FROM reservations r
        JOIN users u ON u.id = r.user_id
        JOIN pitches p ON p.id = r.pitch_id';
if (!empty($where)) {
  $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY r.reservation_date DESC, r.start_time DESC';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$reservations = $stmt->fetchAll();

// Pitches for filter dropdown
$pitches = $db->query('SELECT * FROM pitches ORDER BY name')->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-header" data-aos="fade-up">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h1><i class="bi bi-calendar-check me-2"></i>Manage Reservations</h1>
      <p class="text-muted mb-0">
        <?= count($reservations) ?> reservation(s) found
      </p>
    </div>
    <a href="dashboard.php" class="btn btn-outline-light"><i class="bi bi-arrow-left me-2"></i>Dashboard</a>
  </div>
</section>

<!-- Filters -->
<section data-aos="fade-up" data-aos-delay="50">
  <div class="admin-panel mb-4">
    <form method="GET" class="row g-3 p-3 align-items-end">
      <div class="col-md-3">
        <label class="form-label fw-semibold"><i class="bi bi-funnel me-1"></i>Pitch</label>
        <select class="form-select" name="pitch">
          <option value="">All Pitches</option>
          <?php foreach ($pitches as $p): ?>
            <option value="<?= $p['id'] ?>" <?= ($filterPitch == $p['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($p['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label fw-semibold"><i class="bi bi-calendar me-1"></i>Date</label>
        <input type="date" class="form-control" name="date" value="<?= htmlspecialchars($filterDate) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label fw-semibold"><i class="bi bi-person me-1"></i>User</label>
        <input type="text" class="form-control" name="user" value="<?= htmlspecialchars($filterUser) ?>"
          placeholder="Name or email">
      </div>
      <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary-gradient flex-grow-1"><i
            class="bi bi-search me-1"></i>Filter</button>
        <a href="reservations.php" class="btn btn-outline-light"><i class="bi bi-x-lg"></i></a>
      </div>
    </form>
  </div>
</section>

<!-- Reservations Table -->
<section data-aos="fade-up" data-aos-delay="100">
  <?php if (empty($reservations)): ?>
    <div class="empty-state">
      <div class="empty-icon"><i class="bi bi-calendar-x"></i></div>
      <h3>No Reservations Found</h3>
      <p class="text-muted">Try adjusting your filters.</p>
    </div>
  <?php else: ?>
    <div class="admin-panel">
      <div class="table-responsive">
        <table class="table reservation-table admin-table align-middle mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>User</th>
              <th>Pitch</th>
              <th>Location</th>
              <th>Date</th>
              <th>Time</th>
              <th>Total</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($reservations as $r): ?>
              <?php
              $startLabel = date('g:i A', strtotime($r['start_time']));
              $endLabel = ($r['end_time'] === '00:00:00' || $r['end_time'] === '00:00') ? '12:00 AM' : date('g:i A', strtotime($r['end_time']));
              $sHour = (int) date('G', strtotime($r['start_time']));
              $eHour = (int) date('G', strtotime($r['end_time']));
              if ($eHour === 0)
                $eHour = 24;
              $hours = $eHour - $sHour;
              $total = $hours * (float) $r['price_per_hour'];
              $isFuture = $r['reservation_date'] >= date('Y-m-d');
              ?>
              <tr class="<?= $isFuture ? '' : 'table-row-past' ?>">
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
                <td><small><i class="bi bi-geo-alt me-1"></i>
                    <?= htmlspecialchars($r['pitch_location']) ?>
                  </small></td>
                <td>
                  <?= date('D, M j, Y', strtotime($r['reservation_date'])) ?>
                </td>
                <td>
                  <?= $startLabel ?> –
                  <?= $endLabel ?>
                </td>
                <td class="fw-semibold">
                  <?= number_format($total, 2) ?> MAD
                </td>
                <td>
                  <form method="POST" action="delete_reservation.php" class="d-inline"
                    onsubmit="return confirm('Delete this reservation?')">
                    <?= csrfField() ?>
                    <input type="hidden" name="reservation_id" value="<?= $r['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>