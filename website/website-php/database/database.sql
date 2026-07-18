-- =========================================
-- SCRIPT SQL: BUAT DATABASE DAN TABEL SISWA
-- =========================================

CREATE DATABASE IF NOT EXISTS db_siswa;
USE db_siswa;

CREATE TABLE IF NOT EXISTS siswa (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    kelas VARCHAR(50) NOT NULL,
    umur INT(3) NOT NULL
);

-- =========================================
-- SELESAI
-- =========================================
