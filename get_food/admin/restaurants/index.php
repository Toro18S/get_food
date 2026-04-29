<?php
require_once '../../config/functions.php';

// 1. Cek Login Admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    redirect('auth/login.php');
}

// 2. LOGIKA PENYIMPANAN DATA (Wajib di Paling Atas)
if (isset($_POST['submit_resto'])) {
    $name    = htmlspecialchars($_POST['name']);
    $address = htmlspecialchars($_POST['address']);
    $phone   = htmlspecialchars($_POST['phone']);

    // Query Insert Sesuai Database Kamu
    $query = "INSERT INTO restaurants (name, address, phone) VALUES ('$name', '$address', '$phone')";

    if (query($query)) {
        set_flash_message('success', 'Cabang baru berhasil disimpan!');
        // Refresh halaman agar data muncul
        header("Location: index.php");
        exit;
    } else {
        set_flash_message('error', 'Gagal menyimpan data ke database.');
    }
}

// 3. Ambil Data untuk Tabel
$restaurants = query("SELECT * FROM restaurants ORDER BY restaurant_id ASC");

require_once '../../layouts/header.php';
?>

<div class="flex bg-gray-100 min-h-screen">
    <?php require_once '../../layouts/sidebar_admin.php'; ?>

    <main class="flex-1 ml-64 p-8 relative">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">🏢 Manajemen Cabang Restoran</h1>
            
            <button onclick="toggleModal('modalResto')" class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg font-semibold shadow transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Tambah Cabang
            </button>
        </div>

        <?php show_flash_message(); ?>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">ID</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Nama Cabang</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Alamat</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">No. Telp</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($restaurants)): ?>
                        <tr><td colspan="5" class="text-center py-6 text-gray-400">Belum ada data restoran.</td></tr>
                    <?php else: ?>
                        <?php foreach ($restaurants as $resto) : ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-bold text-gray-700">#<?php echo $resto['restaurant_id']; ?></td>
                            <td class="px-6 py-4 font-semibold text-gray-800"><?php echo $resto['name']; ?></td>
                            <td class="px-6 py-4 text-gray-600 text-sm truncate max-w-xs"><?php echo $resto['address']; ?></td>
                            <td class="px-6 py-4 text-gray-600 text-sm"><?php echo $resto['phone']; ?></td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-3">
                                    <a href="edit.php?id=<?php echo $resto['restaurant_id']; ?>" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    <a href="delete.php?id=<?php echo $resto['restaurant_id']; ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Hapus cabang ini?');"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<div id="modalResto" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50 transition-opacity" onclick="toggleModal('modalResto')"></div>
    
    <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full mx-auto mt-20 p-6 animate-fade-in-down">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Tambah Cabang Baru</h3>
            <button onclick="toggleModal('modalResto')" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
        
        <form action="" method="POST">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Restoran</label>
                <input type="text" name="name" class="w-full px-4 py-2 border rounded-lg focus:border-orange-500 focus:outline-none" required placeholder="Contoh: Cabang Bali">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat</label>
                <textarea name="address" rows="2" class="w-full px-4 py-2 border rounded-lg focus:border-orange-500 focus:outline-none" required></textarea>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">No. Telp</label>
                <input type="text" name="phone" class="w-full px-4 py-2 border rounded-lg focus:border-orange-500 focus:outline-none" required>
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" onclick="toggleModal('modalResto')" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                <button type="submit" name="submit_resto" class="px-4 py-2 text-white bg-orange-500 rounded-lg hover:bg-orange-600 font-bold">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal(modalID) {
        const modal = document.getElementById(modalID);
        modal.classList.toggle('hidden');
    }
</script>
</body>
</html>