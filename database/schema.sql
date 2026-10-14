-- Bangladesh Students Community Contest Platform Database Schema

CREATE DATABASE IF NOT EXISTS bsc_contest CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bsc_contest;

-- Admins Table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Submissions Table
CREATE TABLE IF NOT EXISTS submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL,
    semester VARCHAR(20) NOT NULL,
    pdf_file VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings Table
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Logs Table
CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action VARCHAR(100) NOT NULL,
    details TEXT NULL,
    ip_address VARCHAR(45) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default admin account
-- Default username: admin
-- Default password: Admin@ADTU2026
INSERT INTO admins (username, password, email)
VALUES ('admin', '$2y$10$JvF2MuP0rayQy6LmhaKf3eWPN/URCqLmYbeH8jx4QwDlOSiUsf8iy', 'admin@adtu-bsc.com')
ON DUPLICATE KEY UPDATE id=id;

-- Seed default settings
INSERT INTO settings (setting_key, setting_value) VALUES
('contest_title', 'Bangladesh Students Community'),
('contest_topic', 'আমি যা হতে চেয়েছিলাম এবং যা হতে যাচ্ছি'),
('contest_deadline', '2026-06-30 23:59:59'),
('is_contest_active', '1'),
('max_upload_size', '10485760')
ON DUPLICATE KEY UPDATE setting_key=setting_key;
