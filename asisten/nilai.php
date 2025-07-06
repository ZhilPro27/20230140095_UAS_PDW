<?php
$pageTitle = 'Beri Nilai Laporan';
$activePage = 'laporan';
require_once 'templates/header.php';
require_once '../config.php';

if (!isset($_GET['id'])) {
    header("Location: laporan.php");
    exit();
}

$id_pengumpulan = $_GET['id'];

// Query untuk mengambil detail pengumpulan
$query = "
    SELECT 
        p.*,
        u.nama AS nama_mahasiswa, 
        m.nama_modul, 
        mp.nama_matkul
    FROM pengumpulan p
    JOIN users u ON p.id_mahasiswa = u.id
    JOIN modul m ON p.id_modul = m.id
    JOIN mata_praktikum mp ON m.id_matkul = mp.id
    WHERE p.id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_pengumpulan);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "Laporan tidak ditemukan.";
    exit();
}
$laporan = $result->fetch_assoc();
?>

<div class="bg-white p-8 rounded-lg shadow-md max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Detail & Penilaian Laporan</h2>

    <div class="mb-6 border-b pb-6">
        <div class="grid grid-cols-3 gap-4">
            <div class="col-span-1 text-sm font-medium text-gray-500">Mahasiswa</div>
            <div class="col-span-2 text-sm text-gray-900"><?php echo htmlspecialchars($laporan['nama_mahasiswa']); ?></div>

            <div class="col-span-1 text-sm font-medium text-gray-500">Mata Praktikum</div>
            <div class="col-span-2 text-sm text-gray-900"><?php echo htmlspecialchars($laporan['nama_matkul']); ?></div>

            <div class="col-span-1 text-sm font-medium text-gray-500">Modul</div>
            <div class="col-span-2 text-sm text-gray-900"><?php echo htmlspecialchars($laporan['nama_modul']); ?></div>

            <div class="col-span-1 text-sm font-medium text-gray-500">Tanggal Kumpul</div>
            <div class="col-span-2 text-sm text-gray-900"><?php echo date('d M Y H:i', strtotime($laporan['tanggal_kumpul'])); ?></div>
            
            <div class="col-span-1 text-sm font-medium text-gray-500">File Laporan</div>
            <div class="col-span-2 text-sm text-gray-900">
                <a href="../uploads/laporan/<?php echo htmlspecialchars($laporan['file_laporan']); ?>" class="text-blue-500 hover:underline" download>
                    Download Laporan
                </a>
            </div>
        </div>
    </div>

    <form action="nilai_aksi.php" method="POST">
        <input type="hidden" name="id_pengumpulan" value="<?php echo $laporan['id']; ?>">
        
        <div class="mb-4">
            <label for="nilai" class="block text-sm font-medium text-gray-700">Nilai (0-100)</label>
            <input type="number" name="nilai" id="nilai" min="0" max="100" value="<?php echo htmlspecialchars($laporan['nilai']); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
        </div>

        <div class="mb-6">
            <label for="feedback" class="block text-sm font-medium text-gray-700">Feedback</label>
            <textarea name="feedback" id="feedback" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"><?php echo htmlspecialchars($laporan['feedback']); ?></textarea>
        </div>

        <div class="flex justify-end">
            <a href="laporan.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg mr-2">Kembali</a>
            <button type="submit" name="simpan_nilai" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">Simpan Nilai</button>
        </div>
    </form>
</div>


<?php
require_once 'templates/footer.php';
?>