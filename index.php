<?php
require_once __DIR__ . '/api/db.php';

// Preload gallery images for faster rendering
$preloadedGalleryImages = [];
$tableCheck = $conn->query("SHOW TABLES LIKE 'gallery_images'");
if ($tableCheck->num_rows > 0) {
    $sql = 'SELECT id, image_url, alt_text, display_order FROM gallery_images ORDER BY display_order ASC, created_at ASC';
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $preloadedGalleryImages[] = $row;
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Transparency Portal - Barangay San Antonio 1</title>
    <!-- Tailwind config + CDN -->
    <script src="assets/js/tailwind-config.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom styles -->
    <link rel="stylesheet" href="assets/css/styles.css">
    <script>
        // Preload gallery images from PHP
        window.preloadedGalleryImages = <?php echo json_encode($preloadedGalleryImages); ?>;
    </script>
</head>
<body class="bg-brgy-bg min-h-screen">

    <!-- Navbar -->
    <header class="hero-header bg-brgy-primary shadow-xl sticky top-0 z-20">
        <div class="header-content container mx-auto px-4 py-4 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="text-center sm:text-left">
                <h1 class="text-2xl md:text-3xl font-extrabold text-white drop-shadow-lg tracking-tight">Barangay San Antonio 1</h1>
                <p class="header-tagline text-sm md:text-base">Budget Transparency Portal</p>
            </div>
            <nav class="w-full sm:w-auto flex flex-wrap items-center justify-center sm:justify-end gap-2">
                <a href="home.php" class="header-nav-link hover:bg-brgy-secondary hover:text-white px-4 py-2 rounded-full transition-all duration-200">Home</a>
                <a href="#budget-transparency" class="header-nav-link hover:bg-brgy-secondary hover:text-white px-4 py-2 rounded-full transition-all duration-200">Budget Transparency</a>
                <a href="#announcements" class="header-nav-link hover:bg-brgy-secondary hover:text-white px-4 py-2 rounded-full transition-all duration-200">Announcements</a>
                <a href="#barangay-in-action" class="header-nav-link hover:bg-brgy-secondary hover:text-white px-4 py-2 rounded-full transition-all duration-200">Barangay in Action</a>
            </nav>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="container mx-auto p-4 md:p-8">
        
        <!-- Budget Transparency Section -->
        <section id="budget-transparency" class="bg-white p-6 md:p-10 rounded-xl shadow-2xl mb-12 scroll-mt-20">
            <h2 class="text-4xl font-bold text-brgy-primary mb-6 border-b-4 border-brgy-secondary pb-2">Budget Transparency</h2>
            <p class="text-gray-600 mb-8">View the annual budget allocations, expenditures, and project status for Barangay San Antonio 1, San Pablo City.</p>
            
            <!-- Summary Cards -->
            <div id="summary-cards" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Summary cards will be populated by JavaScript -->
            </div>

            <!-- Budget Table -->
            <div class="table-card bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table id="budgetTable" class="w-full">
                        <thead class="bg-brgy-primary">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-bold uppercase tracking-wider text-black">Project/Category</th>
                                <th class="px-6 py-4 text-left text-sm font-bold uppercase tracking-wider text-black">Allocated (₱)</th>
                                <th class="px-6 py-4 text-left text-sm font-bold uppercase tracking-wider text-black">Spent (₱)</th>
                                <th class="px-6 py-4 text-left text-sm font-bold uppercase tracking-wider text-black">Status</th>
                                <th class="px-6 py-4 text-left text-sm font-bold uppercase tracking-wider text-black">Progress/Updates</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Table rows will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Announcements Section -->
        <section id="announcements" class="bg-white p-6 md:p-10 rounded-xl shadow-2xl mb-12 scroll-mt-20">
            <h2 class="text-4xl font-bold text-brgy-primary mb-6 border-b-4 border-brgy-secondary pb-2">Latest Announcements</h2>
            <p class="text-gray-600 mb-8">Stay updated with the latest news, updates, and announcements from Barangay San Antonio 1.</p>
            
            <div id="public-posts" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Posts will be populated by JavaScript -->
            </div>
        </section>

        <!-- Live Projects Gallery Section -->
        <section id="barangay-in-action" class="bg-white p-6 md:p-10 rounded-xl shadow-2xl mb-12 scroll-mt-20">
            <div class="bg-gray-900 text-white rounded-xl shadow-2xl p-6 md:p-10">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-3xl font-bold">Barangay in Action</h3>
                        <p class="text-gray-300">Ongoing infrastructure and maintenance efforts around San Antonio 1.</p>
                    </div>
                    <span class="text-sm uppercase tracking-widest text-gray-400">Live Project Highlights</span>
                </div>
                <div class="gallery-marquee">
                    <div class="gallery-track" id="gallery-track">
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
    <script defer src="assets/js/app.js"></script>

</body></html>

