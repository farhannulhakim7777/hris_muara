<?php
$is_spa_request = isset($_SERVER['HTTP_X_SPA_REQUEST']) && $_SERVER['HTTP_X_SPA_REQUEST'] === 'true';
if (!$is_spa_request) {
    require_once PROJECT_ROOT . '/views/header.php';
}

// Security check (Pastikan permission 'hr_karyawan' sudah ditambahkan ke database jika ingin mengaktifkan ini)
// check_permission('hr_karyawan', 'view');
?>

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
            <i class="bi bi-people-fill text-blue-500"></i> Manajemen Karyawan
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola data dan profil karyawan perusahaan</p>
    </div>
    <button type="button" class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-xl shadow-lg text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 hover:-translate-y-0.5" onclick="openKaryawanModal()">
        <i class="bi bi-plus-circle mr-2"></i> Tambah Karyawan
    </button>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                <i class="bi bi-person-check text-emerald-600 dark:text-emerald-400"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400">Aktif</p>
                <p class="text-lg font-bold text-slate-800 dark:text-white" id="stat-aktif">0</p>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center">
                <i class="bi bi-person-x text-rose-600 dark:text-rose-400"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400">Nonaktif</p>
                <p class="text-lg font-bold text-slate-800 dark:text-white" id="stat-nonaktif">0</p>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                <i class="bi bi-people text-blue-600 dark:text-blue-400"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400">Total</p>
                <p class="text-lg font-bold text-slate-800 dark:text-white" id="stat-total">0</p>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                <i class="bi bi-clock text-amber-600 dark:text-amber-400"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400">Probation</p>
                <p class="text-lg font-bold text-slate-800 dark:text-white" id="stat-probation">0</p>
            </div>
        </div>
    </div>
</div>

<!-- Filter & Search -->
<div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label for="search-karyawan" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Pencarian</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="bi bi-search text-slate-400"></i>
                </div>
                <input type="text" id="search-karyawan" class="block w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white dark:placeholder-slate-400 transition-all duration-200" placeholder="Cari NIP atau Nama...">
            </div>
        </div>
        <div>
            <label for="filter-status" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Status Karyawan</label>
            <select id="filter-status" class="block w-full pl-4 pr-10 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200">
                <option value="">Semua Status</option>
                <option value="aktif">Aktif</option>
                <option value="nonaktif">Nonaktif</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="button" onclick="loadKaryawan()" class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 transition-all duration-200 text-sm font-medium">
                <i class="bi bi-arrow-clockwise mr-2"></i> Refresh
            </button>
        </div>
    </div>
</div>

<!-- Table -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-900/50">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">NIP</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Lengkap</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Detail Pekerjaan</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jadwal</th>
                    <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody id="karyawan-table-body" class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
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

<!-- Modal Form Karyawan -->
<div id="karyawanModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeKaryawanModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white dark:bg-slate-800 px-6 pt-6 pb-4 sm:p-6 sm:pb-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                <h3 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-2" id="modal-title"><i class="bi bi-person-plus-fill text-blue-500"></i> Form Karyawan</h3>
                <button type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors" onclick="closeKaryawanModal()">
                    <i class="bi bi-x-lg text-xl"></i>
                </button>
            </div>
            <div class="p-6 max-h-[70vh] overflow-y-auto">
                <form id="karyawan-form">
                    <input type="hidden" name="id" id="karyawan-id">
                    <input type="hidden" name="action" id="karyawan-action" value="save">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label for="nip" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2"><i class="bi bi-card-heading mr-2 text-slate-400"></i>NIP</label>
                            <input type="text" name="nip" id="nip" required class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white dark:placeholder-slate-400 transition-all duration-200" placeholder="Contoh: EMP001">
                        </div>
                        
                        <div>
                            <label for="nama_lengkap" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2"><i class="bi bi-person mr-2 text-slate-400"></i>Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" required class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white dark:placeholder-slate-400 transition-all duration-200" placeholder="Nama Lengkap Karyawan">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label for="divisi_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2"><i class="bi bi-diagram-3 mr-2 text-slate-400"></i>Divisi</label>
                            <select name="divisi_id" id="divisi_id" class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200">
                                <option value="">Pilih Divisi</option>
                            </select>
                        </div>

                        <div>
                            <label for="kantor_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2"><i class="bi bi-building mr-2 text-slate-400"></i>Kantor</label>
                            <select name="kantor_id" id="kantor_id" class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200">
                                <option value="">Pilih Kantor</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label for="golongan_gaji_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2"><i class="bi bi-cash-coin mr-2 text-slate-400"></i>Golongan Gaji</label>
                            <select name="golongan_gaji_id" id="golongan_gaji_id" class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200">
                                <option value="">Pilih Golongan Gaji</option>
                            </select>
                        </div>

                        <div>
                            <label for="jadwal_kerja_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2"><i class="bi bi-clock mr-2 text-slate-400"></i>Jadwal Kerja</label>
                            <select name="jadwal_kerja_id" id="jadwal_kerja_id" class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200">
                                <option value="">Pilih Jadwal</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label for="jabatan_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2"><i class="bi bi-briefcase mr-2 text-slate-400"></i>Jabatan</label>
                            <select name="jabatan_id" id="jabatan_id" class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200">
                                <option value="">Pilih Jabatan</option>
                            </select>
                        </div>
                        <div>
                            <label for="atasan_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2"><i class="bi bi-person-badge mr-2 text-slate-400"></i>Atasan Langsung</label>
                            <select name="atasan_id" id="atasan_id" class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200">
                                <option value="">Pilih Atasan</option>
                            </select>
                        </div>
                    </div>

                    <div class="border-t border-slate-200 dark:border-slate-700 pt-5 mt-5 bg-slate-50 dark:bg-slate-900/50 p-5 rounded-xl">
                        <h4 class="text-sm font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2"><i class="bi bi-person-badge text-blue-500"></i> Informasi Pribadi</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                            <div>
                                <label for="tanggal_lahir" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200">
                            </div>
                            <div>
                                <label for="no_hp" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nomor HP</label>
                                <input type="tel" name="no_hp" id="no_hp" maxlength="20" class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white dark:placeholder-slate-400 transition-all duration-200" placeholder="08xxxxxxxxxx atau +62xxxxxxxxxx">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email</label>
                                <input type="email" name="email" id="email" maxlength="100" class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white dark:placeholder-slate-400 transition-all duration-200" placeholder="email@contoh.com">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                        <div>
                            <label for="tanggal_masuk" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2"><i class="bi bi-calendar-event mr-2 text-slate-400"></i>Tgl Masuk</label>
                            <input type="date" name="tanggal_masuk" id="tanggal_masuk" required class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200">
                        </div>
                        <div>
                            <label for="tanggal_berakhir_kontrak" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2"><i class="bi bi-calendar-x mr-2 text-slate-400"></i>Tgl Berakhir (Opsional)</label>
                            <input type="date" name="tanggal_berakhir_kontrak" id="tanggal_berakhir_kontrak" class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200">
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2"><i class="bi bi-info-circle mr-2 text-slate-400"></i>Status</label>
                            <select name="status" id="status" class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                                <option value="probation">Probation</option>
                                <option value="kontrak">Kontrak</option>
                            </select>
                        </div>
                    </div>

                    <div class="border-t border-slate-200 dark:border-slate-700 pt-5 mt-5 bg-slate-50 dark:bg-slate-900/50 p-5 rounded-xl">
                        <h4 class="text-sm font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2"><i class="bi bi-calculator text-blue-500"></i> Informasi Pajak & BPJS</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                            <div>
                                <label for="npwp" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">NPWP</label>
                                <input type="text" name="npwp" id="npwp" class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white dark:placeholder-slate-400 transition-all duration-200" placeholder="Nomor NPWP">
                            </div>

                            <div>
                                <label for="status_ptkp" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Status PTKP</label>
                                <select name="status_ptkp" id="status_ptkp" class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200">
                                    <option value="TK/0">TK/0 - Tidak Kawin, 0 Tanggungan</option>
                                    <option value="K/0">K/0 - Kawin, 0 Tanggungan</option>
                                    <option value="K/1">K/1 - Kawin, 1 Tanggungan</option>
                                    <option value="K/2">K/2 - Kawin, 2 Tanggungan</option>
                                    <option value="K/3">K/3 - Kawin, 3 Tanggungan</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center mb-3">
                            <input type="checkbox" name="ikut_bpjs_kes" id="ikut_bpjs_kes" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 dark:border-slate-600 rounded cursor-pointer">
                            <label for="ikut_bpjs_kes" class="ml-2 block text-sm text-slate-700 dark:text-slate-300">Ikut BPJS Kesehatan</label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="ikut_bpjs_tk" id="ikut_bpjs_tk" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 dark:border-slate-600 rounded cursor-pointer">
                            <label for="ikut_bpjs_tk" class="ml-2 block text-sm text-slate-700 dark:text-slate-300">Ikut BPJS Ketenagakerjaan</label>
                        </div>
                    </div>

                    <div class="border-t border-slate-200 dark:border-slate-700 pt-5 mt-5 bg-slate-50 dark:bg-slate-900/50 p-5 rounded-xl">
                        <h4 class="text-sm font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2"><i class="bi bi-person-check text-blue-500"></i> Akun Pengguna</h4>
                        <div class="mb-3">
                            <label for="user_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Akun Pengguna</label>
                            <select name="user_id" id="user_id" class="block w-full px-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white transition-all duration-200">
                                <option value="">Belum Dihubungkan</option>
                            </select>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Akun pengguna harus dibuat terlebih dahulu melalui menu Manajemen Pengguna.</p>
                        </div>
                    </div>
                </form>
            </div>
            <div class="bg-slate-50 dark:bg-slate-900/50 px-6 py-4 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                <button type="button" id="save-karyawan-btn" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-base font-medium text-white focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-all duration-200">Simpan</button>
                <button type="button" onclick="closeKaryawanModal()" class="w-full inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-600 shadow-sm px-4 py-2.5 bg-white dark:bg-slate-800 text-base font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all duration-200">Batal</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Initiate Offboarding -->
<div id="initiateOffboardingModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal('initiateOffboardingModal')"></div>
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="initiate-offboarding-form">
                <input type="hidden" name="action" value="initiate">
                <input type="hidden" name="karyawan_id" id="offboarding_karyawan_id">
                <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Mulai Proses Offboarding</h3>
                    <p class="text-sm text-gray-500 mb-4">Proses ini akan memulai checklist offboarding untuk <strong id="offboarding_karyawan_nama"></strong>.</p>
                    
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe Offboarding</label>
                        <select name="tipe" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 shadow-sm sm:text-sm" required>
                            <option value="resign">Resign (Mengundurkan Diri)</option>
                            <option value="terminate">Terminasi (PHK)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Efektif (Hari Terakhir)</label>
                        <input type="date" name="tanggal_efektif" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 shadow-sm sm:text-sm" required value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
                        <input type="hidden" name="tanggal_pengajuan" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alasan</label>
                        <textarea name="alasan" rows="3" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 shadow-sm sm:text-sm" required></textarea>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Mulai Proses</button>
                    <button type="button" onclick="closeModal('initiateOffboardingModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Evaluasi Probation -->
<div id="probationModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal('probationModal')"></div>
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="probation-form">
                <input type="hidden" name="action" value="save_probation_evaluation">
                <input type="hidden" name="karyawan_id" id="probation_karyawan_id">
                <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Evaluasi Masa Percobaan</h3>
                    <p class="text-sm text-gray-500 mb-4">Evaluasi untuk <strong id="probation_karyawan_nama"></strong>.</p>
                    
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Evaluasi</label>
                        <input type="date" name="tanggal_evaluasi" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 shadow-sm sm:text-sm" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Skor Teknis (0-100)</label>
                            <input type="number" name="skor_teknis" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 shadow-sm sm:text-sm" required min="0" max="100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Skor Budaya (0-100)</label>
                            <input type="number" name="skor_budaya" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 shadow-sm sm:text-sm" required min="0" max="100">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rekomendasi</label>
                        <select name="rekomendasi" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 shadow-sm sm:text-sm" required>
                            <option value="angkat_tetap">Angkat Menjadi Karyawan Tetap</option>
                            <option value="perpanjang_probation">Perpanjang Masa Percobaan</option>
                            <option value="terminasi">Terminasi (Tidak Lulus)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan Evaluasi</label>
                        <textarea name="catatan" rows="3" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 shadow-sm sm:text-sm" required></textarea>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Simpan Evaluasi</button>
                    <button type="button" onclick="closeModal('probationModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Riwayat Evaluasi Probation -->
<div id="probationHistoryModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal('probationHistoryModal')"></div>
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Riwayat Evaluasi Probation</h3>
                <button type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none" onclick="closeModal('probationHistoryModal')">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Karyawan: <strong id="history_karyawan_nama" class="text-gray-900 dark:text-white"></strong></p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700" id="probation-history-head"></thead>
                        <tbody id="probation-history-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                    </table>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeModal('probationHistoryModal')" class="w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Riwayat Gaji -->
<div id="salaryHistoryModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal('salaryHistoryModal')"></div>
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Riwayat Perubahan Gaji</h3>
                <button type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none" onclick="closeModal('salaryHistoryModal')">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Karyawan: <strong id="salary_history_karyawan_nama" class="text-gray-900 dark:text-white"></strong></p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700" id="salary-history-head"></thead>
                        <tbody id="salary-history-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                    </table>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeModal('salaryHistoryModal')" class="w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?php
if (!$is_spa_request) {
    require_once PROJECT_ROOT . '/views/footer.php';
}
?>