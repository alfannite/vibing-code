<?php
$host    = "localhost";     // alamat host database
$user    = "root";          // username database
$pass    = "";              // password database
$dbname  = "db_siswa";      // nama database

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

?>
