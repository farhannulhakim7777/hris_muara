let kantorMap = null;
let kantorMarker = null;
let kantorCircle = null;

const DEFAULT_COORDS = {
    lat: 0.7913706711114893,
    lng: 127.38612602822592,
    radius: 10
};

function initKantorPage() {
    if (kantorMap) { try { kantorMap.remove(); } catch (e) {} }
    kantorMap = null;
    kantorMarker = null;
    kantorCircle = null;

    if (document.getElementById('kantor-table-body')) {
        loadKantor();

        const searchInput = document.getElementById('search-kantor');
        if (searchInput) searchInput.addEventListener('input', debounce(loadKantor, 500));

        const saveBtn = document.getElementById('save-kantor-btn');
        if (saveBtn) saveBtn.addEventListener('click', saveKantor);

        // Input sync with Leaflet map
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const radInput = document.getElementById('attendance_radius');

        if (latInput && lngInput) {
            const updateFromInputs = () => {
                const lat = parseFloat(latInput.value);
                const lng = parseFloat(lngInput.value);
                if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                    if (kantorMarker && kantorCircle && kantorMap) {
                        kantorMarker.setLatLng([lat, lng]);
                        kantorCircle.setLatLng([lat, lng]);
                        kantorMap.panTo([lat, lng]);
                    }
                }
            };
            latInput.addEventListener('change', updateFromInputs);
            lngInput.addEventListener('change', updateFromInputs);
        }

        if (radInput) {
            radInput.addEventListener('input', () => {
                const rad = parseInt(radInput.value, 10);
                if (!isNaN(rad) && rad > 0 && kantorCircle) {
                    kantorCircle.setRadius(rad);
                }
            });
        }
    }
}

function loadKantor() {
    const search = document.getElementById('search-kantor')?.value || '';
    const tbody = document.getElementById('kantor-table-body');
    if (!tbody) return;

    fetch(`${basePath}/api/hr/kantor?search=${encodeURIComponent(search)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                tbody.innerHTML = '';
                if (!data.data || data.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">Tidak ada data kantor ditemukan.</td></tr>';
                    return;
                }
                
                data.data.forEach(item => {
                    const itemJson = JSON.stringify(item).replace(/'/g, "&#39;");
                    
                    const coordsDisplay = (item.latitude !== null && item.longitude !== null && item.latitude !== '' && item.longitude !== '')
                        ? `<span class="font-mono text-xs bg-slate-100 dark:bg-slate-700/60 text-slate-800 dark:text-slate-200 px-2 py-1 rounded-lg select-all">${parseFloat(item.latitude).toFixed(6)}, ${parseFloat(item.longitude).toFixed(6)}</span>`
                        : `<span class="text-xs text-slate-400 italic">Belum diatur</span>`;

                    const geofenceBadge = (item.geofence_enabled == 1)
                        ? `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300"><span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-green-500"></span>Aktif</span>`
                        : `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400"><span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-slate-400"></span>Non-aktif</span>`;

                    const jenisBadge = item.jenis_kantor === 'Pusat'
                        ? `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">Pusat</span>`
                        : `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300">${item.jenis_kantor}</span>`;

                    const row = `
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-900 dark:text-white">${item.nama_kantor}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">${jenisBadge}</td>
                            <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400 max-w-xs truncate" title="${item.alamat || '-'}">${item.alamat || '-'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">${coordsDisplay}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-slate-700 dark:text-slate-300">${item.attendance_radius} m</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">${geofenceBadge}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button type="button" onclick='editKantor(${itemJson})' class="inline-flex items-center text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 mr-3 transition-colors">
                                    <i class="bi bi-pencil-square mr-1"></i> Edit
                                </button>
                                <button type="button" onclick="deleteKantor(${item.id})" class="inline-flex items-center text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                    <i class="bi bi-trash mr-1"></i> Hapus
                                </button>
                            </td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="7" class="px-6 py-4 text-center text-red-500">Gagal memuat data: ${data.message}</td></tr>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-red-500">Terjadi kesalahan saat memuat data kantor.</td></tr>';
        });
}

function initOrUpdateMap(targetLat = null, targetLng = null, targetRadius = 10) {
    if (typeof L === 'undefined') {
        console.warn('Leaflet library is not loaded yet.');
        return;
    }

    const mapContainer = document.getElementById('kantor-map');
    if (!mapContainer) return;

    let lat = parseFloat(targetLat);
    let lng = parseFloat(targetLng);
    let radius = parseInt(targetRadius, 10);

    if (isNaN(lat) || isNaN(lng)) {
        lat = DEFAULT_COORDS.lat;
        lng = DEFAULT_COORDS.lng;
    }
    if (isNaN(radius) || radius <= 0) {
        radius = DEFAULT_COORDS.radius;
    }

    if (!kantorMap) {
        kantorMap = L.map('kantor-map').setView([lat, lng], 17);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(kantorMap);

        kantorMarker = L.marker([lat, lng], { draggable: true }).addTo(kantorMap);
        kantorCircle = L.circle([lat, lng], {
            radius: radius,
            color: '#2563EB',
            fillColor: '#3B82F6',
            fillOpacity: 0.25,
            weight: 2
        }).addTo(kantorMap);

        const onMarkerMove = (e) => {
            const pos = e.target.getLatLng();
            kantorCircle.setLatLng(pos);
            const latEl = document.getElementById('latitude');
            const lngEl = document.getElementById('longitude');
            if (latEl) latEl.value = pos.lat.toFixed(14);
            if (lngEl) lngEl.value = pos.lng.toFixed(14);
        };

        kantorMarker.on('drag', onMarkerMove);
        kantorMarker.on('dragend', onMarkerMove);

        kantorMap.on('click', (e) => {
            kantorMarker.setLatLng(e.latlng);
            kantorCircle.setLatLng(e.latlng);
            const latEl = document.getElementById('latitude');
            const lngEl = document.getElementById('longitude');
            if (latEl) latEl.value = e.latlng.lat.toFixed(14);
            if (lngEl) lngEl.value = e.latlng.lng.toFixed(14);
        });
    } else {
        kantorMap.invalidateSize();
        kantorMap.setView([lat, lng], 17);
        kantorMarker.setLatLng([lat, lng]);
        kantorCircle.setLatLng([lat, lng]);
        kantorCircle.setRadius(radius);
    }
}

window.resetMapToDefault = function() {
    const latEl = document.getElementById('latitude');
    const lngEl = document.getElementById('longitude');
    const radEl = document.getElementById('attendance_radius');

    if (latEl) latEl.value = DEFAULT_COORDS.lat.toFixed(14);
    if (lngEl) lngEl.value = DEFAULT_COORDS.lng.toFixed(14);
    const radius = radEl ? parseInt(radEl.value, 10) || DEFAULT_COORDS.radius : DEFAULT_COORDS.radius;

    initOrUpdateMap(DEFAULT_COORDS.lat, DEFAULT_COORDS.lng, radius);
};

window.useMyLocation = function() {
    if (!navigator.geolocation) {
        Swal.fire('Tidak Didukung', 'Browser Anda tidak mendukung Geolocation.', 'error');
        return;
    }

    navigator.geolocation.getCurrentPosition(
        (pos) => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            const latEl = document.getElementById('latitude');
            const lngEl = document.getElementById('longitude');
            const radEl = document.getElementById('attendance_radius');

            if (latEl) latEl.value = lat.toFixed(14);
            if (lngEl) lngEl.value = lng.toFixed(14);
            const radius = radEl ? parseInt(radEl.value, 10) || DEFAULT_COORDS.radius : DEFAULT_COORDS.radius;

            initOrUpdateMap(lat, lng, radius);
        },
        (err) => {
            let msg = 'Gagal mengambil lokasi GPS.';
            if (err.code === err.PERMISSION_DENIED) {
                msg = 'Izin lokasi ditolak. Aktifkan izin lokasi pada browser Anda.';
            } else if (err.code === err.POSITION_UNAVAILABLE) {
                msg = 'Lokasi tidak tersedia. Pastikan GPS aktif.';
            } else if (err.code === err.TIMEOUT) {
                msg = 'Waktu tunggu GPS habis.';
            }
            Swal.fire('GPS Gagal', msg, 'error');
        },
        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
    );
};

function openKantorModal(reset = true) {
    if (reset) {
        document.getElementById('kantor-form').reset();
        document.getElementById('kantor-id').value = '';
        document.getElementById('modal-title').innerHTML = '<i class="bi bi-geo-alt-fill text-blue-500 mr-2"></i> Tambah Kantor';
        document.getElementById('attendance_radius').value = 10;
        document.getElementById('geofence_enabled').checked = false;
        document.getElementById('latitude').value = '';
        document.getElementById('longitude').value = '';
    }
    
    document.getElementById('kantorModal').classList.remove('hidden');

    setTimeout(() => {
        const latVal = document.getElementById('latitude')?.value;
        const lngVal = document.getElementById('longitude')?.value;
        const radVal = document.getElementById('attendance_radius')?.value;
        initOrUpdateMap(latVal, lngVal, radVal);
    }, 200);
}

function closeKantorModal() {
    document.getElementById('kantorModal').classList.add('hidden');
}

function editKantor(item) {
    openKantorModal(false);
    document.getElementById('kantor-id').value = item.id;
    document.getElementById('modal-title').innerHTML = '<i class="bi bi-geo-alt-fill text-blue-500 mr-2"></i> Edit Kantor';
    document.getElementById('nama_kantor').value = item.nama_kantor;
    document.getElementById('jenis_kantor').value = item.jenis_kantor;
    document.getElementById('alamat').value = item.alamat || '';
    
    document.getElementById('latitude').value = (item.latitude !== null && item.latitude !== undefined) ? item.latitude : '';
    document.getElementById('longitude').value = (item.longitude !== null && item.longitude !== undefined) ? item.longitude : '';
    document.getElementById('attendance_radius').value = item.attendance_radius || 10;
    document.getElementById('geofence_enabled').checked = (item.geofence_enabled == 1);

    setTimeout(() => {
        initOrUpdateMap(item.latitude, item.longitude, item.attendance_radius);
    }, 200);
}

function saveKantor() {
    const form = document.getElementById('kantor-form');
    if (!form) return;

    const nama = document.getElementById('nama_kantor').value.trim();
    if (!nama) {
        Swal.fire({
            icon: 'warning',
            title: 'Validasi Gagal',
            text: 'Nama Kantor wajib diisi.',
            confirmButtonColor: '#F59E0B'
        });
        return;
    }

    const radiusInput = document.getElementById('attendance_radius').value;
    const radius = parseInt(radiusInput, 10);
    if (isNaN(radius) || radius <= 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Validasi Gagal',
            text: 'Radius absensi harus berupa angka positif lebih besar dari nol (meter).',
            confirmButtonColor: '#F59E0B'
        });
        return;
    }

    const geofenceChecked = document.getElementById('geofence_enabled').checked;
    const latVal = document.getElementById('latitude').value.trim();
    const lngVal = document.getElementById('longitude').value.trim();

    if (geofenceChecked && (!latVal || !lngVal)) {
        Swal.fire({
            icon: 'warning',
            title: 'Validasi Gagal',
            text: 'Koordinat kantor (Latitude dan Longitude) wajib diisi ketika fitur geofencing diaktifkan.',
            confirmButtonColor: '#F59E0B'
        });
        return;
    }

    if (latVal !== '') {
        const lat = parseFloat(latVal);
        if (isNaN(lat) || lat < -90 || lat > 90) {
            Swal.fire({
                icon: 'warning',
                title: 'Validasi Gagal',
                text: 'Nilai Latitude tidak valid. Harus berada di antara -90 dan 90 derajat.',
                confirmButtonColor: '#F59E0B'
            });
            return;
        }
    }

    if (lngVal !== '') {
        const lng = parseFloat(lngVal);
        if (isNaN(lng) || lng < -180 || lng > 180) {
            Swal.fire({
                icon: 'warning',
                title: 'Validasi Gagal',
                text: 'Nilai Longitude tidak valid. Harus berada di antara -180 dan 180 derajat.',
                confirmButtonColor: '#F59E0B'
            });
            return;
        }
    }

    const formData = new FormData(form);
    
    // Ensure geofence_enabled is sent as 1 or 0
    formData.set('geofence_enabled', geofenceChecked ? '1' : '0');

    // CSRF Protection
    if (typeof addCsrfToken === 'function') {
        addCsrfToken(formData);
    }
    if (!formData.has('csrf_token') || !formData.get('csrf_token')) {
        const token = (typeof getCsrfToken === 'function' ? getCsrfToken() : '') 
            || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
            || '';
        if (token) formData.set('csrf_token', token);
    }

    Swal.fire({
        title: 'Menyimpan...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch(`${basePath}/api/hr/kantor`, {
        method: 'POST',
        body: formData
    })
    .then(async response => {
        const data = await response.json().catch(() => ({ success: false, message: 'Respon server tidak valid' }));
        if (!response.ok) {
            throw new Error(data.message || 'Gagal menyimpan data');
        }
        return data;
    })
    .then(data => {
        if (data.success) {
            closeKantorModal();
            loadKantor();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: data.message || 'Data kantor berhasil disimpan',
                confirmButtonColor: '#10B981',
                timer: 2000,
                timerProgressBar: true
            });
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: error.message,
            confirmButtonColor: '#EF4444'
        });
    });
}

function deleteKantor(id) {
    Swal.fire({
        title: 'Hapus Kantor',
        text: 'Apakah Anda yakin ingin menghapus kantor ini? Kantor yang masih digunakan karyawan tidak dapat dihapus.',
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

            if (typeof addCsrfToken === 'function') {
                addCsrfToken(formData);
            }
            if (!formData.has('csrf_token') || !formData.get('csrf_token')) {
                const token = (typeof getCsrfToken === 'function' ? getCsrfToken() : '') 
                    || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                    || '';
                if (token) formData.set('csrf_token', token);
            }

            Swal.fire({
                title: 'Menghapus...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch(`${basePath}/api/hr/kantor`, {
                method: 'POST',
                body: formData
            })
            .then(async response => {
                const data = await response.json().catch(() => ({ success: false, message: 'Respon server tidak valid' }));
                if (!response.ok) {
                    throw new Error(data.message || 'Gagal menghapus data');
                }
                return data;
            })
            .then(data => {
                if (data.success) {
                    loadKantor();
                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus',
                        text: data.message || 'Data kantor berhasil dihapus',
                        confirmButtonColor: '#10B981',
                        timer: 2000,
                        timerProgressBar: true
                    });
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: error.message,
                    confirmButtonColor: '#EF4444'
                });
            });
        }
    });
}
