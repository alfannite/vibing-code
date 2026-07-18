<?php
// =========================================
// PROSES SIMPAN DATA DARI FORM KE DATABASE
// =========================================

// Memanggil file koneksi database
include "config.php";

// =========================================
// AMBIL DATA DARI FORM (METHOD POST)
// =========================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nama  = mysqli_real_escape_string($conn, $_POST['nama']);
    $kelas = mysqli_real_escape_string($conn, $_POST['kelas']);
    $umur  = mysqli_real_escape_string($conn, $_POST['umur']);

    // =========================================
    // QUERY INSERT KE TABEL SISWA
    // =========================================
    $query = "INSERT INTO siswa (nama, kelas, umur) VALUES ('$nama', '$kelas', '$umur')";

    // Eksekusi query
    if (mysqli_query($conn, $query)) {
        echo "Data berhasil disimpan!";
        echo "<br><a href='index.php'>Kembali ke Form</a>";
    } else {
        echo "Gagal menyimpan data: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>
