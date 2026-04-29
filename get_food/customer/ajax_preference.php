<?php
require_once '../config/functions.php';

// Pastikan method POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. GANTI TIPE PESANAN (Dine In / Take Away)
    if (isset($_POST['order_type'])) {
        $_SESSION['order_type'] = $_POST['order_type'];
        echo "Tipe pesanan diubah ke: " . $_SESSION['order_type'];
    }

    // 2. GANTI RESTORAN
    if (isset($_POST['restaurant_id'])) {
        $_SESSION['restaurant_id'] = $_POST['restaurant_id'];
        
        // Ambil nama restoran untuk display
        $rid = $_POST['restaurant_id'];
        $resto = query("SELECT name FROM restaurants WHERE restaurant_id = '$rid'");
        if ($resto) {
            $_SESSION['restaurant_name'] = $resto[0]['name'];
        }
        echo "Restoran diubah";
    }
}
?>