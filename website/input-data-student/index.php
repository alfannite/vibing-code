<?php
$host    = "localhost";    
$user    = "root";                 
$pass    = "";                     
$dbname  = "db_siswa";      

$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}


$pesan_error = "";
$sukses_input = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $nama  = mysqli_real_escape_string($conn, $_POST['nama']);
    $kelas = mysqli_real_escape_string($conn, $_POST['kelas']);
    $umur  = mysqli_real_escape_string($conn, $_POST['umur']);

    $query = "INSERT INTO siswa (nama, kelas, umur) VALUES ('$nama', '$kelas', '$umur')";

    if (mysqli_query($conn, $query)) {
        $sukses_input = true; // Ubah jadi true agar pop-up tampil
    } else {
        $pesan_error = "Gagal menyimpan data: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa - Luxury Navy</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="./assets/img/favicon.ico">
    <style>
        :root {
            --bg-navy: #0F172A;         /* Deep Grey Navy */
            --card-bg: rgba(30, 41, 59, 0.75); /* Glassmorphism Card */
            --text-main: #F8FAFC;        /* Putih bersih */
            --text-muted: #94A3B8;       /* Abu-abu terang */
            --accent-gold: #E2B714;      /* Gold Mewah */
            --accent-glow: #38BDF8;      /* Neon Cyan Subtle */
            --neon-green: #10B981;       /* Hijau Neon */
            --btn-bg: #3B82F6;           /* Royal Blue */
            --btn-hover: #2563EB;
            --border-color: rgba(255, 255, 255, 0.1);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-navy);
            color: var(--text-main);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            justify-content: space-between;
            padding: 40px 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* --- BACKGROUND SHAPE & GLOW EFFECTS --- */
        body::before {
            content: '';
            position: absolute;
            top: -100px;
            left: -100px;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.15) 0%, rgba(15, 23, 42, 0) 70%);
            border-radius: 50%;
            z-index: -1;
        }

        body::after {
            content: '';
            position: absolute;
            bottom: -100px;
            right: -100px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(226, 183, 20, 0.1) 0%, rgba(15, 23, 42, 0) 70%);
            border-radius: 50%;
            z-index: -1;
        }

        .container {
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            background-color: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            padding: 40px;
            border-radius: 20px;
            border: 1px solid var(--border-color);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        h2, h3 { 
            color: var(--text-main); 
            font-weight: 700; 
            margin-bottom: 20px; 
            font-size: 1.5rem;
            text-align: center;
            letter-spacing: -0.5px;
        }

        form { display: flex; flex-direction: column; gap: 14px; margin-bottom: 25px; }
        
        label { font-size: 0.9rem; font-weight: 600; color: var(--text-muted); }

        input {
            padding: 14px 16px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.95rem;
            background-color: rgba(15, 23, 42, 0.6);
            color: var(--text-main);
            transition: all 0.3s ease;
        }

        input:focus { 
            outline: none; 
            border-color: var(--btn-bg); 
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.25);
            background-color: rgba(15, 23, 42, 0.9);
        }

        .btn {
            padding: 14px;
            background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            display: inline-block;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn:hover { 
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(59, 130, 246, 0.4);
        }

        .btn-secondary { 
            background: rgba(255, 255, 255, 0.05); 
            border: 1px solid var(--border-color);
            color: var(--text-main);
            margin-bottom: 25px; 
        }
        
        .btn-secondary:hover { 
            background: rgba(255, 255, 255, 0.1); 
            box-shadow: none;
        }

        .alert-error { 
            background-color: rgba(239, 68, 68, 0.2); 
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #FCA5A5; 
            padding: 12px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
            font-size: 0.9rem; 
        }

        /* --- STYLING POP-UP MODAL KEREN (BOUNCE + BLUR) --- */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-card {
            background: rgba(30, 41, 59, 0.9);
            border: 1px solid rgba(16, 185, 129, 0.3);
            padding: 35px 30px;
            border-radius: 20px;
            text-align: center;
            width: 90%;
            max-width: 360px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5), 0 0 25px rgba(16, 185, 129, 0.2);
            transform: scale(0.5);
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); /* Efek Bounce Keren */
        }

        .modal-overlay.active .modal-card {
            transform: scale(1);
        }

        /* Animasi Loading Hijau Neon */
        .neon-loader {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(16, 185, 129, 0.2);
            border-top: 4px solid var(--neon-green);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto 20px auto;
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.4);
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Icon Checkmark Keren */
        .checkmark-icon {
            display: none;
            width: 55px;
            height: 55px;
            background: rgba(16, 185, 129, 0.15);
            border: 2px solid var(--neon-green);
            border-radius: 50%;
            color: var(--neon-green);
            font-size: 28px;
            line-height: 52px;
            text-align: center;
            margin: 0 auto 20px auto;
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.4);
            animation: bounceIn 0.5s ease;
        }

        @keyframes bounceIn {
            0% { transform: scale(0); }
            70% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .modal-text {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-main);
            letter-spacing: -0.3px;
        }

        footer { 
            text-align: center; 
            margin-top: 40px; 
            font-size: 0.85rem; 
            color: var(--text-muted); 
            font-weight: 500; 
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Input Data Siswa | By ALFAN</h2>

        <?php if (!empty($pesan_error)): ?>
            <div class="alert-error"><?php echo $pesan_error; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="nama">Nama</label>
            <input type="text" name="nama" id="nama" placeholder="Masukkan nama" required>

            <label for="kelas">Kelas</label>
            <input type="text" name="kelas" id="kelas" placeholder="Masukkan kelas" required>

            <label for="umur">Umur</label>
            <input type="number" name="umur" id="umur" placeholder="Masukkan umur" required>

            <button type="submit" name="submit" class="btn">Simpan Data</button>
        </form>

        <a href="cari.php" class="btn btn-secondary" style="width: 100%;">Cari Data Siswa</a>

        <hr style="border: none; border-top: 1px solid var(--border-color); margin: 30px 0;">
    </div>

    <!-- POP-UP MODAL BERHASIL -->
    <div class="modal-overlay" id="successModal">
        <div class="modal-card">
            <!-- Loading Awal -->
            <div class="neon-loader" id="loaderIcon"></div>
            <!-- Checkmark Sukses (Muncul setelah loading) -->
            <div class="checkmark-icon" id="checkIcon">✓</div>
            <div class="modal-text" id="modalText">Menyimpan data...</div>
        </div>
    </div>

    <footer>Made by Alfan NiteOps</footer>

    <!-- Script JavaScript untuk Animasi Pop-Up -->
    <?php if ($sukses_input): ?>
    <script>
        const modal = document.getElementById('successModal');
        const loader = document.getElementById('loaderIcon');
        const check = document.getElementById('checkIcon');
        const text = document.getElementById('modalText');

        // Tampilkan Modal dengan efek bounce & blur
        modal.classList.add('active');

        // Simulasi jeda animasi loading (0.8 detik), lalu ubah ke checklist sukses
        setTimeout(() => {
            loader.style.display = 'none';
            check.style.display = 'block';
            text.innerHTML = "Data Kamu Masuk!";
        }, 900);

        // Setelah total 2 detik, refresh halaman atau bersihkan form secara mulus
        setTimeout(() => {
            window.location.href = window.location.pathname;
        }, 2200);
    </script>
    <?php endif; ?>

</body>
</html>
<?php mysqli_close($conn); ?>