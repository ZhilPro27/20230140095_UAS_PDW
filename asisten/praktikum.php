<?php
$pageTitle = 'Manajemen Praktikum';
$activePage = 'praktikum'; 
require_once 'templates/header.php';
require_once '../config.php'; 

$result = $conn->query("SELECT * FROM mata_praktikum ORDER BY kode_matkul");
?>

<div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <h3 class="text-xl font-bold text-gray-800 mb-4">Tambah/Edit Mata Praktikum</h3>
    <form action="praktikum_aksi.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" id="id_matkul">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="kode_matkul" class="block text-sm font-medium text-gray-700">Kode Praktikum</label>
                <input type="text" name="kode_matkul" id="kode_matkul" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
            <div>
                <label for="nama_matkul" class="block text-sm font-medium text-gray-700">Nama Praktikum</label>
                <input type="text" name="nama_matkul" id="nama_matkul" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
             <div>
                <label for="gambar" class="block text-sm font-medium text-gray-700">Gambar (Opsional)</label>
                <input type="file" name="gambar" id="gambar" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
        </div>
        <div class="mt-4">
            <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
        </div>
        <div class="mt-4">
            <button type="submit" name="simpan" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">Simpan</button>
        </div>
    </form>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-xl font-bold text-gray-800 mb-4">Daftar Mata Praktikum</h3>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gambar</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Praktikum</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <?php if (!empty($row['gambar'])): ?>
                        <img src="../uploads/images/<?php echo htmlspecialchars($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['nama_matkul']); ?>" class="w-16 h-10 object-cover rounded">
                    <?php else: ?>
                        <div class="w-16 h-10 bg-gray-200 rounded flex items-center justify-center text-xs text-gray-500">No Image</div>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($row['kode_matkul']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($row['nama_matkul']); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button onclick="editMatkul(<?php echo htmlspecialchars(json_encode($row)); ?>)" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                    <a href="praktikum_aksi.php?hapus=<?php echo $row['id']; ?>" class="text-red-600 hover:text-red-900 ml-4" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
                    <a href="modul.php?id_matkul=<?php echo $row['id']; ?>" class="text-green-600 hover:text-green-900 ml-4">Kelola Modul</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
function editMatkul(data) {
    document.getElementById('id_matkul').value = data.id;
    document.getElementById('kode_matkul').value = data.kode_matkul;
    document.getElementById('nama_matkul').value = data.nama_matkul;
    document.getElementById('deskripsi').value = data.deskripsi;
    window.scrollTo(0, 0); 
}
</script>

<?php
require_once 'templates/footer.php';
?>