<?php
require_once '../config/functions.php';

// Jika sudah login, langsung lempar sesuai role
if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role'] == 'customer') {
        redirect('customer/home.php');
    } else {
        redirect('admin/index.php');
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Reservasi - Get Food</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen px-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden">
        
        <div class="bg-orange-500 p-6 text-center">
            <h1 class="text-white text-2xl font-bold">Get Food</h1>
            <p class="text-orange-100 text-sm">Reservasi Meja & Pesan Makan</p>
        </div>

        <div class="flex border-b">
            <button onclick="switchTab('customer')" id="btn-customer" class="w-1/2 py-4 text-sm font-semibold text-orange-600 border-b-2 border-orange-500 focus:outline-none transition">
                Pelanggan
            </button>
            <button onclick="switchTab('admin')" id="btn-admin" class="w-1/2 py-4 text-sm font-semibold text-gray-500 focus:outline-none hover:text-orange-500 transition">
                Admin / Staf
            </button>
        </div>

        <div class="p-8">
            <?php show_flash_message(); ?>

            <form action="process_login.php" method="POST" id="form-customer">
                <input type="hidden" name="role" value="customer">
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
                    <input type="text" name="name" class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:border-orange-500 focus:outline-none" placeholder="Contoh: Budi Santoso" required>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nomor WhatsApp / HP</label>
                    <input type="number" name="phone" class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:border-orange-500 focus:outline-none" placeholder="Contoh: 08123456789" required>
                </div>

                <button type="submit" class="w-full block bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-4 rounded-lg transition duration-300">
                    Mulai Reservasi
                </button>
            </form>

            <form action="process_login.php" method="POST" id="form-admin" class="hidden">
                <input type="hidden" name="role" value="admin">
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                    <input type="text" name="username" class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:border-blue-500 focus:bg-white focus:outline-none" placeholder="Username pegawai">
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" name="password" class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:border-blue-500 focus:bg-white focus:outline-none" placeholder="********">
                </div>

                <button type="submit" class="w-full block bg-gray-800 hover:bg-gray-900 text-white font-bold py-3 px-4 rounded-lg transition duration-300">
                    Login Staf
                </button>
            </form>
        </div>
    </div>

    <script>
        function switchTab(role) {
            const formCustomer = document.getElementById('form-customer');
            const formAdmin = document.getElementById('form-admin');
            const btnCustomer = document.getElementById('btn-customer');
            const btnAdmin = document.getElementById('btn-admin');

            if (role === 'customer') {
                formCustomer.classList.remove('hidden');
                formAdmin.classList.add('hidden');
                
                btnCustomer.classList.add('text-orange-600', 'border-orange-500');
                btnCustomer.classList.remove('text-gray-500');
                btnAdmin.classList.add('text-gray-500');
                btnAdmin.classList.remove('text-orange-600', 'border-orange-500', 'border-b-2');
            } else {
                formCustomer.classList.add('hidden');
                formAdmin.classList.remove('hidden');

                btnAdmin.classList.add('text-gray-800', 'border-gray-800', 'border-b-2');
                btnAdmin.classList.remove('text-gray-500');
                btnCustomer.classList.add('text-gray-500');
                btnCustomer.classList.remove('text-orange-600', 'border-orange-500');
            }
        }
    </script>
</body>
</html>