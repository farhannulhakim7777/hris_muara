<?php
$is_spa_request = isset($_SERVER['HTTP_X_SPA_REQUEST']) && $_SERVER['HTTP_X_SPA_REQUEST'] === 'true';
if (!$is_spa_request) {
    require_once PROJECT_ROOT . '/views/header.php';
}

// Security check
check_permission('transaksi_kas', 'menu');
?>

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
            <i class="bi bi-arrow-down-up text-blue-500"></i> Transaksi Keuangan
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola arus kas dan transaksi keuangan</p>
    </div>
    <button type="button" class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-xl shadow-lg text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 hover:-translate-y-0.5" id="add-transaksi-btn">
        <i class="bi bi-plus-circle mr-2"></i> Tambah Transaksi
    </button>
</div>

<!-- Filter -->
<div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-1">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="bi bi-search text-slate-400"></i>
                </div>
                <input type="text" id="search-transaksi" class="block w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white dark:placeholder-slate-400 transition-all duration-200" placeholder="Cari keterangan...">
            </div>
        </div>
        <div>
            <select id="filter-akun-kas" class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200">
                <option value="">Semua Akun Kas/Bank</option>
            </select>
        </div>
        <div>
            <select id="filter-bulan" class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200">
            </select>
        </div>
        <div>
            <select id="filter-tahun" class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200">
            </select>
        </div>
    </div>
</div>

<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Akun</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">No. Ref</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Keterangan</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Info</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Dari/Ke Akun Kas</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="transaksi-table-body" class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                </tbody>
            </table>
        </div>
        <div class="flex justify-between items-center mt-4">
            <div id="transaksi-pagination-info" class="text-sm text-slate-700 dark:text-slate-300"></div>
            <div id="transaksi-pagination">
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Tambah/Edit Transaksi -->
<div id="transaksiModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="transaksiModalLabel" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeModal('transaksiModal')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
            <div class="bg-white dark:bg-slate-800 px-6 pt-6 pb-4 sm:p-6 sm:pb-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                <h5 class="text-lg font-bold text-slate-800 dark:text-white" id="transaksiModalLabel">Tambah Transaksi Baru</h5>
                <button type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors" onclick="closeModal('transaksiModal')"><i class="bi bi-x-lg text-xl"></i></button>
            </div>
            <div class="p-6">
                <form id="transaksi-form" novalidate class="space-y-5">
                    <input type="hidden" name="id" id="transaksi-id">
                    <input type="hidden" name="action" id="transaksi-action" value="add">
                    <input type="hidden" name="jenis" id="jenis" required>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Jenis Transaksi</label>
                            <div id="jenis-btn-group" class="grid grid-cols-3 gap-1 rounded-xl bg-slate-200 dark:bg-slate-700 p-1">
                                <button type="button" class="jenis-btn px-3 py-2.5 text-sm font-medium rounded-lg" data-value="pengeluaran"><i class="bi bi-arrow-down-circle-fill mr-2"></i>Pengeluaran</button>
                                <button type="button" class="jenis-btn px-3 py-2.5 text-sm font-medium rounded-lg" data-value="pemasukan"><i class="bi bi-arrow-up-circle-fill mr-2"></i>Pemasukan</button>
                                <button type="button" class="jenis-btn px-3 py-2.5 text-sm font-medium rounded-lg" data-value="transfer"><i class="bi bi-arrow-left-right mr-2"></i>Transfer</button>
                            </div>
                        </div>
                        <div>
                            <label for="tanggal" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tanggal</label>
                            <input type="date" class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200" id="tanggal" name="tanggal" required>
                        </div>
                    </div>

                    <div>
                        <label for="jumlah" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Jumlah (Rp)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-slate-400 text-sm">Rp</span>
                            </div>
                            <input type="number" class="block w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white dark:placeholder-slate-400 transition-all duration-200" id="jumlah" name="jumlah" placeholder="cth: 50000" required>
                        </div>
                    </div>

                    <div>
                        <label for="nomor_referensi" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nomor Referensi (Opsional)</label>
                        <input type="text" class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white dark:placeholder-slate-400 transition-all duration-200" id="nomor_referensi" name="nomor_referensi" placeholder="Kosongkan untuk nomor otomatis">
                    </div>

                    <div id="pemasukan-fields" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div><label for="kas_account_id_pemasukan" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Masuk ke Akun Kas</label><select class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200" id="kas_account_id_pemasukan" name="kas_account_id_pemasukan"></select></div>
                        <div><label for="account_id_pemasukan" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Dari Akun Pendapatan</label><select class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200" id="account_id_pemasukan" name="account_id_pemasukan"></select></div>
                    </div>
                    <div id="pengeluaran-fields" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div><label for="kas_account_id_pengeluaran" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Keluar dari Akun Kas</label><select class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200" id="kas_account_id_pengeluaran" name="kas_account_id_pengeluaran"></select></div>
                        <div><label for="account_id_pengeluaran" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Untuk Akun Beban</label><select class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200" id="account_id_pengeluaran" name="account_id_pengeluaran"></select></div>
                    </div>
                    <div id="transfer-fields" class="grid grid-cols-1 md:grid-cols-2 gap-5 hidden">
                        <div><label for="kas_account_id_transfer" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Dari Akun Kas</label><select class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200" id="kas_account_id_transfer" name="kas_account_id_transfer"></select></div>
                        <div><label for="kas_tujuan_account_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Ke Akun Kas</label><select class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200" id="kas_tujuan_account_id" name="kas_tujuan_account_id"></select></div>
                    </div>

                    <div>
                        <label for="keterangan" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Keterangan (bisa lebih dari 1 baris)</label>
                        <textarea class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white dark:placeholder-slate-400 transition-all duration-200" id="keterangan" name="keterangan" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="bg-slate-50 dark:bg-slate-900/50 px-6 py-4 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                <button type="button" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-base font-medium text-white focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-all duration-200" id="save-transaksi-btn">Simpan Transaksi</button>
                <button type="button" class="w-full inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-600 shadow-sm px-4 py-2.5 bg-white dark:bg-slate-800 text-base font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm transition-all duration-200" onclick="closeModal('transaksiModal')">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Detail Jurnal -->
<div id="jurnalDetailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="jurnalDetailModalLabel" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeModal('jurnalDetailModal')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white dark:bg-slate-800 px-6 pt-6 pb-4 sm:p-6 sm:pb-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                <h5 class="text-lg font-bold text-slate-800 dark:text-white" id="jurnalDetailModalLabel"><i class="bi bi-journal-text text-blue-500 mr-2"></i> Detail Jurnal Transaksi</h5>
                <button type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors" onclick="closeModal('jurnalDetailModal')"><i class="bi bi-x-lg text-xl"></i></button>
            </div>
            <div class="p-6" id="jurnal-detail-body">
                <div class="text-center p-4"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"></div></div>
            </div>
            <div class="bg-slate-50 dark:bg-slate-900/50 px-6 py-4 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                <button type="button" class="w-full inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-600 shadow-sm px-4 py-2.5 bg-white dark:bg-slate-800 text-base font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm transition-all duration-200" onclick="closeModal('jurnalDetailModal')">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?php
if (!$is_spa_request) {
    require_once PROJECT_ROOT . '/views/footer.php';
}
?>