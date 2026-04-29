<?php
require_once '../../config/functions.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    redirect('auth/login.php');
}

$id = $_GET['id'];
$resto = query("SELECT * FROM restaurants WHERE restaurant_id = '$id'")[0];

if (isset($_POST['update'])) {
    $name    = htmlspecialchars($_POST['name']);
    $address = htmlspecialchars($_POST['address']);
    $phone   = htmlspecialchars($_POST['phone']);

    $query = "UPDATE restaurants SET name = '$name', address = '$address', phone = '$phone' WHERE restaurant_id = '$id'";

    if (query($query)) {
        set_flash_message('success', 'Data cabang berhasil diperbarui!');
        redirect('admin/restaurants/index.php');
    } else {
        set_flash_message('error', 'Gagal update data.');
    }
}

require_once '../../layouts/header.php';
?>

<div class="flex bg-gray-100 min-h-screen">
    <?php require_once '../../layouts/sidebar_admin.php'; ?>

    <main class="flex-1 ml-64 p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Cabang</h1>

        <div class="bg-white rounded-xl shadow-sm p-8 max-w-2xl">
            <form action="" method="POST">
                <div class="mb-5">
                    <label class="block text-gray-700 font-semibold mb-2">Nama Restoran</label>
                    <input type="text" name="name" value="<?php echo $resto['name']; ?>" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-orange-500 focus:outline-none" required>
                </div>

                <div class="mb-5">
                    <label class="block text-gray-700 font-semibold mb-2">Alamat</label>
                    <textarea name="address" rows="3" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-orange-500 focus:outline-none" required><?php echo $resto['address']; ?></textarea>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-semibold mb-2">Nomor Telepon</label>
                    <input type="text" name="phone" value="<?php echo $resto['phone']; ?>" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-orange-500 focus:outline-none" required>
                </div>

                <div class="flex gap-3">
                    <button type="submit" name="update" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition">Update Data</button>
                    <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-6 rounded-lg transition">Batal</a>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>