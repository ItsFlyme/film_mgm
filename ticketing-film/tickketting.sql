-- Import file ini lewat phpMyAdmin (http://localhost/phpmyadmin)
CREATE DATABASE IF NOT EXISTS db_ticketing CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE db_ticketing;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','user') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(150) NOT NULL,
  deskripsi TEXT,
  lokasi VARCHAR(150) NOT NULL,
  tanggal DATETIME NOT NULL,
  harga INT NOT NULL DEFAULT 0,
  kuota INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode VARCHAR(20) NOT NULL UNIQUE,
  user_id INT NOT NULL,
  event_id INT NOT NULL,
  jumlah INT NOT NULL,
  total INT NOT NULL,
  status ENUM('lunas','batal') NOT NULL DEFAULT 'lunas',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- Akun admin: admin@tiket.test / admin123
INSERT INTO users (nama,email,password,role) VALUES ('Administrator','admin@tiket.test','$2y$10$WW2G6mypL6lTZI9pYM4dne6/kki9HiSXgJ6bWdUW0BgBHQBVX0guW','admin');

-- Contoh data event
INSERT INTO events (nama,deskripsi,lokasi,tanggal,harga,kuota) VALUES
('Konser Musik Jogja Fest','Festival musik dengan penampilan band lokal dan nasional.','Lapangan Denggung, Sleman','2026-12-20 18:00:00',75000,200),
('Seminar Teknologi Digital','Seminar tentang transformasi digital dan AI untuk mahasiswa.','Auditorium Kampus, Yogyakarta','2026-11-15 09:00:00',25000,100),
('Pameran Seni Rupa','Pameran karya seniman muda Yogyakarta.','Taman Budaya Yogyakarta','2026-11-28 10:00:00',15000,150);
