<?php
require_once __DIR__ . '/session_helper.php';
require_once __DIR__ . '/../api/db.php';

// Initialize session and check login
startDevAdminSession();
requireDevAdminLogin();

// Handle actions
$message = '';

// Get all admins
$admins = [];
$result = $conn->query("SELECT * FROM admins ORDER BY id DESC");
if ($result) {
    $admins = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dev Admin Dashboard - Barangay San Antonio 1</title>
    <!-- Tailwind config + CDN -->
    <script src="../assets/js/tailwind-config.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom styles -->
    <link rel="stylesheet" href="../assets/css/styles.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .bg-brgy-primary {
            background-color: #1e40af;
        }
        .text-brgy-primary {
            color: #1e40af;
        }
        .border-brgy-primary {
            border-color: #1e40af;
        }
        .hover\:bg-brgy-primary:hover {
            background-color: #1e40af;
        }
        .header-nav-link {
            @apply px-3 py-2 rounded-md text-sm font-medium text-white hover:bg-blue-700 transition-colors duration-200;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <header class="bg-brgy-primary shadow-xl sticky top-0 z-20">
        <div class="container mx-auto px-4 py-3 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="text-center sm:text-left">
                <h1 class="text-2xl md:text-3xl font-extrabold text-white drop-shadow-lg tracking-tight">Dev Admin Dashboard</h1>
                <p class="text-sm md:text-base text-blue-100">Barangay San Antonio 1 Management Portal</p>
            </div>
            <nav class="w-full sm:w-auto flex flex-wrap items-center justify-center sm:justify-end gap-3">
                <a href="../index.php" class="header-nav-link">Public Portal</a>
                <a href="../admin.php" class="header-nav-link">Admin Panel</a>
                <a href="logout.php" class="header-nav-link">
                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                </a>
            </nav>
        </div>
    </header>

        <main class="container mx-auto p-4 md:p-6">
            <?php if ($message): ?>
                <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700 rounded-r">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle mr-3 text-xl"></i>
                        <p><?php echo htmlspecialchars($message); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Admin Accounts Section -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8 border border-gray-200">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-200">
                    <div>
                        <h3 class="text-xl font-bold text-brgy-primary">Admin Accounts</h3>
                        <a href="create_account.php" class="inline-flex items-center px-4 py-2 border-2 border-brgy-primary rounded-lg text-sm font-bold text-brgy-primary bg-white hover:bg-blue-50 hover:shadow-md transition-all duration-200">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Create New Admin
                        </a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-blue-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Username</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Last Login</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($admins as $admin): ?>
                            <tr class="hover:bg-blue-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">#<?php echo htmlspecialchars($admin['id']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($admin['username']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($admin['last_login']): ?>
                                        <div class="flex items-center text-sm text-gray-600">
                                            <i class="far fa-clock text-blue-500 mr-2"></i>
                                            <?php echo date('M j, Y g:i A', strtotime($admin['last_login'])); ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-600">Never logged in</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
    <div class="flex space-x-3">
        <a href="edit_admin.php?id=<?php echo $admin['id']; ?>" class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 px-3 py-1 rounded transition-colors duration-200">
            <i class="fas fa-edit mr-1"></i> Edit
        </a>
        <a href="#" onclick="confirmDelete(<?php echo $admin['id']; ?>)" class="text-red-600 hover:text-red-800 hover:bg-red-50 px-3 py-1 rounded transition-colors duration-200">
            <i class="fas fa-trash-alt mr-1"></i> Delete
        </a>
    </div>
</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Security Settings Section -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-200">
                    <h3 class="text-xl font-bold text-brgy-primary">Security Settings</h3>
                    <p class="mt-1 text-sm text-blue-600">Manage security-related settings</p>
                </div>
                <div class="p-6">
                    <div class="bg-white rounded-lg border border-blue-100 p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xl">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold text-gray-900">Account Security</h4>
                                <p class="mt-1 text-sm text-gray-600">Update your password and secure your account</p>
                                <div class="mt-4">
                                    <a href="change_password.php" class="inline-flex items-center px-4 py-2 border-2 border-brgy-primary rounded-lg text-sm font-bold text-brgy-primary hover:bg-blue-50 hover:shadow-md transition-all duration-200">
                                        <i class="fas fa-key mr-2"></i>
                                        Change Password
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white mt-12 py-6">
            <div class="container mx-auto px-4 text-center">
                <p class="text-sm text-gray-300">
                    &copy; <?php echo date('Y'); ?> Barangay San Antonio 1. All rights reserved.
                </p>
                <p class="text-xs text-gray-400 mt-2">
                    Dev Admin Dashboard v1.0.0
                </p>
            </div>
        </footer>
    </div>

    <script>
        // SweetAlert2 confirmation for delete actions
        function confirmDelete(adminId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                    confirmButton: 'px-4 py-2 rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500',
                    cancelButton: 'px-4 py-2 mr-2 rounded-md text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'delete_admin.php?id=' + adminId;
                }
            });
        }

        // Display success/error messages using SweetAlert2
        <?php if (isset($_SESSION['success'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '<?php echo addslashes($_SESSION['success']); ?>',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        <?php unset($_SESSION['success']); endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '<?php echo addslashes($_SESSION['error']); ?>',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        <?php unset($_SESSION['error']); endif; ?>
    </script>
</body>
</html>
