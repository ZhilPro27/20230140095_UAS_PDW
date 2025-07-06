<?php
session_start();
require_once '../config.php';

// Proteksi halaman, hanya untuk asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

// Ambil id_matkul dari POST atau GET untuk keperluan redirect
$id_matkul = $_POST['id_matkul'] ?? $_GET['id_matkul'];

// Proses Simpan (Create & Update)
if (isset($_POST['simpan'])) {
    $id = $_POST['id'];
    $nama_modul = trim($_POST['nama_modul']);
    $deskripsi = trim($_POST['deskripsi']);
    
    $file_materi_baru = '';
    $gambar_baru = '';

    // Logika upload file materi
    if (isset($_FILES['file_materi']) && $_FILES['file_materi']['error'] == 0) {
        $target_dir_materi = "../uploads/materi/";
        $file_materi_baru = time() . '_materi_' . basename($_FILES["file_materi"]["name"]);
        move_uploaded_file($_FILES["file_materi"]["tmp_name"], $target_dir_materi . $file_materi_baru);
    }
    
    // Logika upload file gambar
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir_images = "../uploads/images/";
        $gambar_baru = time() . '_gambar_' . basename($_FILES["gambar"]["name"]);
        move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_dir_images . $gambar_baru);
    }

    // --- PROSES UPDATE (BAGIAN YANG DIPERBAIKI) ---
    if (!empty($id)) {
        // Ambil nama file-file lama dari database
        $stmt_old = $conn->prepare("SELECT file_materi, gambar FROM modul WHERE id = ?");
        $stmt_old->bind_param("i", $id);
        $stmt_old->execute();
        $old_files = $stmt_old->get_result()->fetch_assoc();
        $stmt_old->close();

        // Tentukan query berdasarkan file apa yang diupload
        if (!empty($file_materi_baru) && !empty($gambar_baru)) {
            // Kasus 1: Update semua, termasuk materi dan gambar
            $stmt = $conn->prepare("UPDATE modul SET nama_modul=?, deskripsi=?, file_materi=?, gambar=? WHERE id=?");
            $stmt->bind_param("ssssi", $nama_modul, $deskripsi, $file_materi_baru, $gambar_baru, $id);
            if (!empty($old_files['file_materi'])) unlink("../uploads/materi/" . $old_files['file_materi']);
            if (!empty($old_files['gambar'])) unlink("../uploads/images/" . $old_files['gambar']);
        } elseif (!empty($file_materi_baru)) {
            // Kasus 2: Hanya update materi
            $stmt = $conn->prepare("UPDATE modul SET nama_modul=?, deskripsi=?, file_materi=? WHERE id=?");
            $stmt->bind_param("sssi", $nama_modul, $deskripsi, $file_materi_baru, $id);
            if (!empty($old_files['file_materi'])) unlink("../uploads/materi/" . $old_files['file_materi']);
        } elseif (!empty($gambar_baru)) {
            // Kasus 3: Hanya update gambar
            $stmt = $conn->prepare("UPDATE modul SET nama_modul=?, deskripsi=?, gambar=? WHERE id=?");
            $stmt->bind_param("sssi", $nama_modul, $deskripsi, $gambar_baru, $id);
            if (!empty($old_files['gambar'])) unlink("../uploads/images/" . $old_files['gambar']);
        } else {
            // Kasus 4: Tidak ada file yang diupdate, hanya teks
            $stmt = $conn->prepare("UPDATE modul SET nama_modul=?, deskripsi=? WHERE id=?");
            $stmt->bind_param("ssi", $nama_modul, $deskripsi, $id);
        }
        $stmt->execute();
        $stmt->close();

    } else { // --- PROSES CREATE ---
        $stmt = $conn->prepare("INSERT INTO modul (id_matkul, nama_modul, deskripsi, file_materi, gambar) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $id_matkul, $nama_modul, $deskripsi, $file_materi_baru, $gambar_baru);
        $stmt->execute();
        $stmt->close();
    }
}

// Proses Hapus (Delete) - Tidak ada perubahan
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $conn->prepare("SELECT file_materi, gambar FROM modul WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $files = $stmt->get_result()->fetch_assoc();
    if($files) {
        if (!empty($files['file_materi'])) unlink("../uploads/materi/" . $files['file_materi']);
        if (!empty($files['gambar'])) unlink("../uploads/images/" . $files['gambar']);
    }
    $stmt->close();
    
    $stmt = $conn->prepare("DELETE FROM modul WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Redirect kembali ke halaman modul
header("Location: modul.php?id_matkul=$id_matkul");
exit();
?>