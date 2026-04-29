<?php
require_once '../../config/functions.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    redirect('auth/login.php');
}

$id = $_GET['id'];

// Cegah hapus diri sendiri
if ($id == $_SESSION['user']['id']) {
    set_flash_message('error', 'Anda tidak bisa menghapus akun sendiri!');
    redirect('admin/users/index.php');
    exit;
}

if (query("DELETE FROM employees WHERE employee_id = '$id'")) {
    set_flash_message('success', 'Data pegawai berhasil dihapus.');
} else {
    set_flash_message('error', 'Gagal menghapus pegawai.');
}

redirect('admin/users/index.php');
?>