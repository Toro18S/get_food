<?php
require_once '../config/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // --- FITUR 1: TAMBAH KE KERANJANG (Sama seperti sebelumnya) ---
    if ($_POST['action'] == 'add') {
        $id = $_POST['menu_id'];
        $name = $_POST['name'];
        $price = $_POST['price'];

        if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty'] += 1;
            $_SESSION['cart'][$id]['subtotal'] = $_SESSION['cart'][$id]['qty'] * $price;
        } else {
            $_SESSION['cart'][$id] = ['menu_id' => $id, 'name' => $name, 'price' => $price, 'qty' => 1, 'subtotal' => $price];
        }
        redirect('customer/menu.php');
    }

    // --- FITUR 2: CHECKOUT DENGAN LOGIKA MEJA PINTAR ---
    elseif ($_POST['action'] == 'checkout') {
        
        if (empty($_SESSION['cart'])) { redirect('customer/menu.php'); }

        $cust_name  = $_SESSION['user']['name'];
        $cust_phone = $_SESSION['user']['phone'];
        $resto_id   = $_SESSION['restaurant_id'] ?? 1;
        $total_amount = $_POST['total_amount'];
        $pax        = $_POST['pax']; // Jumlah Orang
        $time       = $_POST['reservation_time']; // Jam Booking (Contoh: 2025-12-01 14:00:00)

        // --- ALGORITMA PENCARIAN MEJA ---
        
        // 1. Cari Meja di Resto ini yang Kapasitasnya CUKUP
        // Urutkan dari kapasitas terkecil yang muat (biar hemat meja besar)
        $potential_tables = query("SELECT * FROM restaurant_tables 
                                   WHERE restaurant_id = '$resto_id' 
                                   AND capacity >= '$pax' 
                                   ORDER BY capacity ASC");

        // 1. Cari Meja
        $potential_tables = query("SELECT * FROM restaurant_tables 
                                   WHERE restaurant_id = '$resto_id' 
                                   AND capacity >= '$pax' 
                                   ORDER BY capacity ASC");

        if (empty($potential_tables)) {
            // Ubah flash message biasa jadi SweetAlert session
            $_SESSION['swal'] = [
                'type' => 'error',
                'title' => 'Meja Tidak Cukup',
                'text' => "Mohon maaf, tidak ada meja untuk $pax orang di cabang ini."
            ];
            redirect('customer/cart.php');
            exit;
        }

        $assigned_table_name = null;

        // 2. Cek Bentrok Jadwal
        foreach ($potential_tables as $table) {
            $table_name = $table['table_name'];
            
            $check_clash = query("SELECT * FROM orders 
                                  WHERE restaurant_id = '$resto_id' 
                                  AND table_number = '$table_name' 
                                  AND status IN ('pending', 'cooking', 'served')
                                  AND reservation_time BETWEEN DATE_SUB('$time', INTERVAL 2 HOUR) AND DATE_ADD('$time', INTERVAL 2 HOUR)");
            
            if (empty($check_clash)) {
                $assigned_table_name = $table_name;
                break; 
            }
        }

        // 3. Jika Penuh Semua
        if ($assigned_table_name == null) {
            $_SESSION['swal'] = [
                'type' => 'error',
                'title' => 'Jam Penuh',
                'text' => "Semua meja penuh pada jam tersebut. Silakan pilih jam lain."
            ];
            redirect('customer/cart.php');
            exit;
        }

        $assigned_table_name = null;

        // 2. Cek Bentrok Jadwal (Looping setiap meja yang muat)
        // Kita asumsikan 1 sesi makan = 2 JAM
        foreach ($potential_tables as $table) {
            $table_name = $table['table_name'];
            
            // Cek apakah meja ini SUDAH DIPESAN di jam yang bentrok?
            // Bentrok = Pesanan orang lain ada di range (Request Time - 2 jam) s/d (Request Time + 2 jam)
            // Kita juga cek status: hanya yang 'pending', 'cooking', 'served'. Kalau 'completed' berarti meja udh kosong.
            $check_clash = query("SELECT * FROM orders 
                                  WHERE restaurant_id = '$resto_id' 
                                  AND table_number = '$table_name' 
                                  AND status IN ('pending', 'cooking', 'served')
                                  AND reservation_time BETWEEN DATE_SUB('$time', INTERVAL 2 HOUR) AND DATE_ADD('$time', INTERVAL 2 HOUR)");
            
            if (empty($check_clash)) {
                // HORE! Meja ini kosong di jam segitu
                $assigned_table_name = $table_name;
                break; // Stop looping, kita sudah dapat meja
            }
        }

        // 3. Keputusan Akhir
        if ($assigned_table_name == null) {
            // Semua meja penuh di jam itu
            set_flash_message('error', "Mohon maaf, semua meja penuh pada jam tersebut. Silakan pilih jam lain.");
            redirect('customer/cart.php');
            exit;
        }

        // --- SIMPAN ORDER ---
        $query_order = "INSERT INTO orders (restaurant_id, customer_name, customer_phone, table_number, number_of_people, reservation_time, total_amount, status, order_type) 
                        VALUES ('$resto_id', '$cust_name', '$cust_phone', '$assigned_table_name', '$pax', '$time', '$total_amount', 'pending', 'dine_in')";
        
        if (query($query_order)) {
            $order_id = mysqli_insert_id($conn);

            foreach ($_SESSION['cart'] as $item) {
                $mid = $item['menu_id'];
                $qty = $item['qty'];
                $sub = $item['subtotal'];
                query("INSERT INTO order_items (order_id, menu_id, quantity, subtotal) VALUES ('$order_id', '$mid', '$qty', '$sub')");
            }

            unset($_SESSION['cart']);
            set_flash_message('success', "Reservasi Berhasil! Kami telah mengunci <b>$assigned_table_name</b> untuk Anda.");
            redirect('customer/history.php');

        } else {
            set_flash_message('error', 'Gagal membuat reservasi.');
            redirect('customer/cart.php');
        }
    }

    // --- FITUR 3: HAPUS ITEM ---
    elseif ($_POST['action'] == 'remove') {
        $id = $_POST['menu_id'];
        unset($_SESSION['cart'][$id]);
        redirect('customer/cart.php');
    }
}
?>