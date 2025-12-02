-- Create dev_admins table
CREATE TABLE IF NOT EXISTS `dev_admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_ip` varchar(45) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default dev admin account (username: devadmin, password: devadmin1)
INSERT INTO `dev_admins` (`username`, `password_hash`) 
VALUES ('devadmin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi') 
ON DUPLICATE KEY UPDATE `password_hash` = VALUES(`password_hash`);
