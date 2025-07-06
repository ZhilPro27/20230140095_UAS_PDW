<?php

$pageTitle = 'Dashboard';
$activePage = 'dashboard';
require_once 'templates/header_mahasiswa.php'; 
require_once '../config.php';

// Pastikan pengguna login sebagai mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

$id_mahasiswa = $_SESSION['user_id'];

// --- MULAI LOGIKA DINAMIS ---

// 1. Menghitung jumlah praktikum yang diikuti
$praktikum_diikuti_res = $conn->prepare("SELECT COUNT(id) AS total FROM pendaftaran_praktikum WHERE id_mahasiswa = ?");
$praktikum_diikuti_res->bind_param("i", $id_mahasiswa);
$praktikum_diikuti_res->execute();
$praktikum_diikuti = $praktikum_diikuti_res->get_result()->fetch_assoc()['total'];
$praktikum_diikuti_res->close();

// 2. Menghitung tugas yang sudah dinilai (selesai)
$tugas_selesai_res = $conn->prepare("SELECT COUNT(id) AS total FROM pengumpulan WHERE id_mahasiswa = ? AND nilai IS NOT NULL");
$tugas_selesai_res->bind_param("i", $id_mahasiswa);
$tugas_selesai_res->execute();
$tugas_selesai = $tugas_selesai_res->get_result()->fetch_assoc()['total'];
$tugas_selesai_res->close();

// 3. Menghitung tugas yang menunggu penilaian
$tugas_menunggu_res = $conn->prepare("SELECT COUNT(id) AS total FROM pengumpulan WHERE id_mahasiswa = ? AND nilai IS NULL");
$tugas_menunggu_res->bind_param("i", $id_mahasiswa);
$tugas_menunggu_res->execute();
$tugas_menunggu = $tugas_menunggu_res->get_result()->fetch_assoc()['total'];
$tugas_menunggu_res->close();

// 4. Mengambil daftar tugas yang belum dikerjakan
$tugas_belum_dikerjakan_query = "
    SELECT 
        m.nama_modul, 
        mp.nama_matkul,
        mp.id as id_matkul
    FROM modul m
    JOIN mata_praktikum mp ON m.id_matkul = mp.id
    JOIN pendaftaran_praktikum pp ON mp.id = pp.id_matkul
    WHERE pp.id_mahasiswa = ? 
      AND m.id NOT IN (
          SELECT id_modul FROM pengumpulan WHERE id_mahasiswa = ?
      )
    ORDER BY mp.nama_matkul, m.created_at
    LIMIT 5
";
$tugas_belum_dikerjakan_res = $conn->prepare($tugas_belum_dikerjakan_query);
$tugas_belum_dikerjakan_res->bind_param("ii", $id_mahasiswa, $id_mahasiswa);
$tugas_belum_dikerjakan_res->execute();
$tugas_belum_dikerjakan = $tugas_belum_dikerjakan_res->get_result();
$tugas_belum_dikerjakan_res->close();


// --- AKHIR LOGIKA DINAMIS ---
?>


<div class="bg-gradient-to-r from-blue-500 to-cyan-400 text-white p-8 rounded-xl shadow-lg mb-8">
    <h1 class="text-3xl font-bold">Selamat Datang Kembali, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</h1>
    <p class="mt-2 opacity-90">Terus semangat dalam menyelesaikan semua modul praktikummu.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-blue-600"><?php echo $praktikum_diikuti; ?></div>
        <div class="mt-2 text-lg text-gray-600">Praktikum Diikuti</div>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-green-500"><?php echo $tugas_selesai; ?></div>
        <div class="mt-2 text-lg text-gray-600">Tugas Selesai Dinilai</div>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-yellow-500"><?php echo $tugas_menunggu; ?></div>
        <div class="mt-2 text-lg text-gray-600">Menunggu Penilaian</div>
    </div>
    
</div>

<div class="bg-white p-6 rounded-xl shadow-md">
    <h3 class="text-2xl font-bold text-gray-800 mb-4">Tugas yang Perlu Dikerjakan</h3>
    <ul class="space-y-4">
        
        <?php if ($tugas_belum_dikerjakan->num_rows > 0): ?>
            <?php while($row = $tugas_belum_dikerjakan->fetch_assoc()): ?>
                <li class="flex items-start p-3 border-b border-gray-100 last:border-b-0">
                    <span class="text-xl mr-4">📝</span>
                    <div>
                        Segera kumpulkan laporan untuk <strong><?php echo htmlspecialchars($row['nama_modul']); ?></strong>
                        pada mata praktikum <a href="view_course.php?id=<?php echo $row['id_matkul']; ?>" class="font-semibold text-blue-600 hover:underline"><?php echo htmlspecialchars($row['nama_matkul']); ?></a>.
                    </div>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
             <li class="flex items-start p-3">
                <span class="text-xl mr-4">🎉</span>
                <div>
                    <p class="font-semibold">Luar biasa!</p>
                    <p class="text-gray-600">Anda telah menyelesaikan semua tugas yang ada. Tetap pantau untuk modul berikutnya.</p>
                </div>
            </li>
        <?php endif; ?>
        
    </ul>
</div>


<?php
// Panggil Footer
require_once 'templates/footer_mahasiswa.php';
?>