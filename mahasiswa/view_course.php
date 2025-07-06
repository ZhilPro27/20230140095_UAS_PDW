<?php
$pageTitle = 'Detail Praktikum';
$activePage = 'my_courses';
require_once 'templates/header_mahasiswa.php';
require_once '../config.php';

// ... (semua kode otentikasi, otorisasi, dan pengambilan data di atas tidak berubah) ...
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') { header("Location: ../login.php"); exit(); }
if (!isset($_GET['id'])) { header("Location: my_courses.php"); exit(); }
$id_matkul = intval($_GET['id']);
$id_mahasiswa = $_SESSION['user_id'];
$auth_stmt = $conn->prepare("SELECT id FROM pendaftaran_praktikum WHERE id_mahasiswa = ? AND id_matkul = ?");
$auth_stmt->bind_param("ii", $id_mahasiswa, $id_matkul);
$auth_stmt->execute();
if ($auth_stmt->get_result()->num_rows === 0) { echo "<div class='container mx-auto'><p class='text-red-500'>Akses ditolak.</p></div>"; require_once 'templates/footer_mahasiswa.php'; exit(); }
$auth_stmt->close();
$matkul_stmt = $conn->prepare("SELECT nama_matkul FROM mata_praktikum WHERE id = ?");
$matkul_stmt->bind_param("i", $id_matkul);
$matkul_stmt->execute();
$nama_matkul = $matkul_stmt->get_result()->fetch_assoc()['nama_matkul'];
$matkul_stmt->close();
$modul_stmt = $conn->prepare("SELECT * FROM modul WHERE id_matkul = ? ORDER BY created_at");
$modul_stmt->bind_param("i", $id_matkul);
$modul_stmt->execute();
$modul_result = $modul_stmt->get_result();

?>

<div class="container mx-auto">
    
    <?php if (isset($_GET['status']) && $_GET['status'] == 'submit_success') { echo '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert"><p class="font-bold">Sukses!</p><p>Laporan Anda berhasil dikumpulkan.</p></div>'; } ?>

    <h1 class="text-4xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($nama_matkul); ?></h1>
    <p class="text-lg text-gray-600 mb-8">Daftar Modul dan Pengumpulan Tugas</p>

    <div class="space-y-6">
        <?php while ($modul = $modul_result->fetch_assoc()): ?>
            <div class="bg-white p-6 rounded-lg shadow-md overflow-hidden">
                <div class="md:flex md:items-start md:space-x-6">
                    <?php if (!empty($modul['gambar'])): ?>
                        <div class="md:w-1/4 mb-4 md:mb-0">
                            <img src="../uploads/images/<?php echo htmlspecialchars($modul['gambar']); ?>" alt="<?php echo htmlspecialchars($modul['nama_modul']); ?>" class="w-full h-auto object-cover rounded-lg shadow">
                        </div>
                    <?php endif; ?>

                    <div class="flex-1 md:flex md:space-x-6">
                        <div class="md:w-1/2 mb-4 md:mb-0">
                            <h3 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($modul['nama_modul']); ?></h3>
                            <p class="text-gray-600 mt-1"><?php echo htmlspecialchars($modul['deskripsi']); ?></p>
                            <?php if (!empty($modul['file_materi'])): ?>
                                <a href="../uploads/materi/<?php echo htmlspecialchars($modul['file_materi']); ?>" download
                                   class="inline-block mt-4 bg-blue-100 text-blue-700 font-semibold py-2 px-4 rounded-lg hover:bg-blue-200 transition-colors">
                                    Unduh Materi
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="md:w-1/2">
                            <?php
                            $pengumpulan_stmt = $conn->prepare("SELECT * FROM pengumpulan WHERE id_modul = ? AND id_mahasiswa = ?");
                            $pengumpulan_stmt->bind_param("ii", $modul['id'], $id_mahasiswa);
                            $pengumpulan_stmt->execute();
                            $pengumpulan_result = $pengumpulan_stmt->get_result();
                            if ($pengumpulan_result->num_rows > 0):
                                $pengumpulan = $pengumpulan_result->fetch_assoc();
                                ?>
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 h-full">
                                    <h4 class="font-bold text-gray-700">Status Pengumpulan</h4>
                                    <p class="text-sm text-gray-500">Terkumpul pada: <?php echo date('d M Y, H:i', strtotime($pengumpulan['tanggal_kumpul'])); ?></p>
                                    
                                    <?php if ($pengumpulan['nilai'] !== null): ?>
                                        <div class="mt-3 bg-green-100 p-3 rounded-md">
                                            <p class="font-bold text-green-800">Sudah Dinilai</p>
                                            <p class="text-2xl font-extrabold text-green-700"><?php echo htmlspecialchars($pengumpulan['nilai']); ?></p>
                                            <?php if(!empty($pengumpulan['feedback'])): ?><p class="text-sm text-green-600 mt-1"><strong>Feedback:</strong> <?php echo htmlspecialchars($pengumpulan['feedback']); ?></p><?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="mt-3 bg-yellow-100 p-3 rounded-md">
                                            <p class="font-bold text-yellow-800">Menunggu Penilaian</p>
                                            <p class="text-sm text-yellow-700">Laporan Anda sedang menunggu untuk dinilai oleh asisten.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <form action="submit_action.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="id_modul" value="<?php echo $modul['id']; ?>">
                                    <input type="hidden" name="id_matkul" value="<?php echo $id_matkul; ?>">
                                    <div>
                                        <label for="file_laporan_<?php echo $modul['id']; ?>" class="block text-sm font-medium text-gray-700">Unggah Laporan Anda</label>
                                        <input type="file" name="file_laporan" id="file_laporan_<?php echo $modul['id']; ?>" class="mt-1 block w-full text-sm" required>
                                    </div>
                                    <button type="submit" class="w-full mt-2 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">Kumpulkan</button>
                                </form>
                            <?php endif; $pengumpulan_stmt->close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php
$modul_stmt->close();
$conn->close();
require_once 'templates/footer_mahasiswa.php';
?>