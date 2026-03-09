<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: reservations.php');
  exit;
}

if (!verifyCSRF($_POST['csrf_token'] ?? '')) {
  setFlash('danger', 'Invalid form submission.');
  header('Location: reservations.php');
  exit;
}

$reservation_id = (int) ($_POST['reservation_id'] ?? 0);

$db = getDB();
$stmt = $db->prepare('DELETE FROM reservations WHERE id = :rid');
$stmt->execute([':rid' => $reservation_id]);

if ($stmt->rowCount() > 0) {
  setFlash('success', 'Reservation #' . $reservation_id . ' deleted successfully.');
} else {
  setFlash('danger', 'Reservation not found.');
}

header('Location: reservations.php');
exit;
