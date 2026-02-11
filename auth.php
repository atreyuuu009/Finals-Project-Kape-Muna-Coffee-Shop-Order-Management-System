<?php
// auth.php
if (session_status() === PHP_SESSION_NONE) session_start();

function require_login() {
  if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
  }
}

function require_role(array $allowedRoles) {
  require_login();
  $role = $_SESSION['role'] ?? '';
  if (!in_array($role, $allowedRoles, true)) {
    http_response_code(403);
    echo "<h1>403 Forbidden</h1><p>You don't have permission to access this page.</p>";
    exit;
  }
}
