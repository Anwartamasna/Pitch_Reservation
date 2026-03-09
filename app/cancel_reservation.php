<?php
require_once __DIR__ . '/includes/functions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: my_reservations.php');
  exit;
}

if (!verifyCSRF($_POST['csrf_token'] ?? '')) {
  setFlash('danger', 'Invalid form submission.');
  header('Location: my_reservations.php');
  exit;
}

$reservation_id = (int) ($_POST['reservation_id'] ?? 0);

$db = getDB();

// Only allow cancelling own future reservations
$stmt = $db->prepare(
  'DELETE FROM reservations
     WHERE id = :rid AND user_id = :uid AND reservation_date >= CURRENT_DATE'
);
$stmt->execute([':rid' => $reservation_id, ':uid' => currentUserId()]);

if ($stmt->rowCount() > 0) {
  setFlash('success', 'Reservation cancelled successfully.');
} else {
  setFlash('danger', 'Unable to cancel reservation (already past or not yours).');
}

header('Location: my_reservations.php');
exit;
