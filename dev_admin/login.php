<?php
require_once __DIR__ . '/session_helper.php';
require_once __DIR__ . '/../api/db.php';

// Initialize session
startDevAdminSession();

// Redirect if already logged in
if (isDevAdminLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $stmt = $conn->prepare("SELECT id, username, password_hash FROM dev_admins WHERE username = ? AND is_active = 1 LIMIT 1");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['dev_admin_logged_in'] = true;
            $_SESSION['dev_admin_id'] = $user['id'];
            $_SESSION['dev_admin_username'] = $user['username'];
            
            // Update last login info in dev_admins
            $update = $conn->prepare("UPDATE dev_admins SET last_login = NOW(), last_ip = ? WHERE id = ?");
            $update->bind_param('si', $_SERVER['REMOTE_ADDR'], $user['id']);
            $update->execute();
            
            // Also update last login in admins table if the user exists there
            $updateAdmin = $conn->prepare("UPDATE admins SET last_login = NOW() WHERE username = ?");
            $updateAdmin->bind_param('s', $username);
            $updateAdmin->execute();
            
            header('Location: index.php');
            exit;
        }
    }
    
    $error = 'Invalid username or password';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dev Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md border-2 border-blue-400">
            <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">Dev Admin Login</h1>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="space-y-4">
                <div>
                    <label for="username" class="block text-sm font-medium text-black-700">Username</label>
                    <input type="text" id="username" name="username" required 
                           class="mt-1 block w-full px-3 py-2 border-2 border-blue-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="relative">
                    <label for="password" class="block text-sm font-medium text-black-700">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required 
                               class="mt-1 block w-full px-3 py-2 pr-10 border-2 border-blue-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-pink-600 focus:outline-none transition-colors duration-200" onclick="togglePasswordVisibility('password', this)">
                            <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eye-off-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                </div>
                <script>
                    function togglePasswordVisibility(inputId, button) {
                        const input = document.getElementById(inputId);
                        const eyeIcon = button.querySelector('#eye-icon');
                        const eyeOffIcon = button.querySelector('#eye-off-icon');
                        
                        if (input.type === 'password') {
                            input.type = 'text';
                            eyeIcon.classList.add('hidden');
                            eyeOffIcon.classList.remove('hidden');
                        } else {
                            input.type = 'password';
                            eyeIcon.classList.remove('hidden');
                            eyeOffIcon.classList.add('hidden');
                        }
                    }
                </script>
                
                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Sign in
                    </button>
                    <div class="text-center mt-4">
                        <p class="text-sm text-gray-600">Don't have an account?</p>
                        <a href="create_account.php" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                            Create new admin account
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
