<?php
session_start();
require_once '../config.php';

// Proteksi halaman
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}

// Proses Simpan (Create & Update)
if (isset($_POST['simpan'])) {
    $id = $_POST['id'];
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = $_POST['password'];

    // --- PROSES UPDATE ---
    if (!empty($id)) {
        // PENAMBAHAN: Cek apakah email baru sudah digunakan oleh pengguna LAIN
        $email_check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $email_check_stmt->bind_param("si", $email, $id);
        $email_check_stmt->execute();
        $email_result = $email_check_stmt->get_result();
        if ($email_result->num_rows > 0) {
            // Jika email sudah ada, kembali dengan pesan error
            header("Location: pengguna.php?status=email_exists");
            exit();
        }
        $email_check_stmt->close();

        // Cek apakah password diisi atau tidak
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE users SET nama=?, email=?, password=?, role=? WHERE id=?");
            $stmt->bind_param("ssssi", $nama, $email, $hashed_password, $role, $id);
        } else {
            // Jika password kosong, jangan update password
            $stmt = $conn->prepare("UPDATE users SET nama=?, email=?, role=? WHERE id=?");
            $stmt->bind_param("sssi", $nama, $email, $role, $id);
        }
    } 
    // --- PROSES CREATE ---
    else {
        // Untuk pengguna baru, cek apakah email sudah ada di seluruh tabel
        $email_check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $email_check_stmt->bind_param("s", $email);
        $email_check_stmt->execute();
        if ($email_check_stmt->get_result()->num_rows > 0) {
            header("Location: pengguna.php?status=email_exists");
            exit();
        }
        $email_check_stmt->close();
        
        if (empty($password)) {
            header("Location: pengguna.php?status=gagal_password_kosong");
            exit();
        }
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nama, $email, $hashed_password, $role);
    }

    $stmt->execute();
    $stmt->close();
}

// --- PROSES HAPUS (Tidak Berubah) ---
if (isset($_GET['hapus'])) {
    $id_to_delete = $_GET['hapus'];
    if ($id_to_delete == $_SESSION['user_id']) {
        header("Location: pengguna.php?status=gagal_hapus_diri");
        exit();
    }
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id_to_delete);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
// Redirect kembali ke halaman pengguna
header("Location: pengguna.php?status=sukses");
exit();
?>