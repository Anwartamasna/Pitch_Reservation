<?php
$pageTitle = 'Manage Users';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$db = getDB();

// Get all users with reservation count
$users = $db->query(
  "SELECT u.*, COUNT(r.id) AS reservation_count
     FROM users u
     LEFT JOIN reservations r ON r.user_id = u.id
     GROUP BY u.id
     ORDER BY u.created_at DESC"
)->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-header" data-aos="fade-up">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
      <h1><i class="bi bi-people me-2"></i>Manage Users</h1>
      <p class="text-muted mb-0">
        <?= count($users) ?> user(s) registered
      </p>
    </div>
    <a href="dashboard.php" class="btn btn-outline-light"><i class="bi bi-arrow-left me-2"></i>Dashboard</a>
  </div>
</section>

<section data-aos="fade-up" data-aos-delay="100">
  <div class="admin-panel">
    <div class="table-responsive">
      <table class="table reservation-table admin-table align-middle mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Reservations</th>
            <th>Joined</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): ?>
            <tr>
              <td class="text-muted">
                <?= $u['id'] ?>
              </td>
              <td class="fw-semibold"><i class="bi bi-person-circle me-1"></i>
                <?= htmlspecialchars($u['name']) ?>
              </td>
              <td>
                <?= htmlspecialchars($u['email']) ?>
              </td>
              <td>
                <?php if ($u['role'] === 'admin'): ?>
                  <span class="badge bg-warning text-dark"><i class="bi bi-shield-fill me-1"></i>Admin</span>
                <?php else: ?>
                  <span class="badge bg-secondary">User</span>
                <?php endif; ?>
              </td>
              <td><span class="badge bg-primary">
                  <?= $u['reservation_count'] ?>
                </span></td>
              <td><small class="text-muted">
                  <?= date('M j, Y', strtotime($u['created_at'])) ?>
                </small></td>
              <td>
                <?php if ($u['id'] !== currentUserId()): ?>
                  <?php if ($u['role'] !== 'admin'): ?>
                    <form method="POST" action="toggle_role.php" class="d-inline"
                      onsubmit="return confirm('Promote this user to admin?')">
                      <?= csrfField() ?>
                      <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                      <input type="hidden" name="new_role" value="admin">
                      <button class="btn btn-sm btn-outline-warning"><i
                          class="bi bi-arrow-up-circle me-1"></i>Promote</button>
                    </form>
                  <?php else: ?>
                    <form method="POST" action="toggle_role.php" class="d-inline"
                      onsubmit="return confirm('Demote this admin to user?')">
                      <?= csrfField() ?>
                      <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                      <input type="hidden" name="new_role" value="user">
                      <button class="btn btn-sm btn-outline-secondary"><i
                          class="bi bi-arrow-down-circle me-1"></i>Demote</button>
                    </form>
                  <?php endif; ?>
                  <form method="POST" action="delete_user.php" class="d-inline ms-1"
                    onsubmit="return confirm('Delete this user and all their reservations?')">
                    <?= csrfField() ?>
                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Delete</button>
                  </form>
                <?php else: ?>
                  <span class="text-muted"><small>Current</small></span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>