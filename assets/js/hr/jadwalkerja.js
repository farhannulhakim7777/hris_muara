function initJadwalKerjaPage() {
    if (document.getElementById('jadwal-table-body')) {
        loadJadwal();

        const saveBtn = document.getElementById('save-jadwal-btn');
        if (saveBtn) saveBtn.addEventListener('click', saveJadwal);
    }
}

function loadJadwal() {
    const tbody = document.getElementById('jadwal-table-body');

    fetch(`${basePath}/api/hr/jadwal-kerja`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                tbody.innerHTML = '';
                if (data.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Belum ada jadwal kerja yang dibuat.</td></tr>';
                    return;
                }
                
                data.data.forEach(item => {
                    const statusBadge = item.is_active == 1 
                        ? '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Aktif</span>'
                        : '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Non-Aktif</span>';

                    const itemJson = JSON.stringify(item).replace(/'/g, "&#39;");
                    const row = `
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">${item.nama_jadwal}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400">${item.jam_masuk}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400">${item.jam_pulang}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">${statusBadge}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button onclick='editJadwal(${itemJson})' class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">Edit</button>
                                <button onclick="deleteJadwal(${item.id})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Hapus</button>
                            </td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Gagal memuat data: ${data.message}</td></tr>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data.</td></tr>';
        });
}

function openJadwalModal(reset = true) {
    if (reset) {
        document.getElementById('jadwal-form').reset();
        document.getElementById('jadwal-id').value = '';
        document.getElementById('modal-title').innerText = 'Tambah Jadwal Kerja';
    }
    document.getElementById('jadwalModal').classList.remove('hidden');
}

function closeJadwalModal() {
    document.getElementById('jadwalModal').classList.add('hidden');
}

function editJadwal(item) {
    openJadwalModal(false);
    document.getElementById('jadwal-id').value = item.id;
    document.getElementById('modal-title').innerText = 'Edit Jadwal Kerja';
    document.getElementById('nama_jadwal').value = item.nama_jadwal;
    document.getElementById('jam_masuk').value = item.jam_masuk;
    document.getElementById('jam_pulang').value = item.jam_pulang;
    document.getElementById('is_active').value = item.is_active;
}

function saveJadwal() {
    const form = document.getElementById('jadwal-form');
    const formData = new FormData(form);
    addCsrfToken(formData);

    fetch(`${basePath}/api/hr/jadwal-kerja`, { method: 'POST', body: formData })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                console.error('Server Error Response:', text);
                throw new Error(text);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            closeJadwalModal();
            loadJadwal();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data jadwal berhasil disimpan',
                confirmButtonColor: '#10B981',
                timer: 2000,
                timerProgressBar: true
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: data.message,
                confirmButtonColor: '#EF4444'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message,
            confirmButtonColor: '#EF4444'
        });
    });
}

function deleteJadwal(id) {
    Swal.fire({
        title: 'Hapus Jadwal Kerja',
        text: 'Apakah Anda yakin ingin menghapus jadwal ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', id);
            addCsrfToken(formData);

            fetch(`${basePath}/api/hr/jadwal-kerja`, { method: 'POST', body: formData })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('Server Error Response:', text);
                        throw new Error(text);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    loadJadwal();
                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus',
                        text: 'Data jadwal berhasil dihapus',
                        confirmButtonColor: '#10B981',
                        timer: 2000,
                        timerProgressBar: true
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message,
                        confirmButtonColor: '#EF4444'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message,
                    confirmButtonColor: '#EF4444'
                });
            });
        }
    });
}