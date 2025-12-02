<?php
require_once __DIR__ . '/session_helper.php';
require_once __DIR__ . '/../api/db.php';

// Initialize session and check login
startDevAdminSession();

// Check if user is logged in as dev admin
if (!isDevAdminLoggedIn()) {
    header('HTTP/1.1 403 Forbidden');
    echo 'Access denied';
    exit;
}

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';
$response = ['success' => false, 'message' => ''];

try {
    switch ($action) {
        case 'delete_admin':
            $adminId = (int)($_POST['admin_id'] ?? 0);
            
            // Prevent deleting the last admin
            $count = $conn->query("SELECT COUNT(*) as count FROM admins")->fetch_assoc()['count'];
            if ($count <= 1) {
                throw new Exception('Cannot delete the last admin account');
            }
            
            $stmt = $conn->prepare("DELETE FROM admins WHERE id = ?");
            $stmt->bind_param('i', $adminId);
            
            if ($stmt->execute()) {
                $response = [
                    'success' => true,
                    'message' => 'Admin account deleted successfully'
                ];
            } else {
                throw new Exception('Failed to delete admin account');
            }
            break;
            
        case 'change_password':
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                throw new Exception('All fields are required');
            }
            
            if ($newPassword !== $confirmPassword) {
                throw new Exception('New passwords do not match');
            }
            
            if (strlen($newPassword) < 8) {
                throw new Exception('New password must be at least 8 characters long');
            }
            
            // Verify current password
            $stmt = $conn->prepare("SELECT password_hash FROM dev_admins WHERE id = ?");
            $stmt->bind_param('i', $_SESSION['dev_admin_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
                throw new Exception('Current password is incorrect');
            }
            
            // Update password
            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE dev_admins SET password_hash = ? WHERE id = ?");
            $update->bind_param('si', $newHash, $_SESSION['dev_admin_id']);
            
            if ($update->execute()) {
                $response = [
                    'success' => true,
                    'message' => 'Password changed successfully'
                ];
            } else {
                throw new Exception('Failed to update password');
            }
            break;
            
        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
?>
