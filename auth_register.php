<?php
require "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: register.php");
    exit;
}

$first_name = trim($_POST["firstName"] ?? "");
$last_name  = trim($_POST["lastName"] ?? "");
$email      = trim($_POST["email"] ?? "");
$phone      = trim($_POST["phone"] ?? "");
$username   = trim($_POST["username"] ?? "");
$password   = $_POST["password"] ?? "";
$role       = trim($_POST["role"] ?? "");

// Server-side required checks
if ($first_name === "" || $last_name === "" || $email === "" || $username === "" || $password === "" || $role === "") {
    $_SESSION["auth_error"] = "Please complete all required fields.";
    header("Location: register.php");
    exit;
}

// Allow only these roles from your UI
$allowed_roles = ["staff", "cashier", "manager"];
if (!in_array($role, $allowed_roles, true)) {
    $_SESSION["auth_error"] = "Invalid role selected.";
    header("Location: register.php");
    exit;
}

// Password policy (match your JS: 8+ letters & numbers)
if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*#?&]{8,}$/', $password)) {
    $_SESSION["auth_error"] = "Password must be 8+ characters with letters and numbers.";
    header("Location: register.php");
    exit;
}

$hashed = password_hash($password, PASSWORD_DEFAULT);

// Check duplicate username/email
$check = mysqli_prepare($conn, "SELECT user_id FROM users WHERE email = ? OR username = ? LIMIT 1");
mysqli_stmt_bind_param($check, "ss", $email, $username);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) > 0) {
    mysqli_stmt_close($check);
    $_SESSION["auth_error"] = "Email or username already exists.";
    header("Location: register.php");
    exit;
}
mysqli_stmt_close($check);

// Insert
$stmt = mysqli_prepare($conn, "INSERT INTO users (first_name, last_name, email, phone, username, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "sssssss", $first_name, $last_name, $email, $phone, $username, $hashed, $role);

if (!mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    $_SESSION["auth_error"] = "Registration failed. Please try again.";
    header("Location: register.php");
    exit;
}

mysqli_stmt_close($stmt);

// Success -> go to login with new=true banner
header("Location: login.php?new=true");
exit;
