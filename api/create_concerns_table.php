<?php
require_once __DIR__ . '/db.php';

$sql = "CREATE TABLE IF NOT EXISTS resident_concerns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) DEFAULT 'Anonymous',
    message TEXT NOT NULL,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('new', 'in_progress', 'resolved') DEFAULT 'new',
    admin_notes TEXT
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'resident_concerns' created successfully or already exists.\n";
} else {
    echo "Error creating table: " . $conn->error . "\n";
}

$conn->close();
?>
