<?php
// =========================================
// HALAMAN FORM INPUT DATA SISWA
// =========================================
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Data Siswa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
            width: 320px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #45a049;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Form Data Siswa</h2>

        <!-- ========================================= -->
        <!-- FORM INPUT NAMA, KELAS, DAN UMUR -->
        <!-- ========================================= -->
        <form action="simpan.php" method="POST">

            <label for="nama">Nama</label>
            <input type="text" name="nama" id="nama" placeholder="Masukkan nama" required>

            <label for="kelas">Kelas</label>
            <input type="text" name="kelas" id="kelas" placeholder="Masukkan kelas" required>

            <label for="umur">Umur</label>
            <input type="number" name="umur" id="umur" placeholder="Masukkan umur" required>

            <button type="submit">Simpan</button>

        </form>
        <!-- ========================================= -->
        <!-- AKHIR FORM -->
        <!-- ========================================= -->

    </div>

</body>
</html>
