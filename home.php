<?php
require_once __DIR__ . '/api/db.php';
$pageTitle = 'Governance For Everyone';
?>
<!DOCTYPE html>
<html><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Barangay San Antonio 1</title>
    <!-- Tailwind config + CDN -->
    <script src="assets/js/tailwind-config.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom styles -->
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        
        .video-content {
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body class="bg-brgy-bg min-h-screen">
    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; overflow: hidden;">
        <video autoplay muted loop playsinline id="bgVideo" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.35;">
            <source src="vid.mp4" type="video/mp4">
        </video>
    </div>
    
    <!-- Navbar -->
    <header class="hero-header bg-brgy-primary shadow-xl sticky top-0 z-20">
        <div class="header-content container mx-auto px-4 py-4 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="text-center sm:text-left">
                <h1 class="text-2xl md:text-3xl font-extrabold text-white drop-shadow-lg tracking-tight">Barangay San Antonio 1</h1>
                <p class="header-tagline text-sm md:text-base">Governance For Everyone</p>
            </div>
            <nav class="w-full sm:w-auto flex flex-wrap items-center justify-center sm:justify-end gap-2">
                <a href="home.php" class="header-nav-link bg-brgy-secondary text-white px-4 py-2 rounded-full transition-all duration-200">Home</a>
                <a href="index.php#budget-transparency" class="header-nav-link hover:bg-brgy-secondary hover:text-white px-4 py-2 rounded-full transition-all duration-200">Budget Transparency</a>
                <a href="index.php#announcements" class="header-nav-link hover:bg-brgy-secondary hover:text-white px-4 py-2 rounded-full transition-all duration-200">Announcements</a>
                <a href="index.php#barangay-in-action" class="header-nav-link hover:bg-brgy-secondary hover:text-white px-4 py-2 rounded-full transition-all duration-200">Barangay in Action</a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto p-4 md:p-8 mt-32">
        <!-- Main Content -->
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold text-black mb-6 drop-shadow-lg">
                Governance For <span class="text-brgy-secondary">Everyone.</span>
            </h1>
            
            <p class="text-lg md:text-xl text-black/90 mb-8 max-w-2xl mx-auto leading-relaxed">
                Welcome to Barangay San Antonio I. We are committed to open data,
                budget transparency and community engagement.
            </p>

            <div class="bg-pink/10 backdrop-blur-sm border border-pink/20 text-black p-4 md:p-6 rounded-xl max-w-md mx-auto my-8">
                <div class="text-3xl font-bold">2025</div>
                <div class="text-xl">FISCAL YEAR</div>
            </div>

            <div class="flex flex-wrap justify-center gap-4 mb-12">
                <button class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-3 px-8 rounded-full transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    Check Insight
                </button>
                <button class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-3 px-8 rounded-full transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    Submit Concerns
                </button>
            </div>

            <!-- Voice of the Community Form -->
            <div class="bg-white/90 backdrop-blur-sm p-6 md:p-8 rounded-xl shadow-2xl max-w-2xl mx-auto border border-white/30">
                <h3 class="text-2xl font-bold text-brgy-primary mb-2">Voice of the Community</h3>
                <p class="text-gray-600 mb-6">If you have a suggestion or concern, submit it here.</p>

                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
                    require_once __DIR__ . '/api/db.php';
                    
                    $name = !empty($_POST['name']) ? $conn->real_escape_string(trim($_POST['name'])) : 'Anonymous';
                    $message = $conn->real_escape_string(trim($_POST['message']));
                    
                    $sql = "INSERT INTO resident_concerns (name, message) VALUES (?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ss", $name, $message);
                    
                    if ($stmt->execute()) {
                        echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                                <strong>Thank you!</strong> Your concern has been submitted successfully.
                              </div>';
                    } else {
                        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                                <strong>Error:</strong> ' . $conn->error . '
                              </div>';
                    }
                    
                    $stmt->close();
                    $conn->close();
                }
                ?>
                <form action="" method="post" class="space-y-4">
                    <div class="text-left">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Your Name (Optional)</label>
                        <input type="text" id="name" name="name" placeholder="Juan Dela Cruz" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brgy-primary focus:border-transparent">
                    </div>
                    
                    <div class="text-left">
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message / Recommendation</label>
                        <textarea id="message" name="message" rows="4" placeholder="I recommend that..." 
                                 class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brgy-primary focus:border-transparent"></textarea>
                    </div>
                    
                    <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-3 px-8 rounded-full transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        Submit & Connect
                    </button>
                </form>
                </div>
            </div>
        </section>
    </main>

    <script>
        // Add any necessary JavaScript here
        document.addEventListener('DOMContentLoaded', function() {
            // Add any page-specific JavaScript here
        });
    </script>
</body>
</html>
