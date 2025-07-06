<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

// Proses Simpan (Create & Update)
if (isset($_POST['simpan'])) {
    $id = $_POST['id'];
    $kode_matkul = trim($_POST['kode_matkul']);
    $nama_matkul = trim($_POST['nama_matkul']);
    $deskripsi = trim($_POST['deskripsi']);
    $gambar_baru = '';

    // LOGIKA UPLOAD GAMBAR BARU
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../uploads/images/";
        $gambar_baru = time() . '_' . basename($_FILES["gambar"]["name"]);
        $target_file = $target_dir . $gambar_baru;
        move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file);
    }

    // Jika ada ID, berarti ini adalah proses Update
    if (!empty($id)) {
        $sql = "UPDATE mata_praktikum SET kode_matkul=?, nama_matkul=?, deskripsi=?" . (!empty($gambar_baru) ? ", gambar=?" : "") . " WHERE id=?";
        $stmt = $conn->prepare($sql);

        if (!empty($gambar_baru)) {
            // Jika ada gambar baru, hapus gambar lama
            $old_img_stmt = $conn->prepare("SELECT gambar FROM mata_praktikum WHERE id = ?");
            $old_img_stmt->bind_param("i", $id);
            $old_img_stmt->execute();
            $old_img_file = $old_img_stmt->get_result()->fetch_assoc()['gambar'];
            if (!empty($old_img_file) && file_exists("../uploads/images/" . $old_img_file)) {
                unlink("../uploads/images/" . $old_img_file);
            }
            $old_img_stmt->close();

            $stmt->bind_param("ssssi", $kode_matkul, $nama_matkul, $deskripsi, $gambar_baru, $id);
        } else {
            $stmt->bind_param("sssi", $kode_matkul, $nama_matkul, $deskripsi, $id);
        }
    } 
    // Jika tidak ada ID, berarti ini adalah proses Create
    else {
        $stmt = $conn->prepare("INSERT INTO mata_praktikum (kode_matkul, nama_matkul, deskripsi, gambar) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $kode_matkul, $nama_matkul, $deskripsi, $gambar_baru);
    }

    $stmt->execute();
    $stmt->close();
}

// Proses Hapus (Delete)
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    // Sebelum menghapus data, ambil nama file gambar untuk dihapus dari server
    $img_stmt = $conn->prepare("SELECT gambar FROM mata_praktikum WHERE id = ?");
    $img_stmt->bind_param("i", $id);
    $img_stmt->execute();
    $img_file = $img_stmt->get_result()->fetch_assoc()['gambar'];
    if (!empty($img_file) && file_exists("../uploads/images/" . $img_file)) {
        unlink("../uploads/images/" . $img_file);
    }
    $img_stmt->close();

    // Hapus data dari database
    $stmt = $conn->prepare("DELETE FROM mata_praktikum WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: praktikum.php");
exit();
?>