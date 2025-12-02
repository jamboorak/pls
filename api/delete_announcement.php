<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$id = (int)$_POST['id'];

// First get the image path if exists
$stmt = $conn->prepare("SELECT image_url FROM posts WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if ($post) {
    // Delete the post
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param('i', $id);
    
    if ($stmt->execute()) {
        // Delete the image file if exists
        if (!empty($post['image_url'])) {
            $imagePath = __DIR__ . '/../' . $post['image_url'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete announcement']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Announcement not found']);
}

$stmt->close();
$conn->close();
?>
