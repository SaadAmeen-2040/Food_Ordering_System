CREATE DATABASE IF NOT EXISTS food_order_db;
USE food_order_db;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admins Table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Default Admin (username: admin, password: admin123)
-- Hash for 'admin123' using PHP password_hash(PASSWORD_DEFAULT)
INSERT INTO admins (username, password) VALUES 
('admin', '$2y$10$cfuh1mCFslm03vqoFcaRNu4nr.Vxm9HgZNfkTWAVJAJNYgb9ZPYtu') 
ON DUPLICATE KEY UPDATE id=id;

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    image_name VARCHAR(255) NOT NULL,
    active ENUM('Yes', 'No') DEFAULT 'Yes'
);

-- Insert Dummy Categories
INSERT INTO categories (id, title, image_name, active) VALUES 
(1, 'Biryani', 'biryani.jpg', 'Yes'),
(2, 'Karahi', 'karahi.jpg', 'Yes'),
(3, 'BBQ', 'bbq.jpg', 'Yes'),
(4, 'Fast Food', 'fast_food.jpg', 'Yes')
ON DUPLICATE KEY UPDATE id=id;

-- Foods Table
CREATE TABLE IF NOT EXISTS foods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image_name VARCHAR(255) NOT NULL,
    category_id INT NOT NULL,
    active ENUM('Yes', 'No') DEFAULT 'Yes',
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Insert Dummy Foods
INSERT INTO foods (id, title, description, price, image_name, category_id, active) VALUES 
(1, 'Chicken Biryani', 'Spicy and aromatic chicken biryani with raita', 800.00, 'biryani_chicken.jpg', 1, 'Yes'),
(2, 'Beef Biryani', 'Authentic karachi style beef biryani', 1000.00, 'biryani_beef.jpg', 1, 'Yes'),
(3, 'Chicken Karahi', 'Delicious chicken karahi cooked in traditional spices', 1500.00, 'chicken_karahi.jpg', 2, 'Yes'),
(4, 'Mutton Karahi', 'Premium mutton karahi with naan', 2500.00, 'mutton_karahi.jpg', 2, 'Yes'),
(5, 'Chicken Tikka', 'Spicy BBQ chicken tikka piece', 450.00, 'chicken_tikka.jpg', 3, 'Yes'),
(6, 'Zinger Burger', 'Crispy fried chicken breast with mayo and lettuce', 600.00, 'zinger_burger.jpg', 4, 'Yes'),
(7, 'Chicken Broast', 'Quarter chicken broast with fries and bun', 750.00, 'broast.jpg', 4, 'Yes'),
(8, 'Club Sandwich', 'Three-layered toasted sandwich with chicken and egg', 450.00, 'club_sandwich.jpg', 4, 'Yes')
ON DUPLICATE KEY UPDATE id=id;

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Ordered', 'On Delivery', 'Delivered', 'Cancelled') DEFAULT 'Ordered',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    food_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (food_id) REFERENCES foods(id) ON DELETE CASCADE
);

-- Contacts Table
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
