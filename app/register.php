<?php
$pageTitle = 'Register';
require_once __DIR__ . '/includes/functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!verifyCSRF($_POST['csrf_token'] ?? '')) {
    $errors[] = 'Invalid form submission.';
  }

  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $confirm = $_POST['password_confirm'] ?? '';

  if ($name === '')
    $errors[] = 'Name is required.';
  if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    $errors[] = 'Valid email is required.';
  if (strlen($password) < 6)
    $errors[] = 'Password must be at least 6 characters.';
  if ($password !== $confirm)
    $errors[] = 'Passwords do not match.';

  if (empty($errors)) {
    $db = getDB();

    // Check duplicate email
    $stmt = $db->prepare('SELECT id FROM users WHERE email = :email');
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
      $errors[] = 'An account with this email already exists.';
    } else {
      $hash = password_hash($password, PASSWORD_BCRYPT);
      $stmt = $db->prepare('INSERT INTO users (name, email, password) VALUES (:name, :email, :password)');
      $stmt->execute([':name' => $name, ':email' => $email, ':password' => $hash]);
      setFlash('success', 'Registration successful! Please log in.');
      header('Location: login.php');
      exit;
    }
  }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="auth-section" data-aos="fade-up">
  <div class="row justify-content-center">
    <div class="col-lg-5 col-md-7">
      <div class="auth-card">
        <div class="auth-card-header">
          <div class="auth-icon"><i class="bi bi-person-plus-fill"></i></div>
          <h2>Create Account</h2>
          <p class="text-muted">Join PitchBook and start reserving</p>
        </div>

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

        <form method="POST" action="register.php" class="auth-form">
          <?= csrfField() ?>

          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="name" name="name" placeholder="Full Name"
              value="<?= htmlspecialchars($name ?? '') ?>" required>
            <label for="name"><i class="bi bi-person me-1"></i>Full Name</label>
          </div>

          <div class="form-floating mb-3">
            <input type="email" class="form-control" id="email" name="email" placeholder="Email"
              value="<?= htmlspecialchars($email ?? '') ?>" required>
            <label for="email"><i class="bi bi-envelope me-1"></i>Email Address</label>
          </div>

          <div class="form-floating mb-3">
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required
              minlength="6">
            <label for="password"><i class="bi bi-lock me-1"></i>Password</label>
          </div>

          <div class="form-floating mb-4">
            <input type="password" class="form-control" id="password_confirm" name="password_confirm"
              placeholder="Confirm" required>
            <label for="password_confirm"><i class="bi bi-lock-fill me-1"></i>Confirm Password</label>
          </div>

          <button type="submit" class="btn btn-primary-gradient w-100 btn-lg">
            <i class="bi bi-check-circle me-2"></i>Register
          </button>
        </form>

        <p class="text-center mt-4 mb-0">
          Already have an account? <a href="login.php" class="fw-semibold">Log in</a>
        </p>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>