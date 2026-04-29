<?php
require_once '../../config/functions.php';

// Cek sesi admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    redirect('auth/login.php');
}

$id = $_GET['id'];

// 1. Ambil Data Menu Utama
$menu = query("SELECT * FROM menu_items WHERE menu_id = '$id'")[0];

// 2. Ambil Daftar Semua Restoran
$all_restos = query("SELECT * FROM restaurants");

// 3. Ambil Data Ketersediaan Menu ini (Ada di cabang mana aja?)
$availability = query("SELECT restaurant_id FROM restaurant_menus WHERE menu_id = '$id'");
$available_ids = array_column($availability, 'restaurant_id'); // Jadi array [1, 2, 5]


// --- PROSES UPDATE ---
if (isset($_POST['update'])) {
    $name  = htmlspecialchars($_POST['name']);
    $desc  = htmlspecialchars($_POST['description']);
    $cat   = $_POST['category'];
    $price = $_POST['price'];
    $oldImage = $_POST['oldImage'];

    // LOGIKA GAMBAR UPDATE
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        // User Upload File Baru
        $fileName = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], '../../assets/img/' . $fileName);
        $image = $fileName;
        
        // Hapus file lama jika lokal
        if ($oldImage != 'default.jpg' && file_exists('../../assets/img/' . $oldImage)) {
            unlink('../../assets/img/' . $oldImage);
        }
    } elseif (!empty($_POST['image_url_input'])) {
        // User Pakai Link Baru
        $image = htmlspecialchars($_POST['image_url_input']);
    } else {
        // User Tidak Ganti Gambar
        $image = $oldImage;
    }

    // 1. Update Tabel Menu Items
    $query = "UPDATE menu_items SET 
              name = '$name', description = '$desc', category = '$cat', base_price = '$price', image_url = '$image'
              WHERE menu_id = '$id'";
    
    // 2. Update Ketersediaan Cabang (Many-to-Many)
    // Hapus dulu semua relasi lama untuk menu ini
    query("DELETE FROM restaurant_menus WHERE menu_id = '$id'");

    // Masukkan relasi baru berdasarkan Checkbox yang dipilih
    if (isset($_POST['restos'])) {
        foreach ($_POST['restos'] as $resto_id) {
            query("INSERT INTO restaurant_menus (restaurant_id, menu_id) VALUES ('$resto_id', '$id')");
        }
    }

    if (query($query)) {
        set_flash_message('success', 'Menu berhasil diperbarui & disinkronkan ke cabang!');
        redirect('admin/menu/index.php');
    } else {
        set_flash_message('error', 'Gagal update menu.');
    }
}

require_once '../../layouts/header.php';
?>

<div class="flex bg-gray-100 min-h-screen">
    <?php require_once '../../layouts/sidebar_admin.php'; ?>

    <main class="flex-1 ml-64 p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Menu & Ketersediaan</h1>

        <div class="flex gap-6">
            <div class="bg-white rounded-xl shadow-sm p-8 max-w-2xl flex-1">
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="oldImage" value="<?php echo $menu['image_url']; ?>">
                    
                    <div class="mb-5">
                        <label class="block text-gray-700 font-semibold mb-2">Nama Menu</label>
                        <input type="text" name="name" value="<?php echo $menu['name']; ?>" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-orange-500 focus:outline-none" required>
                    </div>

                    <div class="grid grid-cols-2 gap-6 mb-5">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Kategori</label>
                            <select name="category" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-orange-500 focus:outline-none">
                                <option value="makanan" <?php echo ($menu['category'] == 'makanan') ? 'selected' : ''; ?>>Makanan</option>
                                <option value="minuman" <?php echo ($menu['category'] == 'minuman') ? 'selected' : ''; ?>>Minuman</option>
                                <option value="snack" <?php echo ($menu['category'] == 'snack') ? 'selected' : ''; ?>>Snack</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Harga (Rp)</label>
                            <input type="number" name="price" value="<?php echo $menu['base_price']; ?>" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-orange-500 focus:outline-none" required>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-gray-700 font-semibold mb-2">Deskripsi Singkat</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-orange-500 focus:outline-none"><?php echo $menu['description']; ?></textarea>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Foto Menu</label>
                        <div class="flex items-center gap-4 mb-3">
                            <img src="<?php echo menu_image($menu['image_url']); ?>" class="w-20 h-20 object-cover rounded shadow-sm border" alt="Current Image">
                            <div class="text-sm text-gray-500">
                                <p>Gambar saat ini.</p>
                            </div>
                        </div>
                        
                        <input type="file" name="image" class="w-full text-sm border rounded-lg p-2 mb-2">
                        
                        <p class="text-xs text-gray-500 font-bold mb-2">ATAU Ganti Link URL:</p>
                        
                        <input type="url" name="image_url_input" 
                               value="<?php echo (strpos($menu['image_url'], 'http') === 0) ? $menu['image_url'] : ''; ?>"
                               placeholder="Paste Link Gambar Baru" 
                               class="w-full px-4 py-2 border rounded-lg bg-gray-50">
                    </div>
            </div>

            <div class="w-80">
                <div class="bg-white rounded-xl shadow-sm p-6 mb-4">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Tersedia di Cabang:</h3>
                    <div class="space-y-3">
                        <?php foreach ($all_restos as $resto) : ?>
                            <label class="flex items-center gap-3 cursor-pointer p-2 hover:bg-gray-50 rounded transition">
                                <input type="checkbox" name="restos[]" 
                                       value="<?php echo $resto['restaurant_id']; ?>" 
                                       class="w-5 h-5 text-orange-500 rounded focus:ring-orange-500"
                                       <?php echo in_array($resto['restaurant_id'], $available_ids) ? 'checked' : ''; ?>>
                                
                                <span class="text-gray-700 font-medium"><?php echo $resto['name']; ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <p class="text-xs text-gray-400 mt-4 italic">
                        *Hilangkan centang jika menu ini tidak dijual di cabang tersebut.
                    </p>
                </div>

                <div class="flex gap-3">
                    <button type="submit" name="update" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition">Update Data</button>
                    <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-6 rounded-lg transition text-center">Batal</a>
                </div>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>