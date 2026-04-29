<?php
require_once '../config/functions.php';

// Cek Login Admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    redirect('auth/login.php');
}

// --- 1. HITUNG OMZET KEUANGAN ---

// A. Omzet Hari Ini
$q_harian = query("SELECT SUM(total_amount) as total FROM orders WHERE status = 'completed' AND DATE(created_at) = CURDATE()");
$omzet_harian = $q_harian[0]['total'] ?? 0;

// B. Omzet Minggu Ini
$q_mingguan = query("SELECT SUM(total_amount) as total FROM orders WHERE status = 'completed' AND YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)");
$omzet_mingguan = $q_mingguan[0]['total'] ?? 0;

// C. Omzet Bulan Ini
$q_bulanan = query("SELECT SUM(total_amount) as total FROM orders WHERE status = 'completed' AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
$omzet_bulanan = $q_bulanan[0]['total'] ?? 0;


// --- 2. DATA GRAFIK PENGUNJUNG (7 HARI TERAKHIR) ---
$chart_query = query("SELECT DATE(created_at) as tanggal, COUNT(*) as jumlah 
                      FROM orders 
                      WHERE created_at >= DATE(NOW()) - INTERVAL 6 DAY 
                      GROUP BY DATE(created_at) 
                      ORDER BY tanggal ASC");

// Siapkan array untuk Chart.js & Tampilan Detail
$labels = [];
$data_visitor = [];
$detail_harian = []; // Array bantu untuk menampilkan kotak detail

// Inisialisasi 7 hari terakhir (biar kalau 0 tetap muncul tanggalnya)
for ($i = 6; $i >= 0; $i--) {
    $tgl = date('Y-m-d', strtotime("-$i days"));
    $found = false;
    $count = 0;

    foreach ($chart_query as $row) {
        if ($row['tanggal'] == $tgl) {
            $count = $row['jumlah'];
            $found = true;
            break;
        }
    }

    $labels[] = date('D, d M', strtotime($tgl)); 
    $data_visitor[] = $count;
    
    // Simpan data lengkap untuk ditampilkan di bawah grafik
    $detail_harian[] = [
        'hari' => date('D', strtotime($tgl)),
        'tgl' => date('d M', strtotime($tgl)),
        'jumlah' => $count
    ];
}

// Konversi ke JSON
$json_labels = json_encode($labels);
$json_data   = json_encode($data_visitor);

require_once '../layouts/header.php';
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="flex bg-gray-100 min-h-screen">
    <?php require_once '../layouts/sidebar_admin.php'; ?>

    <main class="flex-1 ml-64 p-8">
        
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Dashboard & Laporan</h1>
            <p class="text-gray-500">Ringkasan performa restoran minggu ini.</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-6">
            
            <div class="w-full lg:w-2/3">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 h-full flex flex-col justify-between">
                    
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-gray-700 text-lg">📈 Tren Pengunjung (7 Hari)</h3>
                        <span class="text-[10px] bg-orange-50 text-orange-600 px-2 py-1 rounded-md font-bold uppercase tracking-wide">Live Data</span>
                    </div>
                    
                    <div class="relative h-64 w-full mb-6">
                        <canvas id="visitorChart"></canvas>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <h4 class="text-xs font-bold text-gray-400 uppercase mb-3">Rincian Harian</h4>
                        <div class="grid grid-cols-4 md:grid-cols-7 gap-2">
                            <?php foreach ($detail_harian as $harian) : ?>
                                <div class="flex flex-col items-center bg-gray-50 p-2 rounded-lg border border-gray-100">
                                    <span class="text-[10px] text-gray-400 font-semibold"><?php echo $harian['tgl']; ?></span>
                                    <span class="text-lg font-bold text-gray-800 my-1"><?php echo $harian['jumlah']; ?></span>
                                    <span class="text-[10px] text-orange-500 bg-orange-50 px-1 rounded"><?php echo $harian['hari']; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>
            </div>

            <div class="w-full lg:w-1/3 flex flex-col gap-4">
                
                <div class="bg-blue-600 text-white p-6 rounded-xl shadow-lg relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 bg-white opacity-10 w-32 h-32 rounded-full group-hover:scale-125 transition"></div>
                    <p class="text-blue-100 text-xs font-bold uppercase tracking-wider mb-1">Pemasukan Hari Ini</p>
                    <h2 class="text-3xl font-bold"><?php echo format_rupiah($omzet_harian); ?></h2>
                    <div class="mt-4 text-[10px] bg-blue-700 w-fit px-2 py-1 rounded flex items-center gap-1">
                        <i class="fas fa-calendar-day"></i> <?php echo date('d F Y'); ?>
                    </div>
                </div>

                <div class="bg-orange-500 text-white p-6 rounded-xl shadow-lg relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 bg-white opacity-10 w-32 h-32 rounded-full group-hover:scale-125 transition"></div>
                    <p class="text-orange-100 text-xs font-bold uppercase tracking-wider mb-1">Pemasukan Minggu Ini</p>
                    <h2 class="text-3xl font-bold"><?php echo format_rupiah($omzet_mingguan); ?></h2>
                    <div class="mt-4 text-[10px] bg-orange-600 w-fit px-2 py-1 rounded flex items-center gap-1">
                        <i class="fas fa-calendar-week"></i> Senin - Minggu
                    </div>
                </div>

                <div class="bg-emerald-600 text-white p-6 rounded-xl shadow-lg relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 bg-white opacity-10 w-32 h-32 rounded-full group-hover:scale-125 transition"></div>
                    <p class="text-emerald-100 text-xs font-bold uppercase tracking-wider mb-1">Pemasukan Bulan Ini</p>
                    <h2 class="text-3xl font-bold"><?php echo format_rupiah($omzet_bulanan); ?></h2>
                    <div class="mt-4 text-[10px] bg-emerald-700 w-fit px-2 py-1 rounded flex items-center gap-1">
                        <i class="fas fa-calendar-alt"></i> Bulan <?php echo date('F'); ?>
                    </div>
                </div>

            </div>
        </div>

    </main>
</div>

<script>
    const ctx = document.getElementById('visitorChart').getContext('2d');
    const labels = <?php echo $json_labels; ?>;
    const dataVisitor = <?php echo $json_data; ?>;

    // Buat Gradient Warna Orange
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(249, 115, 22, 0.2)'); // Orange transparan atas
    gradient.addColorStop(1, 'rgba(249, 115, 22, 0)');   // Putih transparan bawah

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pesanan',
                data: dataVisitor,
                borderColor: '#F97316',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#F97316',
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4 // Garis melengkung halus
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { borderDash: [4, 4], color: '#f3f4f6' },
                    ticks: { stepSize: 1 } // Pastikan angka bulat (orang)
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
</script>
</body>
</html>