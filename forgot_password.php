<?php
require_once 'api/db.php';
session_start();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    
    // Validate username
    if (empty($username)) {
        $error = 'Please enter your username';
    } else {
        // Check if username exists in the database
        $stmt = $conn->prepare("SELECT id, username FROM admins WHERE username = ? LIMIT 1");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store token in database
            $update = $conn->prepare("UPDATE admins SET reset_token = ?, reset_expires = ? WHERE id = ?");
            $update->bind_param('ssi', $token, $expires, $admin['id']);
            
            if ($update->execute()) {
                // For demo purposes, we'll show the reset link directly
                // In production, you would send an email with this link
                $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=" . $token;
                $message = "Password reset link generated. For this demo, here's your reset link: <br><br>" . 
                          "<a href='$resetLink' class='text-blue-600 hover:underline break-all'>$resetLink</a>";
            } else {
                $error = 'Error generating reset token. Please try again.';
            }
        } else {
            $error = 'No admin account found with that username.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Barangay San Antonio 1</title>
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
                <h2 class="text-3xl font-bold text-pink-600">Reset Password</h2>
                <p class="text-gray-600 mt-2">Enter your username to receive a password reset link</p>
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
            
            <form method="POST" class="space-y-6">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" id="username" name="username" required 
                           class="w-full p-3 border-2 border-pink-200 rounded-lg focus:ring-pink-500 focus:border-pink-500" 
                           placeholder="Enter your username">
                </div>
                
                <button type="submit" class="w-full py-3 bg-pink-600 text-white font-medium text-lg rounded-lg hover:bg-pink-700 hover:shadow-lg transition-all duration-200">
                    Send Reset Link
                </button>
                
                <div class="text-center pt-2">
                    <a href="admin.php" class="text-sm text-pink-600 hover:text-pink-800 hover:underline transition-colors duration-200">
                        Back to Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
