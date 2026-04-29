<?php
require_once '../../config/functions.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    redirect('auth/login.php');
}

$id = $_GET['id'];
$user = query("SELECT * FROM employees WHERE employee_id = '$id'")[0];
$restaurants = query("SELECT * FROM restaurants");

if (isset($_POST['update'])) {
    $name     = htmlspecialchars($_POST['name']);
    $role     = $_POST['role'];
    $resto_id = $_POST['restaurant_id'];
    $password = $_POST['password'];

    // Cek ganti password atau tidak
    if (!empty($password)) {
        $query = "UPDATE employees SET name='$name', role='$role', restaurant_id='$resto_id', password='$password' WHERE employee_id='$id'";
    } else {
        $query = "UPDATE employees SET name='$name', role='$role', restaurant_id='$resto_id' WHERE employee_id='$id'";
    }

    if (query($query)) {
        set_flash_message('success', 'Data pegawai diperbarui!');
        redirect('admin/users/index.php');
    } else {
        set_flash_message('error', 'Gagal update data.');
    }
}

require_once '../../layouts/header.php';
?>

<div class="flex bg-gray-100 min-h-screen">
    <?php require_once '../../layouts/sidebar_admin.php'; ?>

    <main class="flex-1 ml-64 p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Pegawai</h1>

        <div class="bg-white rounded-xl shadow-sm p-8 max-w-xl">
            <form action="" method="POST">
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="<?php echo $user['name']; ?>" class="w-full px-4 py-2 border rounded-lg focus:border-orange-500 focus:outline-none" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Username (Tidak bisa diubah)</label>
                    <input type="text" value="<?php echo $user['username']; ?>" class="w-full px-4 py-2 border rounded-lg bg-gray-100 text-gray-500" readonly>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Password Baru (Opsional)</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengganti" class="w-full px-4 py-2 border rounded-lg focus:border-orange-500 focus:outline-none">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Posisi / Role</label>
                    <select name="role" class="w-full px-4 py-2 border rounded-lg focus:border-orange-500 focus:outline-none">
                        <option value="pelayan" <?php if($user['role']=='pelayan') echo 'selected'; ?>>Pelayan</option>
                        <option value="koki" <?php if($user['role']=='koki') echo 'selected'; ?>>Koki</option>
                        <option value="kasir" <?php if($user['role']=='kasir') echo 'selected'; ?>>Kasir</option>
                        <option value="admin" <?php if($user['role']=='admin') echo 'selected'; ?>>Admin</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Penempatan Cabang</label>
                    <select name="restaurant_id" class="w-full px-4 py-2 border rounded-lg focus:border-orange-500 focus:outline-none">
                        <?php foreach ($restaurants as $resto) : ?>
                            <option value="<?php echo $resto['restaurant_id']; ?>" <?php if($user['restaurant_id']==$resto['restaurant_id']) echo 'selected'; ?>>
                                <?php echo $resto['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="flex gap-3">
                    <button type="submit" name="update" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition">Simpan Perubahan</button>
                    <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-lg transition">Batal</a>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>