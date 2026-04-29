<?php
require_once '../config/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $role_type = $_POST['role'];

    // --- LOGIKA LOGIN CUSTOMER (REVISI) ---
    if ($role_type == 'customer') {
        $name  = htmlspecialchars($_POST['name']);
        $phone = htmlspecialchars($_POST['phone']); // Ambil No HP

        if (empty($name) || empty($phone)) {
            set_flash_message('error', 'Nama dan Nomor HP wajib diisi!');
            redirect('auth/login.php');
        }

        // Simpan data pelanggan ke SESSION
        $_SESSION['user'] = [
            'role' => 'customer',
            'name' => $name,
            'phone' => $phone, // Simpan HP di sesi
            'table_number' => null, // Meja belum ada (nanti ditentukan sistem)
            'login_time' => date('Y-m-d H:i:s')
        ];

        set_flash_message('success', "Selamat datang, $name!");
        redirect('customer/home.php'); // Redirect ke Home, bukan Menu
    }

    // --- LOGIKA LOGIN ADMIN ---
    else if ($role_type == 'admin') {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = $_POST['password'];

        $query = "SELECT * FROM employees WHERE username = '$username'";
        $result = query($query);

        if (!empty($result)) {
            $data = $result[0];
            if ($password === $data['password']) {
                $_SESSION['user'] = [
                    'role' => 'admin',
                    'id' => $data['employee_id'],
                    'name' => $data['name'],
                    'job_role' => $data['role'],
                    'restaurant_id' => $data['restaurant_id']
                ];
                set_flash_message('success', "Login berhasil! Halo, " . $data['name']);
                redirect('admin/index.php');
            } else {
                set_flash_message('error', 'Password salah!');
                redirect('auth/login.php');
            }
        } else {
            set_flash_message('error', 'Username tidak ditemukan!');
            redirect('auth/login.php');
        }
    }
} else {
    redirect('auth/login.php');
}
?>