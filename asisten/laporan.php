<?php
$pageTitle = 'Laporan Masuk';
$activePage = 'laporan';
require_once 'templates/header.php';
require_once '../config.php';

// Ambil data untuk filter
$matkul_list = $conn->query("SELECT id, nama_matkul FROM mata_praktikum ORDER BY nama_matkul");

// Logika Filter
$sql_where = [];
$filter_matkul = $_GET['filter_matkul'] ?? '';
$filter_status = $_GET['filter_status'] ?? '';

if (!empty($filter_matkul)) {
    $sql_where[] = "mp.id = " . intval($filter_matkul);
}
if ($filter_status === 'dinilai') {
    $sql_where[] = "p.nilai IS NOT NULL";
} elseif ($filter_status === 'belum_dinilai') {
    $sql_where[] = "p.nilai IS NULL";
}

$where_clause = count($sql_where) > 0 ? "WHERE " . implode(' AND ', $sql_where) : "";

// Query utama untuk mengambil data laporan
$query = "
    SELECT 
        p.id, 
        p.tanggal_kumpul,
        p.nilai,
        u.nama AS nama_mahasiswa, 
        m.nama_modul, 
        mp.nama_matkul
    FROM pengumpulan p
    JOIN users u ON p.id_mahasiswa = u.id
    JOIN modul m ON p.id_modul = m.id
    JOIN mata_praktikum mp ON m.id_matkul = mp.id
    $where_clause
    ORDER BY p.tanggal_kumpul DESC
";

$result = $conn->query($query);
?>

<div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <h3 class="text-xl font-bold text-gray-800 mb-4">Filter Laporan</h3>
    <form action="laporan.php" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label for="filter_matkul" class="block text-sm font-medium text-gray-700">Mata Praktikum</label>
            <select name="filter_matkul" id="filter_matkul" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                <option value="">Semua</option>
                <?php while ($matkul = $matkul_list->fetch_assoc()): ?>
                    <option value="<?php echo $matkul['id']; ?>" <?php echo ($filter_matkul == $matkul['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($matkul['nama_matkul']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div>
            <label for="filter_status" class="block text-sm font-medium text-gray-700">Status</label>
            <select name="filter_status" id="filter_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                <option value="">Semua</option>
                <option value="dinilai" <?php echo ($filter_status == 'dinilai') ? 'selected' : ''; ?>>Sudah Dinilai</option>
                <option value="belum_dinilai" <?php echo ($filter_status == 'belum_dinilai') ? 'selected' : ''; ?>>Belum Dinilai</option>
            </select>
        </div>
        <div class="self-end">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">Filter</button>
        </div>
    </form>
</div>


<div class="bg-white p-6 rounded-lg shadow-md">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mahasiswa</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mata Praktikum</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modul</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Kumpul</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td class="px-6 py-4"><?php echo htmlspecialchars($row['nama_mahasiswa']); ?></td>
                <td class="px-6 py-4"><?php echo htmlspecialchars($row['nama_matkul']); ?></td>
                <td class="px-6 py-4"><?php echo htmlspecialchars($row['nama_modul']); ?></td>
                <td class="px-6 py-4"><?php echo date('d M Y H:i', strtotime($row['tanggal_kumpul'])); ?></td>
                <td class="px-6 py-4">
                    <?php if ($row['nilai'] !== null): ?>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Sudah Dinilai
                        </span>
                    <?php else: ?>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            Belum Dinilai
                        </span>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-4">
                    <a href="nilai.php?id=<?php echo $row['id']; ?>" class="text-indigo-600 hover:text-indigo-900">
                        <?php echo ($row['nilai'] !== null) ? 'Lihat/Edit Nilai' : 'Beri Nilai'; ?>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
require_once 'templates/footer.php';
?>