<?php
$pageTitle = 'Cari Praktikum';
$activePage = 'courses'; 
require_once 'templates/header_mahasiswa.php';
require_once '../config.php';

// ... (kode untuk mengambil $enrolled_courses_ids tidak berubah) ...
$enrolled_courses_ids = [];
if (isset($_SESSION['user_id'])) {
    $id_mahasiswa = $_SESSION['user_id'];
    $enroll_check_stmt = $conn->prepare("SELECT id_matkul FROM pendaftaran_praktikum WHERE id_mahasiswa = ?");
    $enroll_check_stmt->bind_param("i", $id_mahasiswa);
    $enroll_check_stmt->execute();
    $enroll_result = $enroll_check_stmt->get_result();
    while ($enroll_row = $enroll_result->fetch_assoc()) {
        $enrolled_courses_ids[] = $enroll_row['id_matkul'];
    }
    $enroll_check_stmt->close();
}

$result = $conn->query("SELECT * FROM mata_praktikum ORDER BY nama_matkul");
?>

<div class="container mx-auto">
    <h1 class="text-4xl font-bold text-gray-800 mb-8">Katalog Mata Praktikum</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="bg-white rounded-xl shadow-lg overflow-hidden transform hover:-translate-y-2 transition-transform duration-300 flex flex-col">
                    <?php if (!empty($row['gambar'])): ?>
                        <img class="h-40 w-full object-cover" src="../uploads/images/<?php echo htmlspecialchars($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['nama_matkul']); ?>">
                    <?php endif; ?>
                    <div class="p-6 flex-grow flex flex-col">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($row['nama_matkul']); ?></h3>
                        <p class="text-gray-500 font-mono text-sm mb-4"><?php echo htmlspecialchars($row['kode_matkul']); ?></p>
                        <p class="text-gray-700 mb-6 flex-grow">
                            <?php echo htmlspecialchars($row['deskripsi']); ?>
                        </p>
                        
                        <div class="mt-auto">
                            <?php if (isset($_SESSION['user_id'])):
                                if (in_array($row['id'], $enrolled_courses_ids)): ?>
                                    <span class="w-full text-center bg-gray-400 text-white font-bold py-3 px-4 rounded-lg inline-block cursor-not-allowed">Sudah Terdaftar</span>
                                <?php else: ?>
                                    <a href="enroll_action.php?id_matkul=<?php echo $row['id']; ?>" class="w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-colors duration-300 inline-block">Daftar Praktikum</a>
                                <?php endif;
                            else: ?>
                                <a href="../login.php" class="w-full text-center bg-gray-400 text-white font-bold py-3 px-4 rounded-lg inline-block">Login untuk Mendaftar</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-gray-600 col-span-full">Saat ini belum ada mata praktikum yang tersedia.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'templates/footer_mahasiswa.php'; ?>