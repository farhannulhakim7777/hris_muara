<?php
$is_spa_request = isset($_SERVER['HTTP_X_SPA_REQUEST']) && $_SERVER['HTTP_X_SPA_REQUEST'] === 'true';
if (!$is_spa_request) {
    require_once PROJECT_ROOT . '/views/header.php';
}
?>

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
            <i class="bi bi-puzzle-fill text-blue-500"></i> Manajemen Komponen Gaji
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola komponen pendapatan dan potongan gaji</p>
    </div>
    <button type="button" class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-xl shadow-lg text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 hover:-translate-y-0.5" onclick="openKomponenModal()">
        <i class="bi bi-plus-circle mr-2"></i> Tambah Komponen
    </button>
</div>

<!-- Table -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-900/50">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Komponen</th>
                    <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jenis</th>
                    <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tipe Hitung</th>
                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nilai Default</th>
                    <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Default</th>
                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody id="komponen-table-body" class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                        <div class="flex flex-col items-center justify-center">
                            <i class="bi bi-hourglass-split text-3xl mb-2"></i>
                            <p>Memuat data...</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Form Komponen -->
<div id="komponenModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeKomponenModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white dark:bg-slate-800 px-6 pt-6 pb-4 sm:p-6 sm:pb-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white" id="modal-title">Form Komponen Gaji</h3>
                <button type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors" onclick="closeKomponenModal()">
                    <i class="bi bi-x-lg text-xl"></i>
                </button>
            </div>
            <div class="p-6">
                <form id="komponen-form">
                    <input type="hidden" name="id" id="komponen-id">
                    <input type="hidden" name="action" value="save">
                    <div class="mb-5">
                        <label for="nama_komponen" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nama Komponen</label>
                        <input type="text" name="nama_komponen" id="nama_komponen" required class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white dark:placeholder-slate-400 transition-all duration-200" placeholder="cth: Tunjangan Transport">
                    </div>
                    <div class="mb-5">
                        <label for="jenis" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Jenis Komponen</label>
                        <select name="jenis" id="jenis" class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200">
                            <option value="pendapatan">Pendapatan (Tunjangan)</option>
                            <option value="potongan">Potongan</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="tipe_hitung" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tipe Hitung</label>
                        <select name="tipe_hitung" id="tipe_hitung" class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200">
                            <option value="bulanan">Bulanan (Tetap)</option>
                            <option value="harian">Harian (Per Kehadiran)</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="nilai_default" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nilai Default (Rp)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-slate-400 text-sm">Rp</span>
                            </div>
                            <input type="number" name="nilai_default" id="nilai_default" required min="0" class="block w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white dark:placeholder-slate-400 transition-all duration-200" placeholder="0">
                        </div>
                    </div>
                    <div class="mb-5">
                        <label for="is_default" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Perilaku Default</label>
                        <select name="is_default" id="is_default" class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200">
                            <option value="1">Tambahkan otomatis saat generate gaji</option>
                            <option value="0">Tambahkan manual jika diperlukan</option>
                        </select>
                        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Komponen default akan otomatis muncul di slip gaji setiap karyawan saat gaji di-generate.</p>
                    </div>
                </form>
            </div>
            <div class="bg-slate-50 dark:bg-slate-900/50 px-6 py-4 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                <button type="button" id="save-komponen-btn" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-base font-medium text-white focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-all duration-200">Simpan</button>
                <button type="button" onclick="closeKomponenModal()" class="w-full inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-600 shadow-sm px-4 py-2.5 bg-white dark:bg-slate-800 text-base font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all duration-200">Batal</button>
            </div>
        </div>
    </div>
</div>

<?php
if (!$is_spa_request) {
    require_once PROJECT_ROOT . '/views/footer.php';
}
?>