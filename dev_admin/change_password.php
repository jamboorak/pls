<?php
session_start();
require_once __DIR__ . '/../api/db.php';

// Check if user is logged in
if (!isset($_SESSION['dev_admin_logged_in']) || $_SESSION['dev_admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    try {
        // Validate inputs
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
            $success = 'Password changed successfully';
        } else {
            throw new Exception('Failed to update password');
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Dev Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <div class="mb-6">
                <a href="index.php" class="text-indigo-600 hover:text-indigo-800">&larr; Back to Dashboard</a>
            </div>
            
            <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">Change Password</h1>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="space-y-4">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                    <input type="password" id="new_password" name="new_password" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">Must be at least 8 characters long</p>
                </div>
                
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div class="pt-2">
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
