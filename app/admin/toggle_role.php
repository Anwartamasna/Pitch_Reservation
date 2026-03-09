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
$new_role = $_POST['new_role'] ?? '';

if (!in_array($new_role, ['user', 'admin'], true)) {
  setFlash('danger', 'Invalid role.');
  header('Location: users.php');
  exit;
}

// Prevent self-demotion
if ($user_id === currentUserId()) {
  setFlash('danger', 'You cannot change your own role.');
  header('Location: users.php');
  exit;
}

$db = getDB();
$stmt = $db->prepare('UPDATE users SET role = :role WHERE id = :id');
$stmt->execute([':role' => $new_role, ':id' => $user_id]);

if ($stmt->rowCount() > 0) {
  $action = $new_role === 'admin' ? 'promoted to admin' : 'demoted to user';
  setFlash('success', "User #$user_id $action successfully.");
} else {
  setFlash('danger', 'User not found.');
}

header('Location: users.php');
exit;
