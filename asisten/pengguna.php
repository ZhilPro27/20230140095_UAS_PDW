<?php
$pageTitle = 'Manajemen Pengguna';
$activePage = 'pengguna';
require_once 'templates/header.php';
require_once '../config.php';

// Ambil semua data pengguna
$result = $conn->query("SELECT id, nama, email, role FROM users ORDER BY nama");
?>

<div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <h3 id="form-title" class="text-xl font-bold text-gray-800 mb-4">Tambah Pengguna Baru</h3>
    <form action="pengguna_aksi.php" method="POST">
        <input type="hidden" name="id" id="id_user">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input type="text" name="nama" id="nama" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                <small id="password-help" class="text-gray-500">Kosongkan jika tidak ingin mengubah password.</small>
            </div>
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                <select name="role" id="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="asisten">Asisten</option>
                </select>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" name="simpan" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">Simpan</button>
            <button type="button" onclick="resetForm()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg ml-2">Batal</button>
        </div>
    </form>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-xl font-bold text-gray-800 mb-4">Daftar Pengguna</h3>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td class="px-6 py-4"><?php echo htmlspecialchars($row['nama']); ?></td>
                <td class="px-6 py-4"><?php echo htmlspecialchars($row['email']); ?></td>
                <td class="px-6 py-4"><?php echo ucfirst(htmlspecialchars($row['role'])); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button onclick="editUser(<?php echo htmlspecialchars(json_encode($row)); ?>)" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                    
                    <?php if ($row['id'] != $_SESSION['user_id']): // Mencegah admin menghapus diri sendiri ?>
                    <a href="pengguna_aksi.php?hapus=<?php echo $row['id']; ?>" class="text-red-600 hover:text-red-900 ml-4" onclick="return confirm('Yakin ingin menghapus pengguna ini?')">Hapus</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
function editUser(data) {
    document.getElementById('form-title').innerText = 'Edit Pengguna';
    document.getElementById('id_user').value = data.id;
    document.getElementById('nama').value = data.nama;
    document.getElementById('email').value = data.email;
    document.getElementById('role').value = data.role;
    document.getElementById('password').required = false; // Password tidak wajib diisi saat edit
    document.getElementById('password-help').style.display = 'block';
    window.scrollTo(0, 0);
}

function resetForm() {
    document.getElementById('form-title').innerText = 'Tambah Pengguna Baru';
    document.getElementById('id_user').value = '';
    document.querySelector('form').reset();
    document.getElementById('password').required = true;
    document.getElementById('password-help').style.display = 'none';
}

// Panggil resetForm saat halaman pertama kali dimuat
document.addEventListener('DOMContentLoaded', resetForm);
</script>


<?php
require_once 'templates/footer.php';
?>