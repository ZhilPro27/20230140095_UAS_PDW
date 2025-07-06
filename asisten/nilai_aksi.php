<?php
session_start();
require_once '../config.php';

// Proteksi halaman
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

if (isset($_POST['simpan_nilai'])) {
    $id_pengumpulan = $_POST['id_pengumpulan'];
    $nilai = $_POST['nilai'];
    $feedback = trim($_POST['feedback']);

    if ($nilai < 0 || $nilai > 100) {
        // Handle error: nilai tidak valid
        header("Location: nilai.php?id=$id_pengumpulan&status=gagal_nilai_invalid");
        exit();
    }

    $stmt = $conn->prepare("UPDATE pengumpulan SET nilai = ?, feedback = ? WHERE id = ?");
    $stmt->bind_param("isi", $nilai, $feedback, $id_pengumpulan);

    if ($stmt->execute()) {
        header("Location: laporan.php?status=sukses_nilai");
    } else {
        header("Location: nilai.php?id=$id_pengumpulan&status=gagal");
    }
    
    $stmt->close();
    $conn->close();

} else {
    // Jika akses langsung, redirect ke halaman laporan
    header("Location: laporan.php");
    exit();
}