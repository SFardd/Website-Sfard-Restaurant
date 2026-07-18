-- =========================================================
-- SFARD RESTAURANT DATABASE
-- =========================================================

CREATE DATABASE IF NOT EXISTS `sfardresto_db`;
USE `sfardresto_db`;

-- ---------------------------------------------------------
-- TABLE 1: users (login / register / session / role admin-customer)
-- ---------------------------------------------------------
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    role ENUM('admin','customer') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------
-- TABLE 2: categories (kategori menu)
-- ---------------------------------------------------------
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(50) NOT NULL
);

-- ---------------------------------------------------------
-- TABLE 3: menu_items (menu makanan/minuman)
-- ---------------------------------------------------------
CREATE TABLE menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    badge VARCHAR(50) DEFAULT '',
    rating DECIMAL(2,1) DEFAULT 5.0,
    is_spicy TINYINT(1) DEFAULT 0,
    is_veg TINYINT(1) DEFAULT 0,
    is_available TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- ---------------------------------------------------------
-- TABLE 4: reservations (form reservasi meja)
-- ---------------------------------------------------------
CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    kode VARCHAR(20) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telepon VARCHAR(20) NOT NULL,
    tanggal DATE NOT NULL,
    jam VARCHAR(10) NOT NULL,
    tamu INT NOT NULL,
    meja VARCHAR(50),
    paket VARCHAR(50),
    catatan TEXT,
    metode_pembayaran ENUM('transfer') NOT NULL DEFAULT 'transfer',
    bukti_transfer VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ---------------------------------------------------------
-- TABLE 5: messages (form kontak)
-- ---------------------------------------------------------
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    topik VARCHAR(50),
    pesan TEXT NOT NULL,
    rating INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------
-- TABLE 6: orders (pemesanan online dari pesan.php)
-- ---------------------------------------------------------
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    kode VARCHAR(20) NOT NULL,
    nama_pemesan VARCHAR(100) NOT NULL,
    telepon VARCHAR(20) NOT NULL,
    jenis ENUM('dine_in','take_away','delivery') NOT NULL,
    alamat TEXT,
    catatan TEXT,
    ongkir DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) NOT NULL,
    metode_pembayaran ENUM('transfer') NOT NULL DEFAULT 'transfer',
    bukti_transfer VARCHAR(255) NOT NULL,
    status ENUM('pending','diproses','selesai','dibatalkan') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ---------------------------------------------------------
-- TABLE 7: order_items (detail item per order)
-- ---------------------------------------------------------
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    menu_item_id INT NOT NULL,
    nama_menu VARCHAR(100) NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    qty INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- ---------------------------------------------------------
-- SAMPLE DATA: categories
-- ---------------------------------------------------------
INSERT INTO categories (slug, name) VALUES
('makanan_berat', '🍛 Makanan Berat'),
('makanan_ringan', '🥗 Makanan Ringan'),
('minuman', '🥤 Minuman'),
('dessert', '🍮 Dessert');

-- ---------------------------------------------------------
-- SAMPLE DATA: menu_items (migrasi dari array statis menu.php)
-- ---------------------------------------------------------
INSERT INTO menu_items (category_id, name, description, price, image, badge, rating, is_spicy, is_veg, is_available) VALUES
(1, 'Lobster Thermidor', 'Hidangan mewah khas Prancis berupa lobster yang dagingnya dimasak dengan saus krim kaya rasa', 355000, 'lobster.jpg', 'Bestseller', 4.9, 0, 0, 1),
(1, 'Beef Rendang', 'Daging sapi empuk dengan rempah-rempah Minangkabau', 275000, 'rendang.jpg', 'Signature', 4.9, 1, 0, 1),
(1, 'Salmon Grilled', 'Salmon premium seperti Cajun Salmon with Lemon Butter Sauce', 250000, 'salmon.jpg', 'Popular', 4.8, 0, 0, 1),
(2, 'Truffle Fries', 'Kentang goreng tipis dan crispy yang ditaburi parmesan cheese dan minyak truffle, disajikan dengan saus aioli.', 107000, 'trufle.jpg', '', 4.6, 0, 1, 1),
(2, 'Mini Caesar Salad', 'Selada romaine segar dengan dressing caesar khas, parmesan, dan croutons renyah.', 105000, 'salad.jpg', 'New', 4.7, 0, 0, 1),
(3, 'Fresh Orange Juice', 'Jus jeruk segar tanpa tambahan gula, kaya vitamin dan menyegarkan.', 65000, 'orange.jpg', 'Popular', 4.6, 0, 1, 1),
(3, 'Premium Red Grape Juice', 'Jus anggur merah berkualitas tinggi dengan rasa rich dan sedikit tannin alami.', 80000, 'grape.jpg', '', 4.9, 0, 1, 1),
(4, 'Molten Chocolate Lava Cake', 'Kue cokelat hangat dengan bagian dalam meleleh, disajikan dengan vanilla ice cream dan saus cokelat premium.', 130000, 'lava.jpg', 'New', 4.8, 0, 0, 1),
(4, 'Strawberry Panna Cotta', 'Dessert khas Italia dengan tekstur lembut, dipadukan saus stroberi segar yang manis-asam menyegarkan.', 138000, 'cotta.jpg', '', 4.9, 0, 1, 1);

-- ---------------------------------------------------------
-- Default admin account
-- username: admin | password: admin123
-- ---------------------------------------------------------
INSERT INTO users (username, password, email, role) VALUES
('admin', '$2b$12$qXXceWGe84IhJxFcA30d1uk/oKide1eSl3vjyTOCSgmoiIoHmH8wi', 'admin@sfard.com', 'admin');
-- Hash di atas adalah bcrypt dari 'admin123'.
-- Jika suatu saat tidak bisa login, jalankan query berikut di phpMyAdmin
-- setelah membuat akun baru lewat register.php:
-- UPDATE users SET role = 'admin' WHERE username = 'username_kamu';

-- =========================================================
-- MIGRASI (jalankan blok ini SAJA kalau kamu SUDAH PERNAH
-- import versi lama dan tidak mau kehilangan data yang ada.
-- Kalau baru pertama kali import, ABAIKAN blok ini —
-- struktur tabel di atas sudah termasuk kolom-kolom ini.
-- =========================================================
-- ALTER TABLE reservations ADD COLUMN user_id INT NULL AFTER id,
--     ADD COLUMN metode_pembayaran ENUM('transfer') NOT NULL DEFAULT 'transfer' AFTER catatan,
--     ADD COLUMN bukti_transfer VARCHAR(255) NOT NULL AFTER metode_pembayaran,
--     ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;
--
-- ALTER TABLE orders ADD COLUMN user_id INT NULL AFTER id,
--     ADD COLUMN metode_pembayaran ENUM('transfer') NOT NULL DEFAULT 'transfer' AFTER total,
--     ADD COLUMN bukti_transfer VARCHAR(255) NOT NULL AFTER metode_pembayaran,
--     ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;
--
-- Kalau sebelumnya kolom metode_pembayaran udah ada dengan pilihan 'tunai',
-- ganti tipenya jadi transfer-only:
-- ALTER TABLE orders MODIFY metode_pembayaran ENUM('transfer') NOT NULL DEFAULT 'transfer';
