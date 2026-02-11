<?php
require "db.php";

// change these
$username = "admin";
$newPassword = "admin123";

$hashed = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE username = ?");
mysqli_stmt_bind_param($stmt, "ss", $hashed, $username);
mysqli_stmt_execute($stmt);

echo "✅ Password updated for $username<br>";
echo "You can now login with: <b>$newPassword</b>";
