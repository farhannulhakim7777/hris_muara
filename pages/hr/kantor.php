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
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola informasi lokasi, koordinat GPS, dan konfigurasi radius absensi kantor</p>
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
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Koordinat (Lat, Long)</th>
                    <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Radius</th>
                    <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Geofence</th>
                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody id="kantor-table-body" class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
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
        <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <div class="bg-white dark:bg-slate-800 px-6 pt-6 pb-4 sm:p-6 sm:pb-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2" id="modal-title">
                    <i class="bi bi-geo-alt-fill text-blue-500"></i> Form Kantor
                </h3>
                <button type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors" onclick="closeKantorModal()">
                    <i class="bi bi-x-lg text-xl"></i>
                </button>
            </div>
            <div class="p-6 max-h-[80vh] overflow-y-auto">
                <form id="kantor-form">
                    <input type="hidden" name="id" id="kantor-id">
                    <input type="hidden" name="action" value="save">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="nama_kantor" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Nama Kantor <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_kantor" id="nama_kantor" required placeholder="Contoh: Kantor Pusat" class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white dark:placeholder-slate-400 transition-all">
                        </div>
                        <div>
                            <label for="jenis_kantor" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Jenis Kantor</label>
                            <select name="jenis_kantor" id="jenis_kantor" class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all">
                                <option value="Pusat">Kantor Pusat</option>
                                <option value="Cabang">Kantor Cabang</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="alamat" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Alamat Lengkap</label>
                        <textarea name="alamat" id="alamat" rows="2" placeholder="Masukkan alamat lengkap kantor..." class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white dark:placeholder-slate-400 transition-all"></textarea>
                    </div>

                    <!-- Geofencing Toggle -->
                    <div class="mb-5 p-4 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center justify-between">
                        <div>
                            <label for="geofence_enabled" class="block text-sm font-semibold text-slate-800 dark:text-white cursor-pointer">Fitur Geofencing</label>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Batasi presensi karyawan hanya dapat dilakukan di sekitar area koordinat kantor</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="geofence_enabled" id="geofence_enabled" value="1" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <!-- GPS Coordinates & Radius -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                        <div>
                            <label for="latitude" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Latitude</label>
                            <input type="text" name="latitude" id="latitude" placeholder="Contoh: 0.79137067" class="block w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all">
                        </div>
                        <div>
                            <label for="longitude" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Longitude</label>
                            <input type="text" name="longitude" id="longitude" placeholder="Contoh: 127.38612603" class="block w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all">
                        </div>
                        <div>
                            <label for="attendance_radius" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Radius Absensi (m)</label>
                            <input type="number" name="attendance_radius" id="attendance_radius" min="1" step="1" value="10" placeholder="10" class="block w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all">
                        </div>
                    </div>

                    <!-- Interactive Map Visualizer -->
                    <div class="mb-2">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                                <i class="bi bi-map mr-1"></i> Peta Lokasi & Visualisasi Radius
                            </label>
                            <div class="flex items-center gap-3">
                                <button type="button" class="text-xs text-emerald-600 dark:text-emerald-400 cursor-pointer hover:underline flex items-center gap-1" onclick="useMyLocation()">
                                    <i class="bi bi-crosshair"></i> Gunakan lokasi saya
                                </button>
                                <span class="text-xs text-blue-600 dark:text-blue-400 cursor-pointer hover:underline" onclick="resetMapToDefault()">Pusatkan Titik</span>
                            </div>
                        </div>
                        <div id="kantor-map" class="w-full h-64 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-inner relative z-0"></div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5">
                            <i class="bi bi-info-circle text-blue-500 mr-1"></i> Klik pada peta atau geser marker untuk menentukan koordinat presisi. Lingkaran biru menandai jangkauan radius absensi.
                        </p>
                    </div>
                </form>
            </div>
            <div class="bg-slate-50 dark:bg-slate-900/50 px-6 py-4 sm:px-6 sm:flex sm:flex-row-reverse gap-3 border-t border-slate-200 dark:border-slate-700">
                <button type="button" id="save-kantor-btn" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-sm font-medium text-white focus:outline-none transition-all duration-200">Simpan Konfigurasi</button>
                <button type="button" onclick="closeKantorModal()" class="w-full inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-600 shadow-sm px-5 py-2.5 bg-white dark:bg-slate-800 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none sm:mt-0 sm:w-auto transition-all duration-200">Batal</button>
            </div>
        </div>
    </div>
</div>

<?php
if (!$is_spa_request) {
    require_once PROJECT_ROOT . '/views/footer.php';
}
?>
