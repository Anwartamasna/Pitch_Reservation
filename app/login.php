<?php
$pageTitle = 'Login';
require_once __DIR__ . '/includes/functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!verifyCSRF($_POST['csrf_token'] ?? '')) {
    $errors[] = 'Invalid form submission.';
  }

  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($email === '' || $password === '') {
    $errors[] = 'Email and password are required.';
  }

  if (empty($errors)) {
    $db = getDB();
    $stmt = $db->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = (int) $user['id'];
      $_SESSION['user_name'] = $user['name'];
      $_SESSION['user_role'] = $user['role'] ?? 'user';
      setFlash('success', 'Welcome back, ' . $user['name'] . '!');
      header('Location: ' . (($user['role'] ?? 'user') === 'admin' ? 'admin/dashboard.php' : 'pitches.php'));
      exit;
    } else {
      $errors[] = 'Invalid email or password.';
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
          <div class="auth-icon"><i class="bi bi-box-arrow-in-right"></i></div>
          <h2>Welcome Back</h2>
          <p class="text-muted">Sign in to your PitchBook account</p>
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

        <form method="POST" action="login.php" class="auth-form">
          <?= csrfField() ?>

          <div class="form-floating mb-3">
            <input type="email" class="form-control" id="email" name="email" placeholder="Email"
              value="<?= htmlspecialchars($email ?? '') ?>" required>
            <label for="email"><i class="bi bi-envelope me-1"></i>Email Address</label>
          </div>

          <div class="form-floating mb-4">
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
            <label for="password"><i class="bi bi-lock me-1"></i>Password</label>
          </div>

          <button type="submit" class="btn btn-primary-gradient w-100 btn-lg">
            <i class="bi bi-arrow-right-circle me-2"></i>Sign In
          </button>
        </form>

        <p class="text-center mt-4 mb-0">
          Don't have an account? <a href="register.php" class="fw-semibold">Register</a>
        </p>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>