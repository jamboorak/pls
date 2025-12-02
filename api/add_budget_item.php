<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Set JSON content type header at the very beginning
header('Content-Type: application/json');

// Start session after setting headers
session_start();

// Function to send JSON response and exit
function sendJsonResponse($success, $message, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode([
        'success' => $success,
        'message' => $message
    ]);
    exit;
}

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    sendJsonResponse(false, 'Unauthorized. Please log in.', 401);
}

// Get and validate JSON input
$input = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    sendJsonResponse(false, 'Invalid JSON input.', 400);
}

// Validate required fields
$required = ['category', 'allocated', 'spent', 'status'];
foreach ($required as $field) {
    if (!isset($input[$field])) {
        sendJsonResponse(false, "Missing required field: $field", 400);
    }
}

// Sanitize and validate input
$category = trim($input['category']);
$allocated = filter_var($input['allocated'], FILTER_VALIDATE_FLOAT);
$spent = filter_var($input['spent'], FILTER_VALIDATE_FLOAT);
$status = trim($input['status']);
$projectProgress = isset($input['project_progress']) ? trim($input['project_progress']) : null;

if (empty($category)) {
    sendJsonResponse(false, 'Category name is required.', 422);
}

if ($allocated === false || $spent === false || $allocated < 0 || $spent < 0) {
    sendJsonResponse(false, 'Amounts must be positive numbers.', 422);
}

if ($allocated < $spent) {
    sendJsonResponse(false, 'Allocated amount must be greater than or equal to spent amount.', 422);
}

// Database operations
try {
    require_once __DIR__ . '/db.php';
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }
    
    // Set charset to ensure proper encoding
    $conn->set_charset('utf8mb4');
    
    // Check if project_progress column exists, if not add it
    $columnCheck = $conn->query("SHOW COLUMNS FROM budget_allocations LIKE 'project_progress'");
    if ($columnCheck === false) {
        throw new Exception('Error checking for project_progress column: ' . $conn->error);
    }
    
    if ($columnCheck->num_rows == 0) {
        if (!$conn->query("ALTER TABLE budget_allocations ADD COLUMN project_progress TEXT DEFAULT NULL AFTER status")) {
            throw new Exception('Error adding project_progress column: ' . $conn->error);
        }
    }

    // Prepare and execute the insert statement
    $stmt = $conn->prepare('INSERT INTO budget_allocations (category, allocated, spent, status, project_progress) VALUES (?, ?, ?, ?, ?)');
    if ($stmt === false) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }
    
    $stmt->bind_param('sddss', $category, $allocated, $spent, $status, $projectProgress);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to execute statement: ' . $stmt->error);
    }
    
    $newId = $stmt->insert_id;
    $stmt->close();
    
    // Fetch the newly created item
    $selectStmt = $conn->prepare('SELECT id, category, allocated, spent, status, project_progress FROM budget_allocations WHERE id = ? LIMIT 1');
    if ($selectStmt === false) {
        throw new Exception('Failed to prepare select statement: ' . $conn->error);
    }
    
    $selectStmt->bind_param('i', $newId);
    
    if (!$selectStmt->execute()) {
        throw new Exception('Failed to fetch new item: ' . $selectStmt->error);
    }
    
    $result = $selectStmt->get_result();
    $newItem = $result->fetch_assoc();
    $selectStmt->close();
    $conn->close();
    
    // Return success response
    sendJsonResponse(true, 'Project added successfully!', 201, ['newItem' => $newItem]);
    
} catch (Exception $e) {
    // Clean up resources
    if (isset($stmt) && $stmt instanceof mysqli_stmt) {
        $stmt->close();
    }
    if (isset($selectStmt) && $selectStmt instanceof mysqli_stmt) {
        $selectStmt->close();
    }
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
    
    // Log the error for debugging
    error_log('Error in add_budget_item.php: ' . $e->getMessage());
    
    // Send error response
    sendJsonResponse(false, 'An error occurred while processing your request: ' . $e->getMessage(), 500);
}





