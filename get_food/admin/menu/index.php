<?php
require_once '../../config/functions.php';

// Cek akses admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    redirect('auth/login.php');
}

// Ambil daftar semua restoran untuk ditampilkan di Checkbox Modal
$all_restos = query("SELECT * FROM restaurants");

// --- LOGIKA TAMBAH MENU BARU ---
if (isset($_POST['submit_menu'])) {
    $name  = htmlspecialchars($_POST['name']);
    $desc  = htmlspecialchars($_POST['description']);
    $cat   = $_POST['category'];
    $price = $_POST['price'];

    // LOGIKA GAMBAR (Upload vs URL)
    $image = 'default.jpg'; 

    // Cek 1: Apakah ada file yang diupload?
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $fileName = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], '../../assets/img/' . $fileName);
        $image = $fileName;
    } 
    // Cek 2: Jika tidak upload, apakah ada Link URL?
    elseif (!empty($_POST['image_url_input'])) {
        $image = htmlspecialchars($_POST['image_url_input']);
    }

    // 1. Insert ke Tabel Master Menu
    $query = "INSERT INTO menu_items (name, description, category, base_price, image_url) 
              VALUES ('$name', '$desc', '$cat', '$price', '$image')";
    
    if (query($query)) {
        // Ambil ID menu yang barusan dibuat
        $new_menu_id = mysqli_insert_id($conn);

        // 2. Insert Ketersediaan Cabang (Sesuai Pilihan Checkbox)
        if (isset($_POST['restos'])) {
            foreach ($_POST['restos'] as $resto_id) {
                $q_relasi = "INSERT INTO restaurant_menus (restaurant_id, menu_id, is_available) VALUES ('$resto_id', '$new_menu_id', 1)";
                query($q_relasi);
            }
        } else {
            // Opsional: Jika tidak ada yang dicentang, otomatis masuk ke Cabang Pusat (ID 1) atau biarkan kosong
             $q_relasi = "INSERT INTO restaurant_menus (restaurant_id, menu_id, is_available) VALUES (1, '$new_menu_id', 1)";
             query($q_relasi);
        }

        set_flash_message('success', 'Menu berhasil ditambahkan!');
        redirect('admin/menu/index.php');
    } else {
        set_flash_message('error', 'Gagal menambah menu.');
    }
}

// Ambil semua menu untuk tabel
$menus = query("SELECT * FROM menu_items ORDER BY category ASC, name ASC");

require_once '../../layouts/header.php';
?>

<div class="flex bg-gray-100 min-h-screen">
    <?php require_once '../../layouts/sidebar_admin.php'; ?>

    <main class="flex-1 ml-64 p-8 relative">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Daftar Menu Makanan</h1>
            <button onclick="toggleModal('modalMenu')" class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg font-semibold shadow transition flex items-center gap-2">
                <span>+</span> Tambah Menu
            </button>
        </div>

        <?php show_flash_message(); ?>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Gambar</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Nama Menu</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Kategori</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Harga</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($menus)): ?>
                        <tr><td colspan="5" class="text-center py-6 text-gray-400">Belum ada menu.</td></tr>
                    <?php else: ?>
                        <?php foreach ($menus as $menu) : ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-3">
                                <img src="<?php echo menu_image($menu['image_url']); ?>" class="w-12 h-12 object-cover rounded-lg border">
                            <td class="px-6 py-3 font-medium text-gray-800">
                                <?php echo $menu['name']; ?>
                                <div class="text-xs text-gray-400 truncate w-32"><?php echo $menu['description']; ?></div>
                            </td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                                    <?php echo ucfirst($menu['category']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-3 font-bold text-gray-700"><?php echo format_rupiah($menu['base_price']); ?></td>
                            <td class="px-6 py-3 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="edit.php?id=<?php echo $menu['menu_id']; ?>" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                    <a href="delete.php?id=<?php echo $menu['menu_id']; ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Hapus menu?');"><i class="fas fa-trash"></i></a>
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

<div id="modalMenu" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50 transition-opacity" onclick="toggleModal('modalMenu')"></div>
    
    <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full mx-auto mt-10 p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Tambah Menu Baru</h3>
            <button onclick="toggleModal('modalMenu')" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
        
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Menu</label>
                <input type="text" name="name" class="w-full px-4 py-2 border rounded-lg focus:border-orange-500 focus:outline-none" required>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori</label>
                    <select name="category" class="w-full px-4 py-2 border rounded-lg focus:border-orange-500 focus:outline-none">
                        <option value="makanan">Makanan</option>
                        <option value="minuman">Minuman</option>
                        <option value="snack">Snack</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Harga</label>
                    <input type="number" name="price" class="w-full px-4 py-2 border rounded-lg focus:border-orange-500 focus:outline-none" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="2" class="w-full px-4 py-2 border rounded-lg focus:border-orange-500 focus:outline-none"></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Foto Menu</label>
                
                <input type="file" name="image" class="w-full text-sm border rounded-lg p-2 text-gray-500 mb-2">
                
                <p class="text-center text-xs text-gray-400 font-bold mb-2">- ATAU -</p>
                
                <input type="url" name="image_url_input" placeholder="Paste Link Gambar (https://...)" class="w-full px-4 py-2 border rounded-lg focus:border-orange-500 focus:outline-none bg-gray-50">
            </div>

            <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-100">
                <label class="block text-sm font-bold text-gray-700 mb-2">Tersedia di Cabang:</label>
                <div class="grid grid-cols-2 gap-2">
                    <?php foreach ($all_restos as $resto) : ?>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="restos[]" value="<?php echo $resto['restaurant_id']; ?>" class="rounded text-orange-500 focus:ring-orange-500" checked>
                            <span class="text-sm text-gray-600"><?php echo $resto['name']; ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" onclick="toggleModal('modalMenu')" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                <button type="submit" name="submit_menu" class="px-4 py-2 text-white bg-orange-500 rounded-lg hover:bg-orange-600 font-bold">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal(modalID) {
        document.getElementById(modalID).classList.toggle('hidden');
    }
</script>
</body>
</html>