<?php
require_once '../../config/functions.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    redirect('auth/login.php');
}

// --- LOGIKA TAMBAH MEJA OTOMATIS (BULK) ---
if (isset($_POST['submit_bulk'])) {
    $resto_id = $_POST['restaurant_id'];
    
    // Ambil jumlah yang diinput
    $qty_couple = (int) $_POST['qty_couple']; // Kapasitas 2
    $qty_family = (int) $_POST['qty_family']; // Kapasitas 4
    $qty_large  = (int) $_POST['qty_large'];  // Kapasitas 8

    $total_added = 0;

    // 1. Generate Meja Couple (2 Orang)
    for ($i = 1; $i <= $qty_couple; $i++) {
        $code = rand(100, 999); // Kode acak biar unik
        $name = "C-$code (Couple)";
        if(query("INSERT INTO restaurant_tables (restaurant_id, table_name, capacity) VALUES ('$resto_id', '$name', 2)")) {
            $total_added++;
        }
    }

    // 2. Generate Meja Family (4 Orang)
    for ($i = 1; $i <= $qty_family; $i++) {
        $code = rand(100, 999);
        $name = "F-$code (Family)";
        if(query("INSERT INTO restaurant_tables (restaurant_id, table_name, capacity) VALUES ('$resto_id', '$name', 4)")) {
            $total_added++;
        }
    }

    // 3. Generate Meja Large (8 Orang)
    for ($i = 1; $i <= $qty_large; $i++) {
        $code = rand(100, 999);
        $name = "L-$code (Large)";
        if(query("INSERT INTO restaurant_tables (restaurant_id, table_name, capacity) VALUES ('$resto_id', '$name', 8)")) {
            $total_added++;
        }
    }

    if ($total_added > 0) {
        set_flash_message('success', "Berhasil menambahkan $total_added meja baru!");
    } else {
        set_flash_message('warning', 'Tidak ada meja yang ditambahkan (Input 0).');
    }
    
    redirect('admin/tables/index.php');
}

// --- LOGIKA HAPUS MEJA ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if(query("DELETE FROM restaurant_tables WHERE table_id = '$id'")){
        set_flash_message('success', 'Meja dihapus.');
        redirect('admin/tables/index.php');
    }
}

// --- LOGIKA RESET MEJA PER RESTO (Hapus Semua) ---
if (isset($_GET['reset_resto'])) {
    $rid = $_GET['reset_resto'];
    if(query("DELETE FROM restaurant_tables WHERE restaurant_id = '$rid'")){
        set_flash_message('success', 'Semua meja di restoran ini berhasil direset.');
        redirect('admin/tables/index.php');
    }
}

// Ambil Data Meja
$tables = query("SELECT t.*, r.name as resto_name 
                 FROM restaurant_tables t 
                 JOIN restaurants r ON t.restaurant_id = r.restaurant_id 
                 ORDER BY r.name ASC, t.capacity ASC");

$restaurants = query("SELECT * FROM restaurants");

require_once '../../layouts/header.php';
?>

<div class="flex bg-gray-100 min-h-screen">
    <?php require_once '../../layouts/sidebar_admin.php'; ?>

    <main class="flex-1 ml-64 p-8 relative">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">🪑 Manajemen Meja</h1>
            <button onclick="toggleModal('modalTable')" class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg font-bold shadow transition flex items-center gap-2">
                <i class="fas fa-magic"></i> Generate Meja Otomatis
            </button>
        </div>

        <?php show_flash_message(); ?>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-sm text-gray-600">Cabang Restoran</th>
                        <th class="px-6 py-3 text-sm text-gray-600">Kode Meja</th>
                        <th class="px-6 py-3 text-sm text-gray-600">Kapasitas</th>
                        <th class="px-6 py-3 text-sm text-gray-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($tables)): ?>
                        <tr><td colspan="4" class="text-center py-6 text-gray-400">Belum ada data meja.</td></tr>
                    <?php else: ?>
                        <?php foreach ($tables as $t) : ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 text-gray-800 font-medium"><?php echo $t['resto_name']; ?></td>
                            <td class="px-6 py-3 font-bold text-gray-700"><?php echo $t['table_name']; ?></td>
                            <td class="px-6 py-3">
                                <span class="
                                    <?php 
                                        if($t['capacity'] <= 2) echo 'bg-blue-100 text-blue-700';
                                        elseif($t['capacity'] <= 4) echo 'bg-green-100 text-green-700';
                                        else echo 'bg-purple-100 text-purple-700';
                                    ?> 
                                    text-xs font-bold px-2 py-1 rounded">
                                    <i class="fas fa-users"></i> <?php echo $t['capacity']; ?> Orang
                                </span>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <a href="?delete=<?php echo $t['table_id']; ?>" class="text-gray-400 hover:text-red-500 transition" onclick="return confirm('Hapus meja ini?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-8">
            <h4 class="text-sm font-bold text-gray-500 mb-3 uppercase">Reset Data Per Cabang</h4>
            <div class="flex gap-2 flex-wrap">
                <?php foreach($restaurants as $r): ?>
                    <a href="?reset_resto=<?php echo $r['restaurant_id']; ?>" 
                       onclick="return confirm('YAKIN? Semua meja di cabang <?php echo $r['name']; ?> akan dihapus!')"
                       class="text-xs border border-red-200 bg-red-50 text-red-600 px-3 py-1 rounded hover:bg-red-600 hover:text-white transition">
                       Hapus Semua Meja: <b><?php echo $r['name']; ?></b>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

    </main>
</div>

<div id="modalTable" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50 transition-opacity" onclick="toggleModal('modalTable')"></div>
    <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full mx-auto mt-20 p-6 animate-fade-in-down">
        <div class="flex justify-between items-center mb-4 border-b pb-3">
            <h3 class="text-xl font-bold text-gray-800">Generate Meja Otomatis</h3>
            <button onclick="toggleModal('modalTable')" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
        
        <form action="" method="POST">
            <div class="mb-5">
                <label class="block text-sm font-bold text-gray-700 mb-1">Pilih Cabang Restoran</label>
                <select name="restaurant_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 outline-none">
                    <?php foreach ($restaurants as $r) : ?>
                        <option value="<?php echo $r['restaurant_id']; ?>"><?php echo $r['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg space-y-4 border border-gray-100">
                <p class="text-xs text-gray-500 font-bold uppercase">Masukkan Jumlah Meja:</p>
                
                <div class="flex justify-between items-center">
                    <div>
                        <span class="block font-bold text-gray-700">Meja Couple</span>
                        <span class="text-xs text-gray-500">Kapasitas 2 Orang</span>
                    </div>
                    <input type="number" name="qty_couple" value="0" min="0" class="w-20 text-center px-2 py-1 border rounded-lg font-bold text-gray-700 focus:border-orange-500 outline-none">
                </div>

                <div class="flex justify-between items-center">
                    <div>
                        <span class="block font-bold text-gray-700">Meja Family</span>
                        <span class="text-xs text-gray-500">Kapasitas 4 Orang</span>
                    </div>
                    <input type="number" name="qty_family" value="0" min="0" class="w-20 text-center px-2 py-1 border rounded-lg font-bold text-gray-700 focus:border-orange-500 outline-none">
                </div>

                <div class="flex justify-between items-center">
                    <div>
                        <span class="block font-bold text-gray-700">Meja Large / VIP</span>
                        <span class="text-xs text-gray-500">Kapasitas 8 Orang</span>
                    </div>
                    <input type="number" name="qty_large" value="0" min="0" class="w-20 text-center px-2 py-1 border rounded-lg font-bold text-gray-700 focus:border-orange-500 outline-none">
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="toggleModal('modalTable')" class="px-4 py-2 bg-gray-100 rounded-lg font-semibold text-gray-600 hover:bg-gray-200">Batal</button>
                <button type="submit" name="submit_bulk" class="px-4 py-2 bg-orange-500 text-white rounded-lg font-bold hover:bg-orange-600 shadow-md">
                    <i class="fas fa-magic mr-1"></i> Generate
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal(id) { document.getElementById(id).classList.toggle('hidden'); }
</script>
</body>
</html>