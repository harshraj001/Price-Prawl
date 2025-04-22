-- PricePrawl Database Schema

-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS priceprawl DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE priceprawl;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email_verified TINYINT(1) DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login DATETIME DEFAULT NULL
) ENGINE=InnoDB;

-- Remember Tokens table for "Remember Me" functionality
CREATE TABLE IF NOT EXISTS remember_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Password Reset Tokens table
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- User Preferences table
CREATE TABLE IF NOT EXISTS user_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    email_alerts TINYINT(1) DEFAULT 1,
    price_drop_threshold INT DEFAULT 10, -- percentage
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Wishlist items table
CREATE TABLE IF NOT EXISTS wishlist_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_url VARCHAR(1000) NOT NULL,
    product_image VARCHAR(1000),
    current_price DECIMAL(10, 2),
    original_price DECIMAL(10, 2),
    retailer VARCHAR(100),
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Price Alerts table
CREATE TABLE IF NOT EXISTS price_alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    wishlist_item_id INT NOT NULL,
    target_price DECIMAL(10, 2) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (wishlist_item_id) REFERENCES wishlist_items(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Price History table
CREATE TABLE IF NOT EXISTS price_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    wishlist_item_id INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    recorded_at DATETIME NOT NULL,
    FOREIGN KEY (wishlist_item_id) REFERENCES wishlist_items(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Alert Notifications table
CREATE TABLE IF NOT EXISTS alert_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    alert_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (alert_id) REFERENCES price_alerts(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- User Activity Log table
CREATE TABLE IF NOT EXISTS user_activity (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activity_type VARCHAR(50) NOT NULL,
    description VARCHAR(255) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Index for faster querying of user activities
CREATE INDEX idx_user_activity_user_timestamp ON user_activity(user_id, timestamp);
CREATE INDEX idx_user_activity_type ON user_activity(activity_type);