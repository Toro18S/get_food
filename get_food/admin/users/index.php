<?php
require_once '../../config/functions.php';

// Cek akses admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    redirect('auth/login.php');
}

// --- LOGIKA TAMBAH PEGAWAI ---
if (isset($_POST['submit_user'])) {
    $name     = htmlspecialchars($_POST['name']);
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password']; 
    $role     = $_POST['role'];
    $resto_id = $_POST['restaurant_id'];

    $cek = query("SELECT * FROM employees WHERE username = '$username'");
    if (!empty($cek)) {
        set_flash_message('error', 'Username sudah dipakai!');
    } else {
        $query = "INSERT INTO employees (name, username, password, role, restaurant_id) 
                  VALUES ('$name', '$username', '$password', '$role', '$resto_id')";
        
        if (query($query)) {
            set_flash_message('success', 'Pegawai baru berhasil ditambahkan!');
            redirect('admin/users/index.php');
        } else {
            set_flash_message('error', 'Gagal menambah pegawai.');
        }
    }
}

$employees = query("SELECT e.*, r.name as resto_name 
                    FROM employees e 
                    LEFT JOIN restaurants r ON e.restaurant_id = r.restaurant_id 
                    ORDER BY e.role ASC, e.name ASC");

$restaurants = query("SELECT * FROM restaurants");

require_once '../../layouts/header.php';
?>

<div class="flex bg-gray-100 min-h-screen">
    <?php require_once '../../layouts/sidebar_admin.php'; ?>

    <main class="flex-1 ml-64 p-8 relative">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">👥 Data Pegawai</h1>
            
            <button type="button" onclick="toggleModal('modalUser')" class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg font-semibold shadow transition flex items-center gap-2">
                <i class="fas fa-user-plus"></i> Tambah Pegawai
            </button>
        </div>

        <?php show_flash_message(); ?>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Nama</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Username</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Role</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600">Cabang</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($employees as $user) : ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-bold text-gray-800">
                            <?php echo $user['name']; ?>
                            
                            <?php if(isset($_SESSION['user']['id']) && $user['employee_id'] == $_SESSION['user']['id']) echo '<span class="text-xs bg-green-100 text-green-600 px-2 py-0.5 rounded ml-2">You</span>'; ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?php echo $user['username']; ?></td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase 
                                <?php 
                                    if($user['role'] == 'admin') echo 'bg-purple-100 text-purple-700';
                                    elseif($user['role'] == 'koki') echo 'bg-blue-100 text-blue-700';
                                    elseif($user['role'] == 'kasir') echo 'bg-green-100 text-green-700';
                                    else echo 'bg-gray-100 text-gray-700';
                                ?>">
                                <?php echo $user['role']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <i class="fas fa-map-marker-alt text-orange-400 mr-1"></i>
                            <?php echo $user['resto_name'] ?? 'Semua Cabang'; ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-3">
                                <a href="edit.php?id=<?php echo $user['employee_id']; ?>" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                                
                                <?php if(isset($_SESSION['user']['id']) && $user['employee_id'] != $_SESSION['user']['id']): ?>
                                <a href="delete.php?id=<?php echo $user['employee_id']; ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Hapus pegawai ini?');"><i class="fas fa-trash"></i></a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<div id="modalUser" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50 transition-opacity" onclick="toggleModal('modalUser')"></div>
    <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full mx-auto mt-20 p-6 animate-fade-in-down">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Tambah Pegawai Baru</h3>
            <button type="button" onclick="toggleModal('modalUser')" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
        
        <form action="" method="POST">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" class="w-full px-4 py-2 border rounded-lg focus:border-orange-500 focus:outline-none" required>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Username</label>
                    <input type="text" name="username" class="w-full px-4 py-2 border rounded-lg focus:border-orange-500 focus:outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:border-orange-500 focus:outline-none" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Posisi / Role</label>
                <select name="role" class="w-full px-4 py-2 border rounded-lg focus:border-orange-500 focus:outline-none">
                    <option value="pelayan">Pelayan (Waiter)</option>
                    <option value="koki">Koki (Chef)</option>
                    <option value="kasir">Kasir</option>
                    <option value="admin">Admin / Manager</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Penempatan Cabang</label>
                <select name="restaurant_id" class="w-full px-4 py-2 border rounded-lg focus:border-orange-500 focus:outline-none">
                    <?php foreach ($restaurants as $resto) : ?>
                        <option value="<?php echo $resto['restaurant_id']; ?>"><?php echo $resto['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" onclick="toggleModal('modalUser')" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                <button type="submit" name="submit_user" class="px-4 py-2 text-white bg-orange-500 rounded-lg hover:bg-orange-600 font-bold">Simpan</button>
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