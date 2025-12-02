<?php
session_start();
$isLoggedIn = isset($_SESSION['admin_id']);

// If not logged in, ensure dashboard is hidden
if (!$isLoggedIn) {
    // Force hide dashboard elements
    $showDashboard = false;
    $showPreview = false;
} else {
    $showDashboard = true;
    $showPreview = true;
}
?>
<!DOCTYPE html>
<html><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Barangay San Antonio 1</title>
    <!-- Tailwind config + CDN -->
    <script src="assets/js/tailwind-config.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom styles -->
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        /* Announcements List Styles */
        .announcement-item {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .announcement-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .delete-announcement {
            opacity: 0.7;
            transition: all 0.2s ease;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem;
        }

        .delete-announcement:hover {
            opacity: 1;
            color: #dc2626;
            background-color: #fef2f2;
            border-radius: 9999px;
        }

        #announcements-list {
            max-height: 70vh;
            overflow-y: auto;
            padding-right: 0.5rem;
        }

        /* Custom scrollbar */
        #announcements-list::-webkit-scrollbar {
            width: 6px;
        }

        #announcements-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        #announcements-list::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        #announcements-list::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-brgy-bg min-h-screen">

    <!-- Navbar -->
    <header class="hero-header bg-brgy-primary shadow-xl sticky top-0 z-20">
        <div class="header-content container mx-auto px-4 py-4 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="text-center sm:text-left">
                <h1 class="text-2xl md:text-3xl font-extrabold text-white drop-shadow-lg tracking-tight">Admin Dashboard</h1>
                <p class="header-tagline text-sm md:text-base">Barangay San Antonio 1 Management Portal</p>
            </div>
            <nav class="w-full sm:w-auto flex flex-wrap items-center justify-center sm:justify-end gap-3">
                <a href="index.php" class="header-nav-link">Public Portal</a>
                <a href="#" id="logout-btn" class="header-nav-link <?php echo $isLoggedIn ? '' : 'hidden'; ?>">Logout</a>
            </nav>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="container mx-auto p-4 md:p-8">
        
        <!-- Admin Login Section -->
        <section id="admin-login" class="bg-white p-6 md:p-10 rounded-xl shadow-2xl mb-12">
            <h2 class="text-4xl font-bold text-brgy-primary mb-6 border-b-4 border-brgy-secondary pb-2">Admin Access</h2>
            
            <form id="login-form" class="max-w-md mx-auto p-8 bg-white rounded-xl shadow-xl space-y-6 border-2 border-pink-100 <?php echo $isLoggedIn ? 'hidden' : ''; ?>" onsubmit="attemptLogin(event)">
                <div class="text-center">
                    <h3 class="text-2xl font-bold text-pink-600">Admin Sign In</h3>
                    <p class="text-pink-500 mt-2">Please enter your credentials to continue</p>
                </div>
                <div class="space-y-4">
                    <div>
                        <label for="admin-username" class="block text-sm font-medium text-pink-700 mb-1">Username</label>
                        <input type="text" id="admin-username" class="w-full p-3 border-2 border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200" placeholder="Enter username" autocomplete="username" required>
                    </div>
                    <div class="relative">
                        <label for="admin-pass" class="block text-sm font-medium text-pink-700 mb-1">Password</label>
                        <div class="relative">
                            <input type="password" id="admin-pass" class="w-full p-3 pr-10 border-2 border-pink-200 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200" placeholder="Enter password" autocomplete="current-password" required>
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-pink-600 focus:outline-none transition-colors duration-200" onclick="togglePasswordVisibility('admin-pass', this)">
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
                </div>
                <button type="submit" class="w-full py-3 bg-gradient-to-r from-pink-500 to-pink-600 text-white font-bold text-lg rounded-lg hover:from-pink-600 hover:to-pink-700 hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>
                    <span>Sign In</span>
                </button>
                <div class="text-center pt-2">
                    <a href="forgot_password.php" class="text-sm text-pink-600 hover:text-pink-800 hover:underline transition-colors duration-200">
                        Forgot Password?
                    </a>
                </div>
                <p id="login-message" class="text-center text-red-500 hidden text-sm mt-2"></p>
            </form>

            <!-- Admin Dashboard (Only visible when logged in) -->
            <div id="admin-dashboard" class="mt-8 <?php echo $isLoggedIn ? '' : 'hidden'; ?>">
                <h3 class="text-3xl font-bold text-brgy-primary mb-6">Budget Update Dashboard</h3>
                
                <!-- Tab Navigation for Admin Functions -->
                <div class="flex border-b border-gray-200 mb-6 overflow-x-auto">
                    <button id="tab-update" onclick="showAdminTab('update')" class="tab-button active px-6 py-3 text-base font-bold rounded-t-lg transition-all duration-200 hover:scale-105 hover:shadow-lg whitespace-nowrap">📊 Update Budget</button>
                    <button id="tab-concerns" onclick="showAdminTab('concerns')" class="tab-button px-4 py-2 text-sm font-medium rounded-t-lg text-gray-600 hover:bg-emerald-600 hover:text-white hover:shadow-lg hover:scale-105 transition-all duration-200 whitespace-nowrap">View Concerns</button>
                    <button id="tab-gallery" onclick="showAdminTab('gallery')" class="tab-button px-4 py-2 text-sm font-medium rounded-t-lg text-gray-600 hover:bg-emerald-600 hover:text-white hover:shadow-lg hover:scale-105 transition-all duration-200 whitespace-nowrap">Gallery</button>
                    <button id="tab-manage-announcements" onclick="showAdminTab('manage-announcements')" 
                        class="tab-button px-4 py-2 text-sm font-medium rounded-t-lg text-gray-600 hover:bg-emerald-600 hover:text-white hover:shadow-lg hover:scale-105 transition-all duration-200 whitespace-nowrap">
                        📢 Manage Announcements
                    </button>
                </div>

                <!-- 1. Update Budget Tab -->
                <div id="admin-tab-update" class="space-y-8">
                    <!-- Add New Project Section -->
                    <div class="bg-white border-2 border-brgy-primary rounded-lg shadow-lg p-6 mb-8">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="status-badge status-ongoing">New</span>
                            <h4 class="text-xl font-semibold text-gray-700">Add New Project</h4>
                        </div>
                        <form id="add-project-form" class="space-y-4">
                            <div>
                                <label for="new-category" class="block text-sm font-medium text-gray-700 mb-2">Project/Category Name:</label>
                                <input type="text" id="new-category" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-brgy-primary focus:border-brgy-primary" placeholder="e.g., Road Infrastructure Project">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="new-allocated" class="block text-sm font-medium text-gray-700 mb-2">Allocated Amount (₱):</label>
                                    <input type="number" id="new-allocated" required min="0" step="0.01" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-brgy-primary focus:border-brgy-primary" placeholder="0.00">
                                </div>
                                <div>
                                    <label for="new-spent" class="block text-sm font-medium text-gray-700 mb-2">Spent Amount (₱):</label>
                                    <input type="number" id="new-spent" required min="0" step="0.01" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-brgy-primary focus:border-brgy-primary" placeholder="0.00">
                                </div>
                            </div>
                            <div>
                                <label for="new-status" class="block text-sm font-medium text-gray-700 mb-2">Status:</label>
                                <select id="new-status" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-brgy-primary focus:border-brgy-primary">
                                    <option value="Initial">Initial</option>
                                    <option value="Ongoing">Ongoing</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>
                            <div>
                                <label for="new-progress" class="block text-sm font-medium text-gray-700 mb-2">Project Progress/Process (Optional):</label>
                                <textarea id="new-progress" rows="3" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-brgy-primary focus:border-brgy-primary" placeholder="Describe the project progress, activities, or milestones..."></textarea>
                            </div>
                            <div class="flex justify-start">
                                <button type="submit" class="px-6 py-2 bg-brgy-secondary text-brgy-primary font-bold text-base rounded-lg hover:bg-yellow-400 hover:shadow-xl hover:scale-105 active:scale-100 transition-all duration-200 shadow-md border-2 border-yellow-500">
                                     Add New Project
                                </button>
                            </div>
                            <p id="add-project-message" class="text-center font-medium hidden"></p>
                        </form>
                    </div>

                    <h4 class="text-xl font-semibold mb-4 text-gray-700">Select Item to Update</h4>
                    <select id="budget-item-select" class="w-full p-3 border border-gray-300 rounded-lg mb-6" onchange="loadItemForEdit()">
                        <option value="">-- Select Category --</option>
                        <!-- Options populated by JS -->
                    </select>

                    <form id="update-form" class="p-6 border border-gray-200 rounded-lg bg-gray-50 shadow-md">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Allocated Amount (₱):</label>
                                <input type="number" id="edit-allocated" required="" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-brgy-primary focus:border-brgy-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Spent Amount (₱):</label>
                                <input type="number" id="edit-spent" required="" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-brgy-primary focus:border-brgy-primary">
                            </div>
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status:</label>
                            <select id="edit-status" required="" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-brgy-primary focus:border-brgy-primary">
                                <option value="Initial">Initial</option>
                                <option value="Ongoing">Ongoing</option>
                                <option value="Completed">Completed</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Project Progress/Process:</label>
                            <textarea id="edit-progress" rows="4" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-brgy-primary focus:border-brgy-primary" placeholder="Describe the current progress, activities, milestones, or updates for this project..."></textarea>
                            <p class="text-xs text-gray-500 mt-1">Provide detailed information about the project's current status, activities, and progress.</p>
                        </div>
                        <div class="flex justify-start">
                            <button type="button" onclick="saveBudgetUpdate()" class="px-6 py-2 bg-brgy-secondary text-brgy-primary font-bold text-base rounded-lg hover:bg-yellow-400 hover:shadow-xl hover:scale-105 active:scale-100 transition-all duration-200 shadow-md border-2 border-yellow-500">
                                 Apply Changes
                            </button>
                        </div>
                    </form>
                    <p id="update-message" class="mt-4 text-center font-medium text-green-600 hidden"></p>

                    <div class="bg-white border border-gray-200 rounded-lg shadow-md p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="status-badge status-ongoing">New</span>
                            <h4 class="text-xl font-semibold text-gray-700">Publish Announcement</h4>
                        </div>
                        <form id="admin-post-form" class="space-y-4" enctype="multipart/form-data">
                            <div>
                                <label for="post-title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                <input id="post-title" type="text" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-brgy-primary focus:border-brgy-primary" placeholder="e.g., Road Rehabilitation Update">
                            </div>
                            <div>
                                <label for="post-body" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                                <textarea id="post-body" rows="4" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-brgy-primary focus:border-brgy-primary" placeholder="Share the latest activities, advisories, or announcements..."></textarea>
                            </div>
                            <div>
                                <label for="post-image" class="block text-sm font-medium text-gray-700 mb-1">Image (optional)</label>
                                <input id="post-image" type="file" accept="image/*" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-brgy-primary focus:border-brgy-primary">
                                <p class="text-xs text-gray-500 mt-1">Supported formats: JPG, PNG, GIF, WebP (Max 5MB)</p>
                            </div>
                            <div class="flex justify-start">
                                <button type="submit" class="px-6 py-2 bg-brgy-secondary text-brgy-primary font-bold text-base rounded-lg hover:bg-yellow-400 hover:shadow-xl hover:scale-105 active:scale-100 transition-all duration-200 shadow-md border-2 border-yellow-500">
                                     Publish Update
                                </button>
                            </div>
                            <p id="post-message" class="text-center font-medium hidden"></p>
                        </form>
                    </div>
                </div>
                
                <!-- 2. View Concerns Tab -->
                <div id="admin-tab-concerns" class="hidden">
                    <?php
                    require_once __DIR__ . '/api/db.php';
                    
                    // Fetch all concerns
                    $sql = "SELECT name, message, submission_date FROM resident_concerns ORDER BY submission_date DESC";
                    $result = $conn->query($sql);
                    $totalConcerns = $result->num_rows;
                    ?>
                    
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-brgy-primary">Resident Concerns & Suggestions</h3>
                        <span class="px-3 py-1 bg-brgy-secondary text-brgy-primary rounded-full text-sm font-bold">
                            <?php echo $totalConcerns; ?> total
                        </span>
                    </div>
                    
                    <div class="space-y-6">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <div class="bg-white border rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200">
                                    <div class="p-5">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3 mb-2">
                                                    <h4 class="text-lg font-semibold text-gray-800">
                                                        <?php echo htmlspecialchars($row['name']); ?>
                                                    </h4>
                                                    <span class="text-sm text-gray-500">
                                                        <?php echo date('M j, Y g:i A', strtotime($row['submission_date'])); ?>
                                                    </span>
                                                </div>
                                                <p class="text-gray-700 whitespace-pre-line">
                                                    <?php echo htmlspecialchars($row['message']); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center py-12 bg-white rounded-lg shadow">
                                <p class="text-gray-500 text-lg">No concerns or suggestions have been submitted yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php $conn->close(); ?>
                    
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-xl font-semibold text-gray-700">Citizen Concerns &amp; Recommendations</h4>
                        <div class="flex gap-2">
                            <button onclick="refreshConcerns()" class="px-3 py-1 bg-brgy-primary text-black text-sm rounded-lg border-2 border-black hover:bg-emerald-300">
                                🔄 Refresh
                            </button>
                        </div>
                    </div>
                    <div id="concerns-list" class="space-y-4">
                        <p class="text-gray-500" id="no-concerns">No new concerns or recommendations.</p>
                        <!-- Concerns will be added here -->
                    </div>
                </div>

                <!-- 3. Chat Messages Tab -->
                <div id="admin-tab-chat" class="hidden">
                    <h4 class="text-xl font-semibold mb-4 text-gray-700">User Messages &amp; Conversations</h4>
                    <div id="chat-list" class="space-y-4 mb-6">
                        <p class="text-gray-500" id="no-chats">No messages yet.</p>
                        <!-- Chat conversations will be added here -->
                    </div>
                    
                    <!-- Selected Conversation View -->
                    <div id="chat-conversation" class="hidden bg-white border border-gray-200 rounded-lg shadow-md p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h5 class="text-lg font-semibold text-gray-700" id="conversation-title">Conversation</h5>
                            <button onclick="closeConversation()" class="text-gray-500 hover:text-gray-700">×</button>
                        </div>
                        <div id="conversation-messages" class="h-96 overflow-y-auto p-4 bg-gray-50 rounded-lg mb-4 space-y-2">
                            <!-- Messages will be loaded here -->
                        </div>
                        <div class="flex gap-2">
                            <input type="text" id="admin-reply-input" placeholder="Type your reply..." class="flex-grow p-3 border border-gray-300 rounded-lg focus:ring-brgy-primary focus:border-brgy-primary">
                            <button onclick="sendAdminReply()" class="px-6 py-3 bg-brgy-primary text-white font-bold rounded-lg hover:bg-emerald-700 transition duration-150">Send</button>
                        </div>
                        <input type="hidden" id="current-conversation-id">
                    </div>
                </div>

                <!-- 4. Gallery Management Tab -->
                <div id="admin-tab-gallery" class="hidden">
                    <h4 class="text-xl font-semibold mb-4 text-gray-700">Manage Live Project Gallery</h4>
                    
                    <!-- Add New Image Form -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow-md p-6 mb-6">
                        <h5 class="text-lg font-semibold text-gray-700 mb-4">Add New Image</h5>
                        <form id="gallery-add-form" class="space-y-4" enctype="multipart/form-data">
                            <div>
                                <label for="gallery-image-file" class="block text-sm font-medium text-gray-700 mb-2">Upload Image</label>
                                <input type="file" id="gallery-image-file" accept="image/*" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-brgy-primary focus:border-brgy-primary">
                                <p class="text-xs text-gray-500 mt-1">Supported formats: JPG, PNG, GIF, WebP</p>
                            </div>
                            <div>
                                <label for="gallery-alt-text" class="block text-sm font-medium text-gray-700 mb-2">Alt Text (Description)</label>
                                <input type="text" id="gallery-alt-text" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-brgy-primary focus:border-brgy-primary" placeholder="Description of the image">
                            </div>
                            <div>
                                <label for="gallery-display-order" class="block text-sm font-medium text-gray-700 mb-2">Display Order</label>
                                <input type="number" id="gallery-display-order" value="0" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-brgy-primary focus:border-brgy-primary" placeholder="0">
                                <p class="text-xs text-gray-500 mt-1">Lower numbers appear first. Use 0 for default ordering.</p>
                            </div>
                            <button type="submit" class="w-full py-3 bg-brgy-secondary text-brgy-primary font-bold rounded-lg hover:bg-yellow-400 transition duration-150 shadow-md">Upload Image</button>
                            <p id="gallery-add-message" class="text-center font-medium hidden"></p>
                        </form>
                    </div>

                    <!-- Gallery Images List -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow-md p-6">
                        <h5 class="text-lg font-semibold text-gray-700 mb-4">Current Gallery Images</h5>
                        <div id="gallery-images-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <p class="text-gray-500">Loading gallery images...</p>
                        </div>
                    </div>
                </div>

                <!-- 5. Manage Announcements Tab -->
                <div id="admin-tab-manage-announcements" class="hidden">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-brgy-primary">Manage Announcements</h3>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div id="announcements-list" class="space-y-4">
                            <div class="text-center py-10" id="announcements-loading">
                                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-brgy-primary mx-auto"></div>
                                <p class="mt-2 text-gray-600">Loading announcements...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Announcement Item Template -->
                <template id="announcement-item-template">
                    <div class="announcement-item bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-200" data-id="">
                        <div class="p-5">
                            <div class="flex justify-between items-start gap-4">
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-lg font-semibold text-gray-800 truncate announcement-title"></h4>
                                    <p class="text-sm text-gray-500 mt-1 announcement-date"></p>
                                    <p class="mt-2 text-gray-600 announcement-content line-clamp-2"></p>
                                </div>
                                <button class="delete-announcement p-2 text-red-600 hover:bg-red-50 rounded-full transition-colors flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                            <div class="announcement-image mt-3 rounded-lg overflow-hidden max-h-48 flex items-center justify-center bg-gray-50"></div>
                        </div>
                    </div>
                </template>

            </div>
        </section>

        <!-- Live Projects Preview Section (Only visible when logged in) -->
        <section id="live-projects-preview" class="bg-white p-6 md:p-10 rounded-xl shadow-2xl mb-12 <?php echo $isLoggedIn ? '' : 'hidden'; ?>">
            <h2 class="text-4xl font-bold text-brgy-primary mb-6 border-b-4 border-brgy-secondary pb-2">Live Projects Preview</h2>
            <p class="text-gray-600 mb-8">This is how the gallery appears to users on the public portal.</p>
            
            <div class="bg-gray-900 text-white rounded-xl shadow-2xl p-6 md:p-10">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-3xl font-bold">Barangay in Action</h3>
                        <p class="text-gray-300">Ongoing infrastructure and maintenance efforts around San Antonio 1.</p>
                    </div>
                    <span class="text-sm uppercase tracking-widest text-gray-400">Live Project Highlights</span>
                </div>
                <div class="gallery-marquee">
                    <div class="gallery-track" id="admin-gallery-preview">
                        <!-- Gallery images will be loaded here -->
                    </div>
                </div>
                <p class="mt-4 text-xs text-gray-400">Tip: Hover to pause the scroll.</p>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white p-6 mt-8">
        <div class="container mx-auto text-center">
            <p>© 2025 Barangay San Antonio 1. Budget Transparency Portal, San Pablo City.</p>
        </div>
    </footer>

    <!-- Main application logic -->
    <script defer src="assets/js/admin.js"></script>

    <script>
    // Format date helper function
    function formatDate(dateString) {
        if (!dateString) return 'No date';
        
        try {
            const options = { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return new Date(dateString).toLocaleDateString('en-US', options);
        } catch (error) {
            console.error('Error formatting date:', error);
            return dateString; // Return original string if date parsing fails
        }
    }

    // Load and display announcements
    async function loadAnnouncements() {
        const container = document.getElementById('announcements-list');
        if (!container) {
            console.error('Announcements container not found');
            return;
        }

        // Show loading state
        container.innerHTML = `
            <div class="text-center py-10" id="announcements-loading">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-brgy-primary mx-auto"></div>
                <p class="mt-2 text-gray-600">Loading announcements...</p>
            </div>
        `;

        try {
            console.log('Fetching announcements...');
            const response = await fetch('api/get_posts.php');
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const announcements = await response.json();
            console.log('Announcements data:', announcements);
            
            if (!Array.isArray(announcements) || announcements.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-10">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No announcements yet</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new announcement.</p>
                    </div>
                `;
                return;
            }

            const template = document.getElementById('announcement-item-template');
            if (!template) {
                throw new Error('Announcement item template not found');
            }
            
            container.innerHTML = ''; // Clear loading state

            announcements.forEach(announcement => {
                try {
                    const item = template.content.cloneNode(true);
                    const announcementEl = item.querySelector('.announcement-item');
                    if (!announcementEl) throw new Error('Announcement item element not found in template');
                    
                    announcementEl.dataset.id = announcement.id;
                    
                    const titleEl = item.querySelector('.announcement-title');
                    const dateEl = item.querySelector('.announcement-date');
                    const contentEl = item.querySelector('.announcement-content');
                    
                    if (titleEl) titleEl.textContent = announcement.title || 'No title';
                    if (dateEl) dateEl.textContent = formatDate(announcement.created_at);
                    if (contentEl) contentEl.textContent = announcement.body || 'No content';
                    
                    const imgContainer = item.querySelector('.announcement-image');
                    if (imgContainer) {
                        if (announcement.image_url) {
                            imgContainer.innerHTML = `
                                <img src="${announcement.image_url}" alt="${announcement.title || 'Announcement image'}" 
                                     class="max-h-48 w-full object-cover">
                            `;
                        } else {
                            imgContainer.remove();
                        }
                    }

                    // Add delete handler
                    const deleteBtn = item.querySelector('.delete-announcement');
                    if (deleteBtn) {
                        deleteBtn.addEventListener('click', (e) => {
                            e.stopPropagation();
                            if (confirm('Are you sure you want to delete this announcement? This action cannot be undone.')) {
                                deleteAnnouncement(announcement.id, announcementEl);
                            }
                        });
                    }

                    container.appendChild(item);
                } catch (error) {
                    console.error('Error rendering announcement:', announcement, error);
                }
            });

        } catch (error) {
            console.error('Error loading announcements:', error);
            container.innerHTML = `
                <div class="text-center py-10 text-red-600">
                    <p>Failed to load announcements. Please try again later.</p>
                    <p class="text-sm mt-2">${error.message || 'Unknown error'}</p>
                    <button onclick="loadAnnouncements()" class="mt-4 px-4 py-2 bg-red-100 text-red-700 rounded hover:bg-red-200">
                        Retry
                    </button>
                </div>
            `;
        }
    }

    // Delete an announcement
    async function deleteAnnouncement(id, element) {
        if (!id || !element) return;
        
        try {
            const formData = new FormData();
            formData.append('id', id);
            
            const response = await fetch('api/delete_announcement.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Add fade out animation
                element.style.opacity = '0';
                element.style.transition = 'opacity 0.3s ease';
                
                // Remove from DOM after animation
                setTimeout(() => {
                    element.remove();
                    
                    // Check if no announcements left
                    const container = document.getElementById('announcements-list');
                    if (container && container.children.length === 0) {
                        container.innerHTML = `
                            <div class="text-center py-10">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No announcements</h3>
                                <p class="mt-1 text-sm text-gray-500">All announcements have been deleted.</p>
                            </div>
                        `;
                    }
                }, 300);
            } else {
                throw new Error(result.message || 'Failed to delete announcement');
            }
        } catch (error) {
            console.error('Error deleting announcement:', error);
            alert('Failed to delete announcement. Please try again.');
        }
    }

    // Update the showAdminTab function to load announcements when the tab is clicked
    document.addEventListener('DOMContentLoaded', function() {
        // Add tab click handler
        const manageTab = document.getElementById('tab-manage-announcements');
        if (manageTab) {
            manageTab.addEventListener('click', function() {
                console.log('Manage Announcements tab clicked');
                loadAnnouncements().catch(error => {
                    console.error('Error loading announcements:', error);
                });
            });
        }

        // Add to tabs array if it exists
        if (window.tabs && !window.tabs.includes('manage-announcements')) {
            window.tabs.push('manage-announcements');
        }
    });
    </script>

</body></html>

