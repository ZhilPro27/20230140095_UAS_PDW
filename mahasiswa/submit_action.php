<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config.php';

// 1. Cek Autentikasi dan Otorisasi
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

// 2. Pastikan metode request adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: my_courses.php");
    exit();
}

// 3. Validasi Input dari form
if (!isset($_POST['id_modul'], $_POST['id_matkul'], $_FILES['file_laporan'])) {
    die("Data tidak lengkap.");
}

$id_modul = intval($_POST['id_modul']);
$id_matkul = intval($_POST['id_matkul']); // Untuk redirect
$id_mahasiswa = $_SESSION['user_id'];

// 4. Proses File Upload
$file = $_FILES['file_laporan'];

// Cek apakah ada error saat upload
if ($file['error'] !== UPLOAD_ERR_OK) {
    die("Terjadi error saat mengunggah file.");
}

$target_dir = "../uploads/laporan/";
// Buat nama file unik untuk mencegah tumpang tindih
// Format: idmahasiswa_idmodul_namafileasli.ekstensi
$file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$unique_filename = $id_mahasiswa . "_" . $id_modul . "_" . time() . "." . $file_extension;
$target_file = $target_dir . $unique_filename;

// Pindahkan file dari temporary location ke folder tujuan
if (move_uploaded_file($file['tmp_name'], $target_file)) {
    // 5. Jika file berhasil di-upload, simpan data ke database
    $stmt = $conn->prepare("INSERT INTO pengumpulan (id_modul, id_mahasiswa, file_laporan) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $id_modul, $id_mahasiswa, $unique_filename);
    
    if ($stmt->execute()) {
        // Jika berhasil, redirect kembali ke halaman detail dengan status sukses
        $stmt->close();
        $conn->close();
        header("Location: view_course.php?id=$id_matkul&status=submit_success");
        exit();
    } else {
        // Jika gagal insert DB, hapus file yang sudah ter-upload untuk konsistensi
        unlink($target_file);
        die("Gagal menyimpan data ke database.");
    }

} else {
    die("Gagal memindahkan file yang diunggah.");
}