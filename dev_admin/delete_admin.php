<?php
require_once __DIR__ . '/session_helper.php';
require_once __DIR__ . '/../api/db.php';

// Start the session and check if dev admin is logged in
startDevAdminSession();
requireDevAdminLogin();

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = 'Invalid admin ID';
    header('Location: index.php');
    exit;
}

$admin_id = (int)$_GET['id'];
$current_admin_id = $_SESSION['dev_admin_id'];

// Prevent self-deletion
if ($admin_id === $current_admin_id) {
    $_SESSION['error'] = 'You cannot delete your own account';
    header('Location: index.php');
    exit;
}

try {
    // Prepare and execute the delete query
    $stmt = $conn->prepare("DELETE FROM admins WHERE id = ?");
    $stmt->bind_param('i', $admin_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['success'] = 'Admin account deleted successfully';
        } else {
            $_SESSION['error'] = 'Admin account not found or already deleted';
        }
    } else {
        throw new Exception('Failed to delete admin account');
    }
    
} catch (Exception $e) {
    $_SESSION['error'] = 'Error: ' . $e->getMessage();
}

// Redirect back to the admin list
header('Location: index.php');
exit;
