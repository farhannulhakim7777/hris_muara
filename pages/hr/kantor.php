<?php
$is_spa_request = isset($_SERVER['HTTP_X_SPA_REQUEST']) && $_SERVER['HTTP_X_SPA_REQUEST'] === 'true';
if (!$is_spa_request) {
    require_once PROJECT_ROOT . '/views/header.php';
}
?>

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
            <i class="bi bi-building-fill text-blue-500"></i> Manajemen Kantor
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola lokasi dan informasi kantor cabang</p>
    </div>
    <button type="button" class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-xl shadow-lg text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 hover:-translate-y-0.5" onclick="openKantorModal()">
        <i class="bi bi-plus-circle mr-2"></i> Tambah Kantor
    </button>
</div>

<!-- Filter & Search -->
<div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm mb-6">
    <div class="max-w-md">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="bi bi-search text-slate-400"></i>
            </div>
            <input type="text" id="search-kantor" class="block w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white dark:placeholder-slate-400 transition-all duration-200" placeholder="Cari Nama Kantor...">
        </div>
    </div>
</div>

<!-- Table -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-900/50">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Kantor</th>
                    <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jenis</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Alamat</th>
                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody id="kantor-table-body" class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
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

<!-- Modal Form Kantor -->
<div id="kantorModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeKantorModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white dark:bg-slate-800 px-6 pt-6 pb-4 sm:p-6 sm:pb-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white" id="modal-title">Form Kantor</h3>
                <button type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors" onclick="closeKantorModal()">
                    <i class="bi bi-x-lg text-xl"></i>
                </button>
            </div>
            <div class="p-6">
                <form id="kantor-form">
                    <input type="hidden" name="id" id="kantor-id">
                    <input type="hidden" name="action" value="save">
                    <div class="mb-5">
                        <label for="nama_kantor" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nama Kantor</label>
                        <input type="text" name="nama_kantor" id="nama_kantor" required class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white dark:placeholder-slate-400 transition-all duration-200">
                    </div>
                    <div class="mb-5">
                        <label for="jenis_kantor" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Jenis Kantor</label>
                        <select name="jenis_kantor" id="jenis_kantor" class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200">
                            <option value="pusat">Kantor Pusat</option>
                            <option value="cabang">Kantor Cabang</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label for="alamat" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Alamat</label>
                        <textarea name="alamat" id="alamat" rows="3" class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white dark:placeholder-slate-400 transition-all duration-200"></textarea>
                    </div>
                </form>
            </div>
            <div class="bg-slate-50 dark:bg-slate-900/50 px-6 py-4 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                <button type="button" id="save-kantor-btn" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-base font-medium text-white focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-all duration-200">Simpan</button>
                <button type="button" onclick="closeKantorModal()" class="w-full inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-600 shadow-sm px-4 py-2.5 bg-white dark:bg-slate-800 text-base font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all duration-200">Batal</button>
            </div>
        </div>
    </div>
</div>
            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">Form Kantor</h3>
                <button type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none" onclick="closeKantorModal()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="p-6">
                <form id="kantor-form">
                    <input type="hidden" name="id" id="kantor-id">
                    <input type="hidden" name="action" value="save">
                    
                    <div class="mb-4">
                        <label for="nama_kantor" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Kantor</label>
                        <input type="text" name="nama_kantor" id="nama_kantor" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                    </div>
                    <div class="mb-4">
                        <label for="jenis_kantor" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Kantor</label>
                        <select name="jenis_kantor" id="jenis_kantor" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                            <option value="Pusat">Pusat</option>
                            <option value="Cabang">Cabang</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="alamat" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat</label>
                        <textarea name="alamat" id="alamat" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary focus:ring-primary sm:text-sm"></textarea>
                    </div>
                </form>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="save-kantor-btn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-600 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Simpan</button>
                <button type="button" onclick="closeKantorModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
            </div>
        </div>
    </div>
</div>

<?php
if (!$is_spa_request) {
    require_once PROJECT_ROOT . '/views/footer.php';
}
?>