<?php
require_once __DIR__ . '/session_helper.php';
require_once __DIR__ . '/../api/db.php';

// Start the session
startDevAdminSession();

// Check if dev admin is logged in
if (!isset($_SESSION['dev_admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    try {
        // Basic validation
        if (empty($username) || empty($password) || empty($confirmPassword)) {
            throw new Exception('All fields are required');
        }
        
        if (strlen($username) < 3) {
            throw new Exception('Username must be at least 3 characters');
        }
        
        if (strlen($password) < 8) {
            throw new Exception('Password must be at least 8 characters');
        }
        
        if ($password !== $confirmPassword) {
            throw new Exception('Passwords do not match');
        }
        
        // Check if username exists
        $stmt = $conn->prepare("SELECT id FROM admins WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception('Username already exists');
        }
        
        // Hash password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $createdAt = date('Y-m-d H:i:s');
        
        // Insert into admins table
        $stmt = $conn->prepare("
            INSERT INTO admins (
                username, 
                password_hash, 
                created_at
            ) VALUES (?, ?, ?)
        ");
        
        $stmt->bind_param('sss', $username, $passwordHash, $createdAt);
        
        if ($stmt->execute()) {
            $success = 'Admin account created successfully!';
            // Clear form
            $username = '';
        } else {
            throw new Exception('Failed to create admin account');
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
    <title>Create Admin Account - Dev Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-brgy-primary { background-color: #1e40af; }
        .text-brgy-primary { color: #1e40af; }
        .border-brgy-primary { border-color: #1e40af; }
        .focus\:ring-blue-500:focus { --tw-ring-color: rgba(59, 130, 246, 0.5); }
        .focus\:border-blue-500:focus { border-color: #3b82f6; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <div class="mb-6">
                <a href="index.php" class="text-blue-600 hover:text-blue-800 font-medium">&larr; Back to Dashboard</a>
            </div>
            
            <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">Create Admin Account</h1>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="space-y-4">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" id="username" name="username" required 
                           value="<?php echo htmlspecialchars($username ?? ''); ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           autocomplete="off">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           autocomplete="new-password">
                </div>
                
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           autocomplete="new-password">
                </div>
                
                <div class="pt-2">
                    <button type="submit" 
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Create Admin Account
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Focus on username field when page loads
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').focus();
        });
    </script>
</body>
</html>