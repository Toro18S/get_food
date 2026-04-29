<?php
// ... (Bagian PHP atas tidak berubah) ...
$restaurants = query("SELECT * FROM restaurants");
if (!isset($_SESSION['restaurant_id'])) {
    if (!empty($restaurants)) {
        $_SESSION['restaurant_id'] = $restaurants[0]['restaurant_id'];
        $_SESSION['restaurant_name'] = $restaurants[0]['name'];
    }
}
$current_resto = $_SESSION['restaurant_id'];
?>

<header class="bg-white sticky top-0 z-40 shadow-sm border-b border-gray-100 md:top-20 transition-all">
    
    <div class="w-full px-4 md:px-10 py-4 bg-white flex justify-between items-center">
        
        <div class="flex-1">
            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold mb-1">Lokasi Reservasi</p>
            
            <div class="relative group w-full max-w-[250px]">
                <form id="form-resto">
                    <select name="restaurant_id" onchange="changeResto(this)" 
                            class="appearance-none bg-transparent font-bold text-gray-800 text-lg w-full pr-6 focus:outline-none cursor-pointer truncate">
                        <?php foreach ($restaurants as $resto) : ?>
                            <option value="<?php echo $resto['restaurant_id']; ?>" <?php echo ($current_resto == $resto['restaurant_id']) ? 'selected' : ''; ?>>
                                📍 <?php echo $resto['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center text-orange-500">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </form>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="hidden md:flex gap-3 text-xs text-gray-500 mr-4 border-r pr-4">
                <span><i class="fas fa-clock text-green-500 mr-1"></i> 10:00 - 22:00</span>
                <span><i class="fas fa-parking text-blue-500 mr-1"></i> Parkir Luas</span>
                <span><i class="fas fa-wifi text-orange-500 mr-1"></i> Free Wifi</span>
            </div>

            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-bold text-gray-700"><?php echo $_SESSION['user']['name']; ?></p>
                    <p class="text-[10px] text-gray-400">Member</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold border-2 border-white shadow-sm">
                    <?php echo substr($_SESSION['user']['name'], 0, 1); ?>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    function changeResto(selectObject) {
        const id = selectObject.value;
        const formData = new FormData();
        formData.append('restaurant_id', id);

        fetch('<?php echo url('customer/ajax_preference.php'); ?>', {
            method: 'POST',
            body: formData
        }).then(response => {
            window.location.reload();
        });
    }
</script>