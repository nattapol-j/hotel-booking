CREATE DATABASE IF NOT EXISTS hotel_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hotel_db;

CREATE TABLE rooms (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  type VARCHAR(50) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  capacity TINYINT NOT NULL DEFAULT 2,
  description TEXT,
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  room_id INT NOT NULL,
  guest_name VARCHAR(120) NOT NULL,
  guest_phone VARCHAR(20) NOT NULL,
  guest_email VARCHAR(120) NOT NULL,
  check_in DATE NOT NULL,
  check_out DATE NOT NULL,
  guests TINYINT NOT NULL DEFAULT 1,
  total_price DECIMAL(10,2) NOT NULL,
  status ENUM('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (room_id) REFERENCES rooms(id),
  INDEX idx_room_dates (room_id, check_in, check_out)
) ENGINE=InnoDB;

INSERT INTO rooms (name, type, price, capacity, description) VALUES
('ห้อง Standard 101', 'Standard', 1200.00, 2, 'เตียงคิงไซส์ 1 เตียง แอร์ ทีวี Wi-Fi ฟรี'),
('ห้อง Standard 102', 'Standard', 1200.00, 2, 'เตียงเดี่ยว 2 เตียง แอร์ ทีวี Wi-Fi ฟรี'),
('ห้อง Deluxe 201', 'Deluxe', 2200.00, 3, 'วิวสวน ระเบียงส่วนตัว อาหารเช้า 2 ท่าน'),
('ห้อง Family 301', 'Family', 3200.00, 5, 'สองห้องนอน เหมาะสำหรับครอบครัว มีครัวเล็ก'),
('ห้อง Suite 401', 'Suite', 4800.00, 2, 'ห้องชุดชั้นบน วิวเมือง อ่างอาบน้ำ');
