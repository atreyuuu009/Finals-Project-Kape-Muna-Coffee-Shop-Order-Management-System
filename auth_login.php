<?php
require "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

$login = trim($_POST["login"] ?? "");
$password = $_POST["password"] ?? "";

if ($login === "" || $password === "") {
    $_SESSION["auth_error"] = "Username/email and password are required.";
    header("Location: login.php");
    exit;
}

$stmt = mysqli_prepare($conn, "
    SELECT user_id, first_name, last_name, username, email, password, role
    FROM users
    WHERE username = ? OR email = ?
    LIMIT 1
");
mysqli_stmt_bind_param($stmt, "ss", $login, $login);
mysqli_stmt_execute($stmt);

$res = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$user) {
    $_SESSION["auth_error"] = "Invalid username/email or password.";
    header("Location: login.php");
    exit;
}

if (!password_verify($password, $user["password"])) {
    $_SESSION["auth_error"] = "Invalid username/email or password.";
    header("Location: login.php");
    exit;
}

// success
$_SESSION["user"] = [
    "user_id" => (int)$user["user_id"],
    "name"    => $user["first_name"] . " " . $user["last_name"],
    "username"=> $user["username"],
    "email"   => $user["email"],
    "role"    => $user["role"]
];

header("Location: home.php");
exit;
