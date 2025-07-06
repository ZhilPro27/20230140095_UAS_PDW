<?php
$pageTitle = 'Praktikum Saya';
$activePage = 'my_courses'; // Variabel untuk menandai menu aktif di header
require_once 'templates/header_mahasiswa.php';
require_once '../config.php';

// Pastikan pengguna sudah login dan perannya adalah mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

$id_mahasiswa = $_SESSION['user_id'];

// Query untuk mengambil mata praktikum yang diikuti oleh mahasiswa yang sedang login
// Kita tambahkan kolom 'gambar' dalam SELECT
$query = "
    SELECT 
        mp.id, 
        mp.kode_matkul, 
        mp.nama_matkul, 
        mp.deskripsi,
        mp.gambar 
    FROM 
        pendaftaran_praktikum pp
    JOIN 
        mata_praktikum mp ON pp.id_matkul = mp.id
    WHERE 
        pp.id_mahasiswa = ?
    ORDER BY 
        mp.nama_matkul
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_mahasiswa);
$stmt->execute();
$result = $stmt->get_result();

?>

<div class="container mx-auto">
    
    <?php
    // Menampilkan notifikasi jika pendaftaran berhasil
    if (isset($_GET['status']) && $_GET['status'] == 'enroll_success') {
        echo '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-md" role="alert">
                <p class="font-bold">Berhasil!</p>
                <p>Anda telah berhasil mendaftar ke mata praktikum.</p>
              </div>';
    }
    ?>

    <h1 class="text-4xl font-bold text-gray-800 mb-8">Praktikum yang Saya Ikuti</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col">
                    <?php if (!empty($row['gambar'])): ?>
                        <img class="h-40 w-full object-cover" src="../uploads/images/<?php echo htmlspecialchars($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['nama_matkul']); ?>">
                    <?php endif; ?>

                    <div class="p-6 flex flex-col flex-grow">
                        <div class="flex-grow">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($row['nama_matkul']); ?></h3>
                            <p class="text-gray-500 font-mono text-sm mb-4"><?php echo htmlspecialchars($row['kode_matkul']); ?></p>
                            <p class="text-gray-700 mb-6">
                                <?php echo htmlspecialchars($row['deskripsi']); ?>
                            </p>
                        </div>
                        
                        <a href="view_course.php?id=<?php echo $row['id']; ?>" class="w-full text-center bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition-colors duration-300 inline-block mt-auto">
                            Lihat Detail & Tugas
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-span-full text-center py-12 px-6 bg-white rounded-lg shadow-md">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Anda Belum Mengikuti Praktikum Apapun</h3>
                <p class="mt-1 text-sm text-gray-500">Silakan cari praktikum yang tersedia untuk memulai.</p>
                <div class="mt-6">
                    <a href="courses.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                        Cari Praktikum
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$stmt->close();
$conn->close();
require_once 'templates/footer_mahasiswa.php';
?>