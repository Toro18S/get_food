<?php
require_once '../config/functions.php';

// 1. Cek Login Customer
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'customer') {
    redirect('auth/login.php');
}

// 2. Cek Apakah Keranjang Kosong?
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    set_flash_message('warning', 'Keranjang masih kosong. Pilih menu dulu yuk!');
    redirect('customer/menu.php');
}

require_once '../layouts/header.php';
?>

<div class="bg-white min-h-screen pb-32">
    <div class="bg-white p-4 shadow-sm sticky top-0 z-20 flex items-center gap-3 border-b border-gray-100">
        <a href="menu.php" class="text-gray-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100"><i class="fas fa-arrow-left"></i></a>
        <h1 class="font-bold text-lg">Konfirmasi Reservasi</h1>
    </div>

    <div class="p-4">
        
        <div class="bg-white p-5 rounded-xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] border border-gray-100 mb-6">
            <h3 class="font-bold text-gray-800 mb-4 border-b pb-2 flex items-center gap-2 text-sm">
                <i class="fas fa-calendar-check text-orange-500"></i> Detail Reservasi
            </h3>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Jumlah Tamu</label>
                    <div class="flex items-center bg-gray-50 rounded-lg px-3 py-2 border border-gray-200 focus-within:border-orange-500 focus-within:ring-1 focus-within:ring-orange-500 transition">
                        <i class="fas fa-users text-gray-400 mr-2 text-xs"></i>
                        <input type="number" form="form-checkout" name="pax" min="1" value="1" class="bg-transparent w-full outline-none font-bold text-gray-800 text-sm" required>
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Jam Datang</label>
                    <input type="datetime-local" form="form-checkout" name="reservation_time" 
                           class="w-full bg-gray-50 rounded-lg px-3 py-2 border border-gray-200 outline-none text-xs font-bold text-gray-800 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition" 
                           value="<?php echo date('Y-m-d\TH:i', strtotime('+1 hour')); ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Atas Nama</label>
                <input type="text" value="<?php echo $_SESSION['user']['name']; ?>" class="w-full bg-gray-100 rounded-lg px-3 py-2 text-sm font-semibold text-gray-600 cursor-not-allowed border border-transparent" readonly>
            </div>
            
            <div>
                <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Nomor Kontak</label>
                <input type="text" value="<?php echo $_SESSION['user']['phone']; ?>" class="w-full bg-gray-100 rounded-lg px-3 py-2 text-sm font-semibold text-gray-600 cursor-not-allowed border border-transparent" readonly>
            </div>
        </div>

        <h3 class="font-bold text-gray-800 text-sm mb-3 ml-1">Menu yang Dipesan</h3>
        <div class="space-y-3 bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
            <?php 
            $grand_total = 0;
            foreach($_SESSION['cart'] as $id => $item): 
                $grand_total += $item['subtotal'];
            ?>
            <div class="flex justify-between items-center border-b border-dashed border-gray-100 last:border-0 pb-3 last:pb-0">
                <div class="flex gap-3 items-center">
                    <div class="bg-orange-50 text-orange-600 font-bold text-xs w-8 h-8 rounded-lg flex items-center justify-center">
                        <?php echo $item['qty']; ?>x
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-700 text-sm"><?php echo $item['name']; ?></h4>
                        <p class="text-xs text-gray-400"><?php echo format_rupiah($item['price']); ?></p>
                    </div>
                </div>
                
                <form action="process_cart.php" method="POST">
                    <input type="hidden" name="action" value="remove">
                    <input type="hidden" name="menu_id" value="<?php echo $id; ?>">
                    <button type="submit" class="text-gray-300 hover:text-red-500 transition"><i class="fas fa-trash-alt"></i></button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="fixed bottom-0 left-0 w-full bg-white border-t border-gray-100 p-4 shadow-[0_-4px_20px_rgba(0,0,0,0.05)] z-50 rounded-t-2xl">
        <form action="process_cart.php" method="POST" id="form-checkout">
            <input type="hidden" name="action" value="checkout">
            <input type="hidden" name="total_amount" value="<?php echo $grand_total; ?>">
            
            <div class="flex justify-between items-center mb-4 px-2">
                <div class="flex flex-col">
                    <span class="text-xs text-gray-500">Total Estimasi</span>
                    <span class="text-xl font-bold text-gray-900"><?php echo format_rupiah($grand_total); ?></span>
                </div>
                <button type="submit" class="bg-gray-900 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:bg-gray-800 active:scale-95 transition flex items-center gap-2">
                    Konfirmasi <i class="fas fa-arrow-right text-xs"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<?php if (isset($_SESSION['swal'])): ?>
<script>
    Swal.fire({
        icon: '<?php echo $_SESSION['swal']['type']; ?>',
        title: '<?php echo $_SESSION['swal']['title']; ?>',
        text: '<?php echo $_SESSION['swal']['text']; ?>',
        confirmButtonColor: '#F97316'
    });
</script>
<?php unset($_SESSION['swal']); // Hapus session agar tidak muncul terus ?>
<?php endif; ?>

<?php require_once '../layouts/footer.php'; ?>