function initKantorPage() {
    if (document.getElementById('kantor-table-body')) {
        loadKantor();

        const searchInput = document.getElementById('search-kantor');
        if (searchInput) searchInput.addEventListener('input', debounce(loadKantor, 500));

        const saveBtn = document.getElementById('save-kantor-btn');
        if (saveBtn) saveBtn.addEventListener('click', saveKantor);
    }
}

function loadKantor() {
    const search = document.getElementById('search-kantor').value;
    const tbody = document.getElementById('kantor-table-body');

    fetch(`${basePath}/api/hr/kantor?search=${encodeURIComponent(search)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                tbody.innerHTML = '';
                if (data.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Tidak ada data kantor.</td></tr>';
                    return;
                }
                
                data.data.forEach(item => {
                    const itemJson = JSON.stringify(item).replace(/'/g, "&#39;");
                    const row = `
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">${item.nama_kantor}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400">${item.jenis_kantor}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">${item.alamat || '-'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button onclick='editKantor(${itemJson})' class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">Edit</button>
                                <button onclick="deleteKantor(${item.id})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Hapus</button>
                            </td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="4" class="px-6 py-4 text-center text-red-500">Gagal memuat data: ${data.message}</td></tr>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data.</td></tr>';
        });
}

function openKantorModal(reset = true) {
    if (reset) {
        document.getElementById('kantor-form').reset();
        document.getElementById('kantor-id').value = '';
        document.getElementById('modal-title').innerText = 'Tambah Kantor';
    }
    document.getElementById('kantorModal').classList.remove('hidden');
}

function closeKantorModal() {
    document.getElementById('kantorModal').classList.add('hidden');
}

function editKantor(item) {
    openKantorModal(false);
    document.getElementById('kantor-id').value = item.id;
    document.getElementById('modal-title').innerText = 'Edit Kantor';
    document.getElementById('nama_kantor').value = item.nama_kantor;
    document.getElementById('jenis_kantor').value = item.jenis_kantor;
    document.getElementById('alamat').value = item.alamat;
}

function saveKantor() {
    const form = document.getElementById('kantor-form');
    const formData = new FormData(form);
    addCsrfToken(formData);

    fetch(`${basePath}/api/hr/kantor`, { method: 'POST', body: formData })
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
            closeKantorModal();
            loadKantor();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data kantor berhasil disimpan',
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

function deleteKantor(id) {
    Swal.fire({
        title: 'Hapus Kantor',
        text: 'Apakah Anda yakin ingin menghapus kantor ini?',
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

            fetch(`${basePath}/api/hr/kantor`, { method: 'POST', body: formData })
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
                    loadKantor();
                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus',
                        text: 'Data kantor berhasil dihapus',
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