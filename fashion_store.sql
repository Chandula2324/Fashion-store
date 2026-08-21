-- Fashion Store Database Schema
-- Run this in phpMyAdmin or MySQL CLI

CREATE DATABASE IF NOT EXISTS fashion_store 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE fashion_store;

-- Admin Users Table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    brand VARCHAR(100) NOT NULL,
    category ENUM('Men', 'Women', 'Kids') NOT NULL,
    type VARCHAR(50) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    sizes VARCHAR(100) NOT NULL,
    color VARCHAR(50) NOT NULL,
    image VARCHAR(255) DEFAULT 'default.jpg',
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    customer_address TEXT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_status ENUM('Pending', 'Completed', 'Failed') DEFAULT 'Pending',
    order_status ENUM('Processing', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Processing',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    size VARCHAR(10) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert Default Admin (password: admin123)
INSERT INTO admins (username, password, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@fashionstore.com');

-- Insert Sample Products
INSERT INTO products (name, brand, category, type, description, price, sizes, color, image, stock) VALUES
('Classic Cotton T-Shirt', 'Nike', 'Men', 'Shirts', 'Premium quality cotton t-shirt for everyday wear. Comfortable fit and breathable fabric.', 29.99, 'S,M,L,XL', 'Black', 'shirt1.jpg', 50),
('Slim Fit Jeans', 'Levi's', 'Men', 'Trousers', 'Modern slim fit jeans with stretch fabric. Perfect for casual and semi-formal occasions.', 59.99, 'S,M,L,XL', 'Blue', 'jeans1.jpg', 40),
('Floral Summer Dress', 'Zara', 'Women', 'Dresses', 'Beautiful floral print summer dress. Light and airy fabric perfect for warm days.', 49.99, 'S,M,L', 'Red', 'dress1.jpg', 30),
('Casual Hoodie', 'Adidas', 'Men', 'Shirts', 'Warm and cozy hoodie for cold weather. Features kangaroo pocket and drawstring hood.', 54.99, 'M,L,XL', 'Gray', 'hoodie1.jpg', 25),
('Kids Denim Jacket', 'Gap', 'Kids', 'Shirts', 'Stylish denim jacket for kids. Durable and easy to wash.', 34.99, 'S,M,L', 'Blue', 'jacket1.jpg', 20),
('Elegant Evening Gown', 'H&M', 'Women', 'Dresses', 'Stunning evening gown for special occasions. Elegant design with flowing silhouette.', 89.99, 'S,M,L,XL', 'Navy', 'gown1.jpg', 15),
('Chino Shorts', 'Tommy Hilfiger', 'Men', 'Trousers', 'Classic chino shorts perfect for summer. Comfortable fit with multiple pockets.', 39.99, 'S,M,L,XL', 'Beige', 'shorts1.jpg', 35),
('Kids Printed T-Shirt', 'Disney', 'Kids', 'Shirts', 'Fun printed t-shirt for kids with favorite characters. Soft cotton fabric.', 19.99, 'S,M,L', 'White', 'kidsshirt1.jpg', 45),
('Formal Blazer', 'Calvin Klein', 'Women', 'Shirts', 'Professional formal blazer for work and business meetings. Tailored fit.', 79.99, 'S,M,L,XL', 'Black', 'blazer1.jpg', 20),
('Running Joggers', 'Puma', 'Men', 'Trousers', 'High-performance running joggers with moisture-wicking fabric.', 44.99, 'S,M,L,XL', 'Black', 'joggers1.jpg', 30);
