<?php
$host    = "localhost";    
$user    = "root";                 
$pass    = "";                     
$dbname  = "db_siswa";      

$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

$keyword = "";
$is_searching = false;

if (isset($_GET['keyword']) && trim($_GET['keyword']) !== "") {
    $keyword = mysqli_real_escape_string($conn, $_GET['keyword']);
    $query = "SELECT * FROM siswa WHERE nama LIKE '%$keyword%' OR kelas LIKE '%$keyword%'";
    $is_searching = true; 
} else {
    $query = "SELECT * FROM siswa";
}
$result = mysqli_query($conn, $query);
$jumlah_data = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Data Siswa - Luxury Navy</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-navy: #0F172A;
            --card-bg: rgba(30, 41, 59, 0.75);
            --text-main: #F8FAFC;
            --text-muted: #94A3B8;
            --accent-gold: #E2B714;
            --neon-cyan: #38BDF8;      
            --btn-bg: #3B82F6;
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

        body::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.15) 0%, rgba(15, 23, 42, 0) 70%);
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

        h2 { 
            color: var(--text-main); 
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 1.5rem;
            text-align: center;
        }

        .search-form { display: flex; gap: 12px; margin-bottom: 25px; }

        input {
            flex: 1;
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
            padding: 14px 22px;
            background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn:hover { 
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(59, 130, 246, 0.4);
        }

        .btn-back { 
            background: rgba(255, 255, 255, 0.05); 
            border: 1px solid var(--border-color);
            color: var(--text-main);
            margin-bottom: 25px; 
            display: block; 
            box-shadow: none;
        }
        
        .btn-back:hover { 
            background: rgba(255, 255, 255, 0.1); 
            transform: none;
        }

        /* --- STYLING POP-UP MODAL YANG LEBIH RAPI & RESPONSIF --- */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            padding: 20px;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-card {
            background: rgba(30, 41, 59, 0.95);
            border: 1px solid rgba(56, 189, 248, 0.3);
            padding: 25px;
            border-radius: 20px;
            width: 100%;
            max-width: 550px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.6), 0 0 30px rgba(56, 189, 248, 0.25);
            transform: scale(0.5);
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .modal-overlay.active .modal-card {
            transform: scale(1);
        }

        /* Neon Cyan Loader */
        .neon-loader-cyan {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(56, 189, 248, 0.2);
            border-top: 4px solid var(--neon-cyan);
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            margin: 20px auto;
            box-shadow: 0 0 15px rgba(56, 189, 248, 0.4);
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .modal-text {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 15px;
            text-align: center;
        }

        .modal-content-data {
            display: none;
            animation: fadeIn 0.4s ease forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Container Tabel Modal yang Responsif & Rapi */
        .modal-table-wrapper {
            max-height: 240px;
            overflow-y: auto;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            background-color: rgba(15, 23, 42, 0.4);
        }

        /* Styling Tabel Rapi */
        .modal-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
            text-align: left;
        }

        .modal-table th, .modal-table td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-color);
            word-break: break-word;
        }

        .modal-table th {
            background-color: rgba(15, 23, 42, 0.95);
            color: var(--text-muted);
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 2;
        }

        .modal-table tr:last-child td {
            border-bottom: none;
        }

        .modal-table tr:hover {
            background-color: rgba(255, 255, 255, 0.03);
        }

        .table-responsive { overflow-x: auto; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 0.9rem; }
        
        th, td { padding: 14px 16px; border-bottom: 1px solid var(--border-color); text-align: left; }
        
        th { background-color: rgba(15, 23, 42, 0.5); font-weight: 600; color: var(--text-muted); }
        
        tr:hover { background-color: rgba(255, 255, 255, 0.02); }

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
        <h2>Pencarian Data Siswa</h2>

        <a href="index.php" class="btn btn-back">← Kembali</a>

        <form action="cari.php" method="GET" class="search-form">
            <input type="text" name="keyword" placeholder="Cari berdasarkan nama atau kelas..." value="<?php echo htmlspecialchars($keyword); ?>" required>
            <button type="submit" class="btn">Cari</button>
        </form>

        <?php if (!empty($keyword)): ?>
            <p style="margin-bottom: 15px; font-size: 0.9rem; color: var(--text-muted);">Hasil pencarian untuk: <b style="color: var(--text-main);">"<?php echo htmlspecialchars($keyword); ?>"</b></p>
        <?php endif; ?>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Umur</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    if ($jumlah_data > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['kelas']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['umur']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align: center; color: var(--text-muted);'>Data siswa tidak ditemukan.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- POP-UP MODAL HASIL PENCARIAN -->
    <div class="modal-overlay" id="searchModal">
        <div class="modal-card">
            <!-- Tahap 1: Loading Cepat -->
            <div id="loadingSection">
                <div class="neon-loader-cyan"></div>
                <div class="modal-text">Mencari ke database...</div>
            </div>

            <!-- Tahap 2: Menampilkan Tabel Data yang Rapi di dalam Pop-up -->
            <div class="modal-content-data" id="dataSection">
                <div class="modal-text" style="color: var(--neon-cyan); margin-bottom: 12px; font-size: 1rem;">
                     Data Ditemukan (<?php echo $jumlah_data; ?> data)
                </div>
                <div class="modal-table-wrapper">
                    <table class="modal-table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Umur</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            mysqli_data_seek($result, 0);
                            if ($jumlah_data > 0) {
                                while ($row_modal = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row_modal['nama']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row_modal['kelas']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row_modal['umur']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3' style='text-align: center; color: var(--text-muted);'>Data tidak ditemukan.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <footer>Made by Alfan NiteOps</footer>

    <!-- Script Alur Waktu Pop-up -->
    <?php if ($is_searching): ?>
    <script>
        const modal = document.getElementById('searchModal');
        const loadingSection = document.getElementById('loadingSection');
        const dataSection = document.getElementById('dataSection');

        modal.classList.add('active');

        // Setelah 0.7 detik, ganti dari loading ke tabel data
        setTimeout(() => {
            loadingSection.style.display = 'none';
            dataSection.style.display = 'block';
        }, 700);

        // Setelah 3.7 detik total, pop-up tertutup sendiri secara mulus
        setTimeout(() => {
            modal.classList.remove('active');
        }, 3700);
    </script>
    <?php endif; ?>

</body>
</html>
<?php mysqli_close($conn); ?>