<?php
// Cek apakah ini permintaan dari SPA via AJAX
$is_spa_request = isset($_SERVER['HTTP_X_SPA_REQUEST']) && $_SERVER['HTTP_X_SPA_REQUEST'] === 'true';

// Hanya muat header jika ini bukan permintaan SPA
if (!$is_spa_request) {
    require_once PROJECT_ROOT . '/views/header.php';
}

?>

                <!-- Dashboard Header & Filters -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
                            <i class="bi bi-speedometer2 text-blue-500"></i> Dashboard
                        </h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Overview keuangan dan operasional perusahaan</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <button type="button" id="open-customize-modal-btn" class="px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 text-sm font-medium transition-all duration-200 shadow-sm hover:shadow">
                            <i class="bi bi-grid-3x3-gap-fill mr-2"></i> Kustomisasi
                        </button>
                        <div class="flex items-center gap-2">
                            <select id="dashboard-bulan-filter" class="form-select block w-full pl-4 pr-10 py-2.5 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200">
                                <!-- Options populated by JS -->
                            </select>
                            <select id="dashboard-tahun-filter" class="form-select block w-full pl-4 pr-10 py-2.5 text-sm bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200">
                                <!-- Options populated by JS -->
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Enterprise KPI Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Pendapatan -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-lg transition-all duration-300 group">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                                        <i class="bi bi-graph-up-arrow text-emerald-600 dark:text-emerald-400 text-lg"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">Pendapatan</p>
                                </div>
                                <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1 tracking-tight" id="stat-income">Rp 0</h3>
                            </div>
                        </div>
                    </div>
                    <!-- Pengeluaran -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-lg transition-all duration-300 group">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center">
                                        <i class="bi bi-graph-down-arrow text-rose-600 dark:text-rose-400 text-lg"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">Pengeluaran</p>
                                </div>
                                <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1 tracking-tight" id="stat-expense">Rp 0</h3>
                            </div>
                        </div>
                    </div>
                    <!-- Laba Bersih -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-lg transition-all duration-300 group">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                        <i class="bi bi-wallet2 text-blue-600 dark:text-blue-400 text-lg"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">Laba Bersih</p>
                                </div>
                                <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1 tracking-tight" id="stat-profit">Rp 0</h3>
                            </div>
                        </div>
                    </div>
                    <!-- Saldo Kas -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-lg transition-all duration-300 group">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="w-10 h-10 rounded-xl bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center">
                                        <i class="bi bi-cash-stack text-violet-600 dark:text-violet-400 text-lg"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">Saldo Kas</p>
                                </div>
                                <h3 class="text-2xl font-bold text-slate-800 dark:text-white mt-1 tracking-tight" id="stat-cash">Rp 0</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
                    <a href="<?= base_url('/transaksi') ?>" class="block bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 text-center group">
                        <div class="w-14 h-14 mx-auto bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 transition-transform">
                            <i class="bi bi-plus-circle-fill text-white text-2xl"></i>
                        </div>
                        <h6 class="font-semibold text-slate-700 dark:text-slate-200 text-sm">Tambah Transaksi</h6>
                    </a>
                    
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="<?= base_url('/entri-jurnal') ?>" class="block bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 text-center group">
                        <div class="w-14 h-14 mx-auto bg-gradient-to-br from-amber-400 to-amber-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 transition-transform">
                            <i class="bi bi-journal-plus text-white text-2xl"></i>
                        </div>
                        <h6 class="font-semibold text-slate-700 dark:text-slate-200 text-sm">Buat Jurnal Umum</h6>
                    </a>
                    <?php endif; ?>

                    <a href="<?= base_url('/konsinyasi') ?>" class="block bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 text-center group">
                        <div class="w-14 h-14 mx-auto bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 transition-transform">
                            <i class="bi bi-box-seam text-white text-2xl"></i>
                        </div>
                        <h6 class="font-semibold text-slate-700 dark:text-slate-200 text-sm">Konsinyasi</h6>
                    </a>

                    <a href="<?= base_url('/laporan') ?>" class="block bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 text-center group">
                        <div class="w-14 h-14 mx-auto bg-gradient-to-br from-cyan-400 to-cyan-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 transition-transform">
                            <i class="bi bi-bar-chart-line-fill text-white text-2xl"></i>
                        </div>
                        <h6 class="font-semibold text-slate-700 dark:text-slate-200 text-sm">Lihat Laporan</h6>
                    </a>

                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="<?= base_url('/coa') ?>" class="block bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 text-center group">
                        <div class="w-14 h-14 mx-auto bg-gradient-to-br from-slate-400 to-slate-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 transition-transform">
                            <i class="bi bi-journal-bookmark-fill text-white text-2xl"></i>
                        </div>
                        <h6 class="font-semibold text-slate-700 dark:text-slate-200 text-sm">Bagan Akun (COA)</h6>
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Cash Flow Trend Chart -->
                    <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm" id="widget-profit_loss_trend">
                        <div class="flex justify-between items-center mb-6">
                            <h5 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                <i class="bi bi-graph-up text-blue-500"></i> Tren Arus Kas
                            </h5>
                            <span class="text-xs text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-700 px-3 py-1 rounded-full">Tahun Ini</span>
                        </div>
                        <div class="relative h-80 w-full">
                            <canvas id="dashboard-trend-chart"></canvas>
                        </div>
                    </div>
                    <!-- Expense Category Chart -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm" id="widget-expense_category">
                        <div class="flex justify-between items-center mb-6">
                            <h5 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                <i class="bi bi-pie-chart text-emerald-500"></i> Kategori Pengeluaran
                            </h5>
                        </div>
                        <div class="relative h-80 w-full flex justify-center">
                            <canvas id="dashboard-expense-chart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Profit Growth & Balance Status -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Profit Growth Chart -->
                    <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm" id="widget-profit_growth">
                        <div class="flex justify-between items-center mb-6">
                            <h5 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                <i class="bi bi-trending-up text-blue-500"></i> Pertumbuhan Laba
                            </h5>
                            <span class="text-xs text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-700 px-3 py-1 rounded-full">Tahun Ini</span>
                        </div>
                        <div class="relative h-80 w-full">
                            <canvas id="dashboard-profit-growth-chart"></canvas>
                        </div>
                    </div>
                    <!-- Balance Status -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm" id="widget-balance_status">
                        <div class="flex justify-between items-center mb-6">
                            <h5 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                <i class="bi bi-balance-scale text-violet-500"></i> Status Neraca
                            </h5>
                        </div>
                        <div id="balance-status-content" class="flex items-center justify-center text-center h-full min-h-[200px]">
                            <!-- Content will be populated by JS -->
                        </div>
                    </div>
                </div>

                <!-- Row 3: Inventory Growth -->
                <div class="grid grid-cols-1 gap-6 mb-8" id="widget-inventory_growth">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm">
                        <div class="flex justify-between items-center mb-6">
                            <h5 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                <i class="bi bi-box-seam text-orange-500"></i> Pertumbuhan Persediaan
                            </h5>
                            <span class="text-xs text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-700 px-3 py-1 rounded-full">Nilai Aset</span>
                        </div>
                        <div class="relative h-80 w-full">
                            <canvas id="dashboard-inventory-growth-chart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="grid grid-cols-1 gap-6 mb-8">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm" id="widget-recent_transactions">
                        <div class="flex justify-between items-center mb-6">
                            <h5 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                <i class="bi bi-clock-history text-blue-500"></i> Transaksi Terbaru
                            </h5>
                            <a href="<?= base_url('/transaksi') ?>" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Lihat Semua</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <tbody id="dashboard-recent-transactions">
                                    <!-- Populated by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            

<!-- Modal Kustomisasi Dashboard -->
<div id="customizeDashboardModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true" data-modal-close="customizeDashboardModal"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white dark:bg-slate-800 px-6 pt-6 pb-4 sm:p-6 sm:pb-4 border-b border-slate-200 dark:border-slate-700">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">
                        Kustomisasi Widget Dashboard
                    </h3>
                    <button type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors" data-modal-close="customizeDashboardModal">
                        <i class="bi bi-x-lg text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                    Pilih widget yang ingin Anda tampilkan di halaman Dashboard.
                </p>
                <form id="dashboard-widgets-form" class="space-y-3">
                    <!-- Checkbox akan diisi oleh JavaScript -->
                </form>
            </div>
            <div class="bg-slate-50 dark:bg-slate-900/50 px-6 py-4 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                <button type="button" id="save-dashboard-widgets-btn" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-base font-medium text-white focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-all duration-200">
                    Simpan Perubahan
                </button>
                <button type="button" class="w-full inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-600 shadow-sm px-4 py-2.5 bg-white dark:bg-slate-800 text-base font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm transition-all duration-200" data-modal-close="customizeDashboardModal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<?php
// Hanya muat footer jika ini bukan permintaan SPA
if (!$is_spa_request) {
    require_once PROJECT_ROOT . '/views/footer.php';
}
?>