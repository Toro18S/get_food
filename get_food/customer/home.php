<?php
require_once '../config/functions.php';

// Cek Login
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'customer') {
    redirect('auth/login.php');
}

// Ambil Menu Random untuk rekomendasi (Ambil 5 biar pas di grid desktop)
$recommendations = query("SELECT * FROM menu_items ORDER BY RAND() LIMIT 10");

require_once '../layouts/header.php';
?>

<?php require_once '../layouts/layout_menu.php'; ?>

<div class="bg-gray-50 min-h-screen pb-24">
    
    <div class="max-w-7xl mx-auto w-full">

        <div class="px-4 md:px-8 mt-6">
            <div class="bg-orange-500 rounded-2xl overflow-hidden shadow-lg relative group">
                <div class="h-40 md:h-80 w-full relative">
                    <img src="<?php echo url('assets/img/banner_promo.jpg'); ?>" 
                         class="w-full h-full object-cover object-center transition duration-700 group-hover:scale-105" 
                         alt="Promo">
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent flex flex-col justify-end p-6">
                        <span class="bg-orange-600 text-white text-xs font-bold px-2 py-1 rounded w-fit mb-2">PROMO SPESIAL</span>
                        <h2 class="text-white text-xl md:text-4xl font-bold mb-1">Diskon 50% Hari Ini!</h2>
                        <p class="text-orange-100 text-xs md:text-base">Khusus pengguna baru untuk menu terpilih.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-4 md:px-8 mt-8">
            <h3 class="font-bold text-gray-800 text-lg mb-4">Kategori</h3>
            <div class="flex justify-between gap-4 md:gap-8">
                <a href="menu.php#cat-makanan" class="flex-1 bg-white border border-gray-100 shadow-sm rounded-xl p-4 flex flex-col items-center hover:border-primary hover:shadow-md transition cursor-pointer group">
                    <div class="bg-orange-50 w-12 h-12 rounded-full flex items-center justify-center text-2xl mb-2 group-hover:bg-primary group-hover:text-white transition">🍜</div>
                    <span class="text-xs font-bold text-gray-600 group-hover:text-primary">Makanan</span>
                </a>
                <a href="menu.php#cat-minuman" class="flex-1 bg-white border border-gray-100 shadow-sm rounded-xl p-4 flex flex-col items-center hover:border-primary hover:shadow-md transition cursor-pointer group">
                    <div class="bg-blue-50 w-12 h-12 rounded-full flex items-center justify-center text-2xl mb-2 group-hover:bg-blue-500 group-hover:text-white transition">🥤</div>
                    <span class="text-xs font-bold text-gray-600 group-hover:text-blue-600">Minuman</span>
                </a>
                <a href="menu.php#cat-snack" class="flex-1 bg-white border border-gray-100 shadow-sm rounded-xl p-4 flex flex-col items-center hover:border-primary hover:shadow-md transition cursor-pointer group">
                    <div class="bg-yellow-50 w-12 h-12 rounded-full flex items-center justify-center text-2xl mb-2 group-hover:bg-yellow-500 group-hover:text-white transition">🍟</div>
                    <span class="text-xs font-bold text-gray-600 group-hover:text-yellow-600">Snack</span>
                </a>
            </div>
        </div>

        <div class="px-4 md:px-8 mt-10">
            <div class="flex justify-between items-end mb-6">
                <div>
                    <h3 class="font-bold text-gray-800 text-xl">Rekomendasi</h3>
                    <p class="text-xs text-gray-400">Pilihan terbaik untukmu</p>
                </div>
                <a href="menu.php" class="text-primary text-sm font-bold hover:underline">Lihat Semua</a>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6">
                
                <?php foreach($recommendations as $item): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition group h-full flex flex-col">
                    <div class="aspect-[4/3] w-full bg-gray-100 relative overflow-hidden">
                        <img src="<?php echo menu_image($item['image_url']); ?>" 
                             class="w-full h-full object-cover transition duration-500 group-hover:scale-110"
                             alt="<?php echo $item['name']; ?>">
                        
                        <span class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm text-[10px] font-bold px-2 py-1 rounded text-gray-600 uppercase">
                            <?php echo $item['category']; ?>
                        </span>
                    </div>

                    <div class="p-3 flex flex-col flex-1 justify-between">
                        <div>
                            <h4 class="font-bold text-gray-800 text-sm line-clamp-1 mb-1" title="<?php echo $item['name']; ?>">
                                <?php echo $item['name']; ?>
                            </h4>
                            <p class="text-[10px] text-gray-400 line-clamp-2 leading-relaxed h-8">
                                <?php echo $item['description']; ?>
                            </p>
                        </div>
                        
                        <div class="mt-3 flex justify-between items-center">
                            <span class="text-sm font-bold text-primary"><?php echo format_rupiah($item['base_price']); ?></span>
                            
                            <form action="process_cart.php" method="POST">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="menu_id" value="<?php echo $item['menu_id']; ?>">
                                <input type="hidden" name="price" value="<?php echo $item['base_price']; ?>">
                                <input type="hidden" name="name" value="<?php echo $item['name']; ?>">
                                <button type="submit" class="w-8 h-8 bg-gray-900 text-white rounded-full flex items-center justify-center hover:bg-primary transition shadow-md">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

            </div>
        </div>

    </div> </div>

<?php require_once '../layouts/footer.php'; ?>