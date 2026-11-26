<?php
$is_spa_request = isset($_SERVER['HTTP_X_SPA_REQUEST']) && $_SERVER['HTTP_X_SPA_REQUEST'] === 'true';
if (!$is_spa_request) {
    require_once PROJECT_ROOT . '/views/header.php';
}

// Security check
check_permission('coa', 'menu');
?>

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
            <i class="bi bi-journal-bookmark-fill text-blue-500"></i> Bagan Akun (Chart of Accounts)
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola struktur akun keuangan perusahaan</p>
    </div>
    <button type="button" class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-xl shadow-lg text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 hover:-translate-y-0.5" id="add-coa-btn">
        <i class="bi bi-plus-circle mr-2"></i> Tambah Akun
    </button>
</div>

<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
    <div class="p-6">
        <div id="coa-tree-container">
            <div class="text-center p-5 text-slate-500">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"></div>
                <span class="sr-only">Memuat...</span>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Tambah/Edit Akun COA -->
<div id="coaModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="coaModalLabel" role="dialog" aria-modal="true">
  <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeModal('coaModal')"></div>
    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
    <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
      <div class="bg-white dark:bg-slate-800 px-6 pt-6 pb-4 sm:p-6 sm:pb-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
        <h5 class="text-lg font-bold text-slate-800 dark:text-white" id="coaModalLabel">Tambah Akun Baru</h5>
        <button type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors" onclick="closeModal('coaModal')">
            <i class="bi bi-x-lg text-xl"></i>
        </button>
      </div>
      <div class="p-6">
        <form id="coa-form" class="space-y-5">
            <input type="hidden" name="id" id="coa-id">
            <input type="hidden" name="action" id="coa-action" value="add">
            <div>
                <label for="parent_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Akun Induk (Parent)</label>
                <select class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200" id="parent_id" name="parent_id">
                </select>
            </div>
            <div>
                <label for="kode_akun" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Kode Akun</label>
                <input type="text" class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white dark:placeholder-slate-400 transition-all duration-200" id="kode_akun" name="kode_akun" required>
            </div>
            <div>
                <label for="nama_akun" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nama Akun</label>
                <input type="text" class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white dark:placeholder-slate-400 transition-all duration-200" id="nama_akun" name="nama_akun" required>
            </div>
            <div>
                <label for="tipe_akun" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tipe Akun</label>
                <select class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200" id="tipe_akun" name="tipe_akun" required>
                    <option value="Aset">Aset</option>
                    <option value="Liabilitas">Liabilitas</option>
                    <option value="Ekuitas">Ekuitas</option>
                    <option value="Pendapatan">Pendapatan</option>
                    <option value="Beban">Beban</option>
                </select>
            </div>
            <div class="flex items-center">
                <input class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 dark:border-slate-600 rounded" type="checkbox" id="is_kas" name="is_kas" value="1">
                <label class="ml-2 block text-sm text-slate-700 dark:text-slate-300" for="is_kas">
                    Ini adalah akun Kas/Bank (bisa menerima/mengirim uang)
                </label>
            </div>
        </form>
      </div>
      <div class="bg-slate-50 dark:bg-slate-900/50 px-6 py-4 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
        <button type="button" id="save-coa-btn" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-base font-medium text-white focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-all duration-200">Simpan</button>
        <button type="button" onclick="closeModal('coaModal')" class="w-full inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-600 shadow-sm px-4 py-2.5 bg-white dark:bg-slate-800 text-base font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all duration-200">Batal</button>
      </div>
    </div>
  </div>
</div>
      <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
        <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm" id="save-coa-btn">Simpan</button>
        <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal('coaModal')">Tutup</button>
      </div>
    </div>
  </div>
</div>

<?php
if (!$is_spa_request) {
    require_once PROJECT_ROOT . '/views/footer.php';
}
?>