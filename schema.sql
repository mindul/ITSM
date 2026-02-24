-- ITSM Database Schema Final
-- Created: 2026-02-24

-- Categories Table
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Assets Table
CREATE TABLE IF NOT EXISTS `assets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `model_name` varchar(100) NOT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `vlan_info` varchar(50) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `status` enum('사용중','재고','폐기','수리중') DEFAULT '재고',
  `manager_name` varchar(50) DEFAULT NULL,
  `introduction_date` date DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `serial_number` (`serial_number`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `assets_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- History Logs Table
CREATE TABLE IF NOT EXISTS `history_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `change_type` enum('등록','수정','삭제','상태변경') NOT NULL,
  `details` text DEFAULT NULL,
  `worker_name` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `asset_id` (`asset_id`),
  CONSTRAINT `history_logs_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Users Table
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `role` enum('SuperAdmin','Manager','User') DEFAULT 'User',
  `is_approved` tinyint(1) DEFAULT 0,
  `assigned_tasks` text DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Initial Data for Categories
INSERT IGNORE INTO `categories` (`name`, `description`) VALUES 
('서버', '물리 및 가상 서버'),
('네트워크장비', '스위치, 라우터 등'),
('정보보호시스템', '방화벽, IPS 등'),
('기타장비', '기타 IT 자산');

-- Default SuperAdmin (password: Fab2026$$)
INSERT IGNORE INTO `users` (`username`, `name`, `password`, `role`, `is_approved`) VALUES 
('kadmin', '최고관리자', '$2y$10$C8.u6bV0x.lq9oW7Wd7m.eD6F5IuYpD.y.S6S.Y.Z.V.W.X.Y.Z.V', 'SuperAdmin', 1);
