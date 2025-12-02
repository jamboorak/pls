<?php
require_once __DIR__ . '/../api/db.php';

// Check if last_login column exists in admins table
$check = $conn->query("SHOW COLUMNS FROM admins LIKE 'last_login'");

if ($check->num_rows === 0) {
    // Add last_login column if it doesn't exist
    $sql = "ALTER TABLE admins ADD COLUMN last_login TIMESTAMP NULL DEFAULT NULL AFTER password_hash";
    if ($conn->query($sql) === TRUE) {
        echo "Successfully added last_login column to admins table\n";
    } else {
        echo "Error adding last_login column: " . $conn->error . "\n";
    }
} else {
    echo "last_login column already exists in admins table\n";
}

// Also ensure last_ip column exists for consistency
$check = $conn->query("SHOW COLUMNS FROM admins LIKE 'last_ip'");
if ($check->num_rows === 0) {
    // Add last_ip column if it doesn't exist
    $sql = "ALTER TABLE admins ADD COLUMN last_ip VARCHAR(45) DEFAULT NULL AFTER last_login";
    if ($conn->query($sql) === TRUE) {
        echo "Successfully added last_ip column to admins table\n";
    } else {
        echo "Error adding last_ip column: " . $conn->error . "\n";
    }
} else {
    echo "last_ip column already exists in admins table\n";
}

echo "Database update complete. You can now log in to see the last login time in the admin list.";
?>
