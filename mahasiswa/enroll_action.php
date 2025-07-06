<?php
// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config.php'; // Hubungkan ke database

// 1. Cek Autentikasi dan Otorisasi
// Pastikan pengguna sudah login dan perannya adalah mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    // Jika tidak, redirect ke halaman login
    header("Location: ../login.php");
    exit();
}

// 2. Validasi Input
// Pastikan id_matkul ada di URL
if (!isset($_GET['id_matkul'])) {
    // Jika tidak ada, kembalikan ke halaman katalog
    header("Location: courses.php?status=error_no_id");
    exit();
}

$id_matkul = intval($_GET['id_matkul']);
$id_mahasiswa = $_SESSION['user_id'];

// 3. Cek Pendaftaran Duplikat
// Periksa apakah mahasiswa sudah terdaftar di praktikum ini sebelumnya
$check_stmt = $conn->prepare("SELECT id FROM pendaftaran_praktikum WHERE id_mahasiswa = ? AND id_matkul = ?");
$check_stmt->bind_param("ii", $id_mahasiswa, $id_matkul);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    // Jika sudah terdaftar, kembalikan ke halaman katalog dengan status 'already_enrolled'
    $check_stmt->close();
    header("Location: courses.php?status=already_enrolled");
    exit();
}
$check_stmt->close();

// 4. Proses Pendaftaran
// Jika semua pengecekan lolos, masukkan data baru ke tabel pendaftaran
$insert_stmt = $conn->prepare("INSERT INTO pendaftaran_praktikum (id_mahasiswa, id_matkul) VALUES (?, ?)");
$insert_stmt->bind_param("ii", $id_mahasiswa, $id_matkul);

if ($insert_stmt->execute()) {
    // Jika berhasil, redirect ke halaman "Praktikum Saya" dengan status sukses
    $insert_stmt->close();
    $conn->close();
    header("Location: my_courses.php?status=enroll_success");
    exit();
} else {
    // Jika gagal, kembalikan ke halaman katalog dengan status error
    $insert_stmt->close();
    $conn->close();
    header("Location: courses.php?status=enroll_failed");
    exit();
}