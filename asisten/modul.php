<?php
$pageTitle = 'Manajemen Modul';
$activePage = 'praktikum'; 
require_once 'templates/header.php';
require_once '../config.php';

if (!isset($_GET['id_matkul'])) {
    header("Location: praktikum.php");
    exit();
}

$id_matkul = $_GET['id_matkul'];
$matkul_stmt = $conn->prepare("SELECT nama_matkul FROM mata_praktikum WHERE id = ?");
$matkul_stmt->bind_param("i", $id_matkul);
$matkul_stmt->execute();
$matkul_result = $matkul_stmt->get_result();
if ($matkul_result->num_rows === 0) {
    echo "Mata praktikum tidak ditemukan.";
    exit();
}
$nama_matkul = $matkul_result->fetch_assoc()['nama_matkul'];

$modul_result = $conn->query("SELECT * FROM modul WHERE id_matkul = $id_matkul ORDER BY created_at");
?>

<h2 class="text-2xl font-bold text-gray-800 mb-4">Manajemen Modul untuk: <?php echo htmlspecialchars($nama_matkul); ?></h2>

<div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <h3 class="text-xl font-bold text-gray-800 mb-4">Tambah/Edit Modul</h3>
    <form action="modul_aksi.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" id="id_modul">
        <input type="hidden" name="id_matkul" value="<?php echo $id_matkul; ?>">
        
        <div class="mb-4">
            <label for="nama_modul" class="block text-sm font-medium text-gray-700">Nama Modul</label>
            <input type="text" name="nama_modul" id="nama_modul" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
        </div>
        <div class="mb-4">
            <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="file_materi" class="block text-sm font-medium text-gray-700">File Materi (PDF, DOCX, dll)</label>
                <input type="file" name="file_materi" id="file_materi" class="mt-1 block w-full text-sm">
                <small id="file_sekarang_materi" class="text-gray-500"></small>
            </div>
            <div>
                <label for="gambar" class="block text-sm font-medium text-gray-700">Gambar Modul (Opsional)</label>
                <input type="file" name="gambar" id="gambar" class="mt-1 block w-full text-sm">
                <small id="file_sekarang_gambar" class="text-gray-500"></small>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" name="simpan" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">Simpan Modul</button>
        </div>
    </form>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-xl font-bold text-gray-800 mb-4">Daftar Modul</h3>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gambar</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Modul</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Materi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php while ($row = $modul_result->fetch_assoc()): ?>
            <tr>
                <td class="px-6 py-4">
                    <?php if (!empty($row['gambar'])): ?>
                        <img src="../uploads/images/<?php echo htmlspecialchars($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['nama_modul']); ?>" class="w-16 h-10 object-cover rounded">
                    <?php else: ?>
                        <div class="w-16 h-10 bg-gray-200 rounded"></div>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-4"><?php echo htmlspecialchars($row['nama_modul']); ?></td>
                <td class="px-6 py-4">
                    <?php if (!empty($row['file_materi'])): ?>
                        <a href="../uploads/materi/<?php echo htmlspecialchars($row['file_materi']); ?>" class="text-blue-500 hover:underline" download>Download</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button onclick="editModul(<?php echo htmlspecialchars(json_encode($row)); ?>)" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                    <a href="modul_aksi.php?hapus=<?php echo $row['id']; ?>&id_matkul=<?php echo $id_matkul; ?>" class="text-red-600 hover:text-red-900 ml-4" onclick="return confirm('Yakin hapus modul ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
function editModul(data) {
    document.getElementById('id_modul').value = data.id;
    document.getElementById('nama_modul').value = data.nama_modul;
    document.getElementById('deskripsi').value = data.deskripsi;
    
    document.getElementById('file_sekarang_materi').innerText = data.file_materi ? 'File materi saat ini: ' + data.file_materi : '';
    document.getElementById('file_sekarang_gambar').innerText = data.gambar ? 'File gambar saat ini: ' + data.gambar : '';

    window.scrollTo(0, 0);
}
</script>

<?php require_once 'templates/footer.php'; ?>