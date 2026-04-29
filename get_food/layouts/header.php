<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Get Food</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FF5C00',
                        secondary: '#1E293B',
                        bglight: '#F8F8F8',
                    },
                    fontFamily: { sans: ['Poppins', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Poppins', sans-serif; -webkit-tap-highlight-color: transparent; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-bglight text-slate-800 flex flex-col min-h-screen">

<?php 
// Cek Halaman (Agar Navbar tidak muncul di Login/Admin)
$is_admin = strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;
$is_auth  = strpos($_SERVER['REQUEST_URI'], '/auth/') !== false;

if (!$is_admin && !$is_auth) : 
    $current = basename($_SERVER['PHP_SELF']);
?>

    <nav class="hidden md:flex fixed top-0 w-full bg-white shadow-sm z-50 border-b border-gray-100 justify-between items-center px-10 py-4">
        
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center text-white font-bold">G</div>
            <span class="text-xl font-bold text-gray-800 tracking-tight">Get Food</span>
        </div>

        <div class="flex gap-8">
            <a href="<?php echo url('customer/home.php'); ?>" class="text-sm font-semibold hover:text-primary transition <?php echo ($current == 'home.php') ? 'text-primary' : 'text-gray-500'; ?>">Home</a>
            <a href="<?php echo url('customer/menu.php'); ?>" class="text-sm font-semibold hover:text-primary transition <?php echo ($current == 'menu.php') ? 'text-primary' : 'text-gray-500'; ?>">Menu</a>
            <a href="<?php echo url('customer/history.php'); ?>" class="text-sm font-semibold hover:text-primary transition <?php echo ($current == 'history.php') ? 'text-primary' : 'text-gray-500'; ?>">Riwayat</a>
        </div>

        <a href="<?php echo url('customer/profile.php'); ?>" class="flex items-center gap-3 hover:bg-gray-50 px-3 py-1 rounded-full transition">
            <span class="text-sm font-semibold text-gray-700"><?php echo $_SESSION['user']['name'] ?? 'Guest'; ?></span>
            <div class="w-8 h-8 rounded-full bg-orange-100 text-primary flex items-center justify-center font-bold border border-orange-200">
                <?php echo substr($_SESSION['user']['name'] ?? 'G', 0, 1); ?>
            </div>
        </a>
    </nav>

    <div class="hidden md:block h-20"></div>

<?php endif; ?>