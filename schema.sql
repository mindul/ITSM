-- IT Asset Integrated Management System Schema

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Assets Table
CREATE TABLE IF NOT EXISTS assets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    model_name VARCHAR(100) NOT NULL,
    serial_number VARCHAR(100) UNIQUE,
    ip_address VARCHAR(45),
    vlan_info VARCHAR(50),
    location VARCHAR(100), -- e.g., Rack No.
    status ENUM('사용중', '재고', '폐기', '수리중') DEFAULT '재고',
    manager_name VARCHAR(50),
    introduction_date DATE,
    expiration_date DATE,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- History/Audit Logs Table
CREATE TABLE IF NOT EXISTS history_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_id INT NOT NULL,
    change_type ENUM('등록', '수정', '삭제', '상태변경') NOT NULL,
    details TEXT,
    worker_name VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE
);

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'User') DEFAULT 'User',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Initial Data
INSERT IGNORE INTO categories (name, description) VALUES 
('서버', '물리 및 가상 서버'),
('네트워크', 'L2/L3 스위치'),
('보안', '방화벽 및 IPS'),
('기타', '기타 주변 장치');

-- Default Admin (password: admin123)
INSERT IGNORE INTO users (username, password, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin');
