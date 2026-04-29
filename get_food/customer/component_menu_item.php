<div class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 hover:shadow-md transition flex gap-4 h-full items-center">
    
    <div class="w-24 h-24 flex-shrink-0">
        <img src="<?php echo menu_image($item['image_url']); ?>" 
             class="w-full h-full object-cover rounded-lg bg-gray-50" alt="Menu">
    </div>
    
    <div class="flex-1 flex flex-col justify-between h-full py-1">
        <div>
            <h4 class="font-bold text-gray-800 text-sm line-clamp-1" title="<?php echo $item['name']; ?>"><?php echo $item['name']; ?></h4>
            <p class="text-[10px] text-gray-400 line-clamp-2 leading-tight mt-1 mb-2 h-8 overflow-hidden"><?php echo $item['description']; ?></p>
        </div>
        
        <div class="flex justify-between items-end">
            <span class="font-bold text-gray-900 text-sm"><?php echo format_rupiah($item['base_price']); ?></span>
            
            <form action="process_cart.php" method="POST">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="menu_id" value="<?php echo $item['menu_id']; ?>">
                <input type="hidden" name="price" value="<?php echo $item['base_price']; ?>">
                <input type="hidden" name="name" value="<?php echo $item['name']; ?>">
                
                <button type="submit" class="w-8 h-8 bg-orange-100 text-primary rounded-full flex items-center justify-center hover:bg-primary hover:text-white transition shadow-sm">
                    <i class="fas fa-plus text-xs"></i>
                </button>
            </form>
        </div>
    </div>
</div>