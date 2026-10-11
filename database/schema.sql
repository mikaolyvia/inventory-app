CREATE DATABASE IF NOT EXISTS inventory_musik;
USE inventory_musik;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'staff') NOT NULL DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL
);

CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NULL,
    address TEXT NULL
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    sku VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    price DECIMAL(12, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    minimum_stock INT NOT NULL DEFAULT 5,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) 
        ON UPDATE CASCADE 
        ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS product_suppliers (
    product_id INT NOT NULL,
    supplier_id INT NOT NULL,
    supplier_price DECIMAL(12, 2) DEFAULT 0,
    PRIMARY KEY (product_id, supplier_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS stock_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    type ENUM('IN', 'OUT') NOT NULL,
    quantity INT NOT NULL,
    notes VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
);

-- Data Pengguna (password login default: admin123 & staff123)
INSERT INTO users (username, password_hash, full_name, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Melodi', 'admin'),
('staff', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Staff Gudang', 'staff');

INSERT INTO categories (name, description) VALUES
('Gitar & Bass', 'Akustik, elektrik, dan bass gitar'),
('Keyboard & Piano', 'Synthesizer, digital piano, dan MIDI controller'),
('Perkusi & Drum', 'Drum akustik, elektrik pad, dan simbal'),
('Audio & Mic', 'Microphone, amplifier, dan soundcard');

INSERT INTO suppliers (name, phone, address) VALUES
('PT Yamaha Music Indo', '021-5551234', 'Jakarta Barat'),
('CV Fender Distributor', '022-7778899', 'Bandung');

INSERT INTO products (category_id, sku, name, price, stock, minimum_stock) VALUES
(1, 'GTR-001', 'Fender Stratocaster Sunburst', 14500000, 8, 3),
(1, 'GTR-002', 'Yamaha F310 Akustik', 1650000, 2, 5),
(2, 'KEY-001', 'Roland XPS-10 Synthesizer', 8200000, 4, 2),
(3, 'DRM-001', 'Pearl Roadshow 5-Piece Drum', 7500000, 1, 3);