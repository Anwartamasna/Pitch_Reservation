<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Define base URL for absolute routing
define('BASE_URL', '/');

// CSRF token helpers
function generateCSRF(): string
{
  if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf_token'];
}

function verifyCSRF(string $token): bool
{
  return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function csrfField(): string
{
  return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generateCSRF()) . '">';
}

// Auth helpers
function isLoggedIn(): bool
{
  return isset($_SESSION['user_id']);
}

function requireLogin(): void
{
  if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
  }
}

function currentUserId(): ?int
{
  return $_SESSION['user_id'] ?? null;
}

function currentUserName(): ?string
{
  return $_SESSION['user_name'] ?? null;
}

function currentUserRole(): ?string
{
  return $_SESSION['user_role'] ?? null;
}

function isAdmin(): bool
{
  return currentUserRole() === 'admin';
}

function requireAdmin(): void
{
  requireLogin();
  if (!isAdmin()) {
    setFlash('danger', 'Access denied. Admin privileges required.');
    header('Location: index.php');
    exit;
  }
}

// Flash message helpers
function setFlash(string $type, string $message): void
{
  $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
  $flash = $_SESSION['flash'] ?? null;
  unset($_SESSION['flash']);
  return $flash;
}

function renderFlash(): string
{
  $flash = getFlash();
  if (!$flash)
    return '';
  $type = htmlspecialchars($flash['type']);
  $msg = htmlspecialchars($flash['message']);
  return "<div class=\"alert alert-{$type} alert-dismissible fade show\" role=\"alert\">
                {$msg}
                <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button>
            </div>";
}
