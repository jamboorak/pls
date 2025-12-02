<?php
session_start();
require_once __DIR__ . '/../api/db.php';

// Check if user is logged in as dev admin
if (!isset($_SESSION['dev_admin_logged_in']) || $_SESSION['dev_admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Get pagination parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Get total number of logs
$totalLogs = $conn->query("SELECT COUNT(*) as count FROM security_logs")->fetch_assoc()['count'];
$totalPages = ceil($totalLogs / $perPage);

// Get logs for current page
$logs = [];
$result = $conn->query("SELECT * FROM security_logs ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
if ($result) {
    $logs = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Logs - Dev Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900">Security Logs</h1>
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="text-sm text-indigo-600 hover:text-indigo-800">Back to Dashboard</a>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Security Events</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Monitor login attempts and security-related events</p>
                </div>
                <div class="border-t border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event Type</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Agent</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($logs as $log): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('M j, Y g:i A', strtotime($log['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php 
                                            $typeClass = 'bg-gray-100 text-gray-800';
                                            if (strpos(strtolower($log['event_type']), 'fail') !== false) {
                                                $typeClass = 'bg-red-100 text-red-800';
                                            } elseif (strpos(strtolower($log['event_type']), 'success') !== false) {
                                                $typeClass = 'bg-green-100 text-green-800';
                                            }
                                            echo $typeClass;
                                        ?>">
                                        <?php echo htmlspecialchars($log['event_type']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo htmlspecialchars($log['ip_address']); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <div class="truncate max-w-xs" title="<?php echo htmlspecialchars($log['user_agent']); ?>">
                                        <?php echo htmlspecialchars(substr($log['user_agent'], 0, 30)) . (strlen($log['user_agent']) > 30 ? '...' : ''); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?php echo htmlspecialchars($log['details']); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium"><?php echo $offset + 1; ?></span> to 
                                <span class="font-medium"><?php echo min($offset + $perPage, $totalLogs); ?></span> of 
                                <span class="font-medium"><?php echo $totalLogs; ?></span> results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?php echo $page - 1; ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <span class="sr-only">Previous</span>
                                        <i class="fas fa-chevron-left h-5 w-5"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <a href="?page=<?php echo $i; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?php echo $i === $page ? 'bg-indigo-50 border-indigo-500 text-indigo-600 z-10' : 'text-gray-500 hover:bg-gray-50'; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <a href="?page=<?php echo $page + 1; ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <span class="sr-only">Next</span>
                                        <i class="fas fa-chevron-right h-5 w-5"></i>
                                    </a>
                                <?php endif; ?>
                            </nav>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
