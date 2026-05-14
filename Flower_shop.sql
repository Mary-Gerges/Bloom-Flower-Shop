-- ============================================
-- Bloom Flower Shop - Database Setup
-- Database: flower_shop
-- ============================================

CREATE DATABASE IF NOT EXISTS flower_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE flower_shop;

-- ============================================
-- Table: users
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- Table: flowers
-- ============================================
CREATE TABLE IF NOT EXISTS flowers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    flower_name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) DEFAULT 'default.jpg',
    category VARCHAR(50) NOT NULL,
    description TEXT,
    stock INT DEFAULT 100,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- Table: cart
-- ============================================
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    flower_id INT NOT NULL,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (flower_id) REFERENCES flowers(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- Table: orders
-- ============================================
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- Table: order_items
-- ============================================
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    flower_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (flower_id) REFERENCES flowers(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- Default Admin User (password: Admin@123)
-- ============================================
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@bloomflowers.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- ============================================
-- Sample Flower Data
-- ============================================
INSERT INTO flowers (flower_name, price, image, category, description, stock) VALUES
('Red Rose Bouquet', 29.99, 'rose_red.jpg', 'Roses', 'A classic bouquet of 12 vibrant red roses, perfect for expressing love and passion. Each rose is hand-picked at peak bloom.', 50),
('White Lily Arrangement', 34.99, 'lily_white.jpg', 'Lilies', 'Elegant white lilies arranged in a premium vase. Symbolizes purity and elegance, ideal for weddings and special occasions.', 40),
('Sunflower Bunch', 24.99, 'sunflower.jpg', 'Sunflowers', 'Bright and cheerful sunflowers that bring warmth to any room. A bundle of 6 premium sunflowers with lush green foliage.', 60),
('Pink Peony Mix', 44.99, 'peony_pink.jpg', 'Peonies', 'Lush pink peonies in full bloom, radiating romance and luxury. Perfect for anniversaries and romantic gestures.', 30),
('Lavender Dreams', 27.99, 'lavender.jpg', 'Wildflowers', 'Fresh lavender bundles that fill your space with a calming, beautiful fragrance. Sustainably sourced from local farms.', 45),
('Tulip Rainbow', 32.99, 'tulip_rainbow.jpg', 'Tulips', 'A vibrant mix of multi-colored tulips creating a stunning rainbow effect. 15 stems of fresh-cut tulips in assorted colors.', 55),
('Orchid Elegance', 54.99, 'orchid.jpg', 'Orchids', 'Exotic purple orchids in a modern ceramic pot. A sophisticated gift that lasts for months with minimal care.', 25),
('Daisy Garden', 19.99, 'daisy.jpg', 'Daisies', 'A cheerful arrangement of fresh white and yellow daisies. Perfect for brightening someones day or decorating your home.', 70),
('Carnation Charm', 22.99, 'carnation.jpg', 'Carnations', 'Classic carnations in a mix of soft pink and white tones. Long-lasting freshness with a delicate, sweet fragrance.', 65),
('Garden Rose Bliss', 49.99, 'rose_garden.jpg', 'Roses', 'Premium garden roses in blush and cream tones. A luxurious arrangement that brings the romance of a blooming garden indoors.', 35),
('Blue Hydrangea', 37.99, 'hydrangea.jpg', 'Hydrangeas', 'Stunning blue hydrangea clusters in full bloom. These full, lush flowers make a dramatic statement in any arrangement.', 40),
('Wildflower Meadow', 28.99, 'wildflower.jpg', 'Wildflowers', 'A natural, effortlessly beautiful mix of seasonal wildflowers. Sustainably harvested to bring the essence of the meadow indoors.', 50);