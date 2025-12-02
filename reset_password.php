<?php
require_once 'api/db.php';
session_start();

$message = '';
$error = '';
$validToken = false;
$token = $_GET['token'] ?? '';

if (empty($token)) {
    header('Location: forgot_password.php');
    exit;
}

// Check if token is valid
$stmt = $conn->prepare("SELECT id, username FROM admins WHERE reset_token = ? AND reset_expires > NOW() LIMIT 1");
$stmt->bind_param('s', $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $error = 'Invalid or expired reset token. Please request a new password reset.';
} else {
    $validToken = true;
    $admin = $result->fetch_assoc();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (empty($password) || empty($confirmPassword)) {
            $error = 'Please fill in all fields';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters long';
        } else {
            // Update password and clear reset token
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE admins SET password_hash = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
            $update->bind_param('si', $hashedPassword, $admin['id']);
            
            if ($update->execute()) {
                $message = 'Your password has been reset successfully. You can now <a href="admin.php" class="text-blue-600 hover:underline">login</a> with your new password.';
                $validToken = false; // Prevent form from being shown again
            } else {
                $error = 'Error updating password. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Barangay San Antonio 1</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .video-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }
        .video-bg video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.35;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <!-- Video Background -->
    <div class="video-bg">
        <video autoplay muted loop playsinline>
            <source src="vid.mp4" type="video/mp4">
        </video>
    </div>

    <div class="w-full max-w-md bg-white/90 backdrop-blur-sm rounded-2xl shadow-2xl overflow-hidden border-2 border-white/20">
        <div class="p-8">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-pink-600">Reset Your Password</h2>
                <p class="text-gray-600 mt-2">Enter your new password below</p>
            </div>
            
            <?php if (!empty($message)): ?>
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($validToken): ?>
            <form method="POST" class="space-y-6">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input type="password" id="password" name="password" required minlength="8"
                           class="w-full p-3 border-2 border-pink-200 rounded-lg focus:ring-pink-500 focus:border-pink-500" 
                           placeholder="Enter new password">
                </div>
                
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="8"
                           class="w-full p-3 border-2 border-pink-200 rounded-lg focus:ring-pink-500 focus:border-pink-500" 
                           placeholder="Confirm new password">
                </div>
                
                <button type="submit" class="w-full py-3 bg-pink-600 text-white font-medium text-lg rounded-lg hover:bg-pink-700 hover:shadow-lg transition-all duration-200">
                    Reset Password
                </button>
            </form>
            <?php endif; ?>
            
            <div class="text-center pt-4">
                <a href="admin.php" class="text-sm text-pink-600 hover:text-pink-800 hover:underline transition-colors duration-200">
                    Back to Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>
