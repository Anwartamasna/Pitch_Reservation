<?php
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: users.php');
  exit;
}

if (!verifyCSRF($_POST['csrf_token'] ?? '')) {
  setFlash('danger', 'Invalid form submission.');
  header('Location: users.php');
  exit;
}

$user_id = (int) ($_POST['user_id'] ?? 0);

// Prevent self-deletion
if ($user_id === currentUserId()) {
  setFlash('danger', 'You cannot delete your own account.');
  header('Location: users.php');
  exit;
}

$db = getDB();

// CASCADE will also delete user's reservations
$stmt = $db->prepare('DELETE FROM users WHERE id = :id');
$stmt->execute([':id' => $user_id]);

if ($stmt->rowCount() > 0) {
  setFlash('success', "User #$user_id and their reservations deleted.");
} else {
  setFlash('danger', 'User not found.');
}

header('Location: users.php');
exit;
