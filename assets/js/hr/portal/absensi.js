let currentAction = '';
let html5QrCode = null;
let stream = null;
let geoWatchId = null;
let officeInfo = null;
let currentGps = null; // { latitude, longitude, accuracy }
let activeTab = 'camera';
let isSubmitting = false;
let gpsWarningShown = false;
let absenDone = false; // true setelah absen sukses/409: kunci tetap aktif sampai modal ditutup

function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function initPortalAbsensiPage() {
    const dateDisplay = document.getElementById('current-date-display');
    if (dateDisplay) {
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        dateDisplay.textContent = new Date().toLocaleDateString('id-ID', options);
    }

    loadTodayStatus();
    loadHistory();

    // Event Listeners
    document.getElementById('btn-clock-in')?.addEventListener('click', () => openAbsenModal('clock_in'));
    document.getElementById('btn-clock-out')?.addEventListener('click', () => openAbsenModal('clock_out'));
    document.getElementById('btn-close-modal')?.addEventListener('click', closeAbsenModal);
    document.getElementById('absenModalOverlay')?.addEventListener('click', closeAbsenModal);

    document.getElementById('tab-camera')?.addEventListener('click', () => switchTab('camera'));
    document.getElementById('tab-qr')?.addEventListener('click', () => switchTab('qr'));
    document.getElementById('btn-take-snapshot')?.addEventListener('click', takeSnapshot);
}

// Haversine di klien hanya untuk tampilan UI (validasi final di server)
function haversineDistanceM(lat1, lng1, lat2, lng2) {
    const R = 6371000;
    const toRad = (deg) => (deg * Math.PI) / 180;
    const dLat = toRad(lat2 - lat1);
    const dLng = toRad(lng2 - lng1);
    const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
        Math.sin(dLng / 2) * Math.sin(dLng / 2);
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}

function isSecureContextForGeo() {
    return (
        window.isSecureContext ||
        location.protocol === 'https:' ||
        location.hostname === 'localhost' ||
        location.hostname === '127.0.0.1'
    );
}

function setAbsenControlsEnabled(enabled) {
    const btnSnap = document.getElementById('btn-take-snapshot');
    if (btnSnap) {
        btnSnap.disabled = !enabled;
        if (enabled) {
            btnSnap.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            btnSnap.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    if (activeTab === 'qr') {
        if (enabled && !isSubmitting && (!html5QrCode || !html5QrCode.isScanning)) {
            startQrScanner();
        } else if (!enabled && html5QrCode && html5QrCode.isScanning) {
            stopQrScanner();
        }
    }
}

function renderLocationStatus(messageHtml = null) {
    const locEl = document.getElementById('location-info');
    if (!locEl) return;

    if (messageHtml) {
        locEl.innerHTML = messageHtml;
        return;
    }

    const namaKantor = escapeHtml(officeInfo?.nama_kantor || 'Belum diatur');
    const geofenceEnabled = officeInfo && Number(officeInfo.geofence_enabled) === 1;
    const radius = officeInfo ? Number(officeInfo.attendance_radius) : 0;
    const maxAcc = Number(officeInfo?.max_accuracy) || 50;

    if (!currentGps) {
        locEl.innerHTML = `
            <div class="p-2.5 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 space-y-1">
                <div><strong>Kantor:</strong> ${namaKantor}</div>
                <div><i class="bi bi-hourglass-split"></i> <strong>Status:</strong> Menunggu GPS...</div>
            </div>
        `;
        setAbsenControlsEnabled(false);
        return;
    }

    const acc = Math.round(currentGps.accuracy);
    let distanceText = '-';
    let statusBadge = '<span class="px-2 py-0.5 rounded bg-green-100 text-green-700 font-semibold">GPS siap</span>';
    let canAbsen = true;

    if (geofenceEnabled) {
        if (!officeInfo.latitude || !officeInfo.longitude) {
            statusBadge = '<span class="px-2 py-0.5 rounded bg-red-100 text-red-700 font-semibold">Koordinat kantor belum diatur</span>';
            canAbsen = false;
        } else {
            const dist = haversineDistanceM(
                currentGps.latitude,
                currentGps.longitude,
                Number(officeInfo.latitude),
                Number(officeInfo.longitude)
            );
            const distRounded = Math.round(dist);
            distanceText = `${distRounded} m (maks ${radius} m)`;

            if (currentGps.accuracy > maxAcc) {
                statusBadge = `<span class="px-2 py-0.5 rounded bg-red-100 text-red-700 font-semibold">Akurasi terlalu rendah (±${acc} m, maks ${maxAcc} m)</span>`;
                canAbsen = false;
            } else if (dist > radius) {
                statusBadge = '<span class="px-2 py-0.5 rounded bg-red-100 text-red-700 font-semibold">Di luar radius</span>';
                canAbsen = false;
            } else {
                statusBadge = '<span class="px-2 py-0.5 rounded bg-green-100 text-green-700 font-semibold">Di dalam radius</span>';
                canAbsen = true;
            }
        }
    } else if (!officeInfo) {
        statusBadge = '<span class="px-2 py-0.5 rounded bg-red-100 text-red-700 font-semibold">Penempatan kantor belum diatur</span>';
        canAbsen = false;
    }

    locEl.innerHTML = `
        <div class="p-2.5 rounded-lg bg-gray-50 dark:bg-gray-700/60 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 space-y-1 text-left">
            <div><strong>Kantor:</strong> ${namaKantor}</div>
            <div><strong>Akurasi GPS:</strong> ±${acc} m &nbsp;|&nbsp; <strong>Jarak:</strong> ${distanceText}</div>
            <div><strong>Status:</strong> ${statusBadge}</div>
        </div>
    `;
    setAbsenControlsEnabled(canAbsen);
}

function startGpsWatch() {
    stopGpsWatch();
    currentGps = null;
    setAbsenControlsEnabled(false);

    if (!isSecureContextForGeo()) {
        renderLocationStatus(
            '<div class="p-2.5 rounded-lg bg-red-50 text-red-700"><i class="bi bi-shield-exclamation"></i> Akses GPS dan kamera memerlukan koneksi HTTPS atau localhost.</div>'
        );
        return;
    }

    if (!navigator.geolocation) {
        renderLocationStatus(
            '<div class="p-2.5 rounded-lg bg-red-50 text-red-700"><i class="bi bi-exclamation-triangle"></i> Browser Anda tidak mendukung Geolocation.</div>'
        );
        return;
    }

    renderLocationStatus();

    geoWatchId = navigator.geolocation.watchPosition(
        (position) => {
            currentGps = {
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
                accuracy: position.coords.accuracy
            };
            renderLocationStatus();
        },
        (error) => {
            console.error('Error watching location:', error);
            let msg = 'Gagal mengambil lokasi GPS.';
            if (error.code === error.PERMISSION_DENIED) {
                msg = 'Izin lokasi ditolak. Aktifkan izin lokasi di pengaturan browser Anda.';
            } else if (error.code === error.POSITION_UNAVAILABLE) {
                msg = 'Lokasi tidak tersedia. Pastikan GPS / layanan lokasi perangkat aktif.';
            } else if (error.code === error.TIMEOUT) {
                msg = 'Waktu tunggu sinyal GPS habis. Coba pindah ke area terbuka.';
            }
            currentGps = null;
            setAbsenControlsEnabled(false);
            renderLocationStatus(
                `<div class="p-2.5 rounded-lg bg-red-50 text-red-700"><i class="bi bi-exclamation-triangle-fill"></i> ${msg}</div>`
            );
        },
        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
    );
}

function stopGpsWatch() {
    if (geoWatchId !== null && navigator.geolocation) {
        navigator.geolocation.clearWatch(geoWatchId);
        geoWatchId = null;
    }
}

async function fetchOfficeInfo() {
    try {
        const res = await fetch(`${basePath}/api/hr/portal/absensi?action=office_info`);
        const result = await res.json();
        officeInfo = result.success ? result.data : null;
    } catch (err) {
        console.error('Failed to fetch office info:', err);
        officeInfo = null;
    }
}

async function loadTodayStatus() {
    try {
        const response = await fetch(`${basePath}/api/hr/portal/absensi?action=today_status`);
        const result = await response.json();

        const btnIn = document.getElementById('btn-clock-in');
        const btnOut = document.getElementById('btn-clock-out');
        const statusCompleted = document.getElementById('status-completed');
        const dispMasuk = document.getElementById('display-jam-masuk');
        const dispKeluar = document.getElementById('display-jam-keluar');

        if (result.success && result.data) {
            const data = result.data;
            dispMasuk.textContent = data.jam_masuk ? data.jam_masuk.substring(0, 5) : '--:--';
            dispKeluar.textContent = data.jam_keluar ? data.jam_keluar.substring(0, 5) : '--:--';

            if (data.jam_masuk && !data.jam_keluar) {
                btnIn.classList.add('hidden');
                btnOut.classList.remove('hidden');
                statusCompleted.classList.add('hidden');
            } else if (data.jam_masuk && data.jam_keluar) {
                btnIn.classList.add('hidden');
                btnOut.classList.add('hidden');
                statusCompleted.classList.remove('hidden');
            }
        } else {
            btnIn.classList.remove('hidden');
            btnOut.classList.add('hidden');
            statusCompleted.classList.add('hidden');
        }
    } catch (error) {
        console.error('Error loading status:', error);
    }
}

async function loadHistory() {
    const tbody = document.getElementById('absensi-history-body');
    const mobileList = document.getElementById('absensi-mobile-list');

    const bulan = document.getElementById('filter-bulan')?.value || new Date().getMonth() + 1;
    const tahun = document.getElementById('filter-tahun')?.value || new Date().getFullYear();

    if (tbody) tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4">Memuat...</td></tr>';
    if (mobileList) mobileList.innerHTML = '<div class="text-center py-4 text-gray-500">Memuat data...</div>';

    try {
        const response = await fetch(`${basePath}/api/hr/portal/absensi?bulan=${bulan}&tahun=${tahun}`);
        const result = await response.json();

        if (result.success && result.data.length > 0) {
            if (tbody) tbody.innerHTML = '';
            if (mobileList) mobileList.innerHTML = '';

            result.data.forEach(row => {
                const badgeClass = row.badge_class || 'bg-gray-100 text-gray-800';

                let jenisBadge = '<span class="text-xs text-gray-400">-</span>';
                if (row.jenis_absensi === 'qrcode') {
                    jenisBadge = '<span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-200 border border-purple-200 dark:border-purple-800"><i class="bi bi-qr-code"></i> QR</span>';
                } else if (row.jenis_absensi === 'selfie') {
                    jenisBadge = '<span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200 border border-blue-200 dark:border-blue-800"><i class="bi bi-camera"></i> Selfie</span>';
                }

                let photoBtn = '-';
                if (row.foto_masuk) {
                    photoBtn = `<button onclick="viewPhoto('${basePath}/${row.foto_masuk}', 'Foto Masuk')" class="text-blue-600 hover:text-blue-800 dark:text-blue-400"><i class="bi bi-image"></i> Lihat</button>`;
                }

                if (tbody) {
                    const tr = `
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${new Date(row.tanggal).toLocaleDateString('id-ID')}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-400">${row.jam_masuk ? row.jam_masuk.substring(0, 5) : '-'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-400">${row.jam_keluar ? row.jam_keluar.substring(0, 5) : '-'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${badgeClass}">${row.status}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">${jenisBadge}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">${photoBtn}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">${row.keterangan || '-'}</td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', tr);
                }

                if (mobileList) {
                    const card = `
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 shadow-sm">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">${new Date(row.tanggal).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">${row.keterangan || '-'}</div>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full ${badgeClass}">${row.status}</span>
                            </div>

                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div class="bg-gray-50 dark:bg-gray-700/50 p-2 rounded border border-gray-100 dark:border-gray-600 text-center">
                                    <div class="text-[10px] text-gray-500 uppercase tracking-wider">Masuk</div>
                                    <div class="font-mono font-bold text-green-600 dark:text-green-400 text-lg">${row.jam_masuk ? row.jam_masuk.substring(0, 5) : '--:--'}</div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700/50 p-2 rounded border border-gray-100 dark:border-gray-600 text-center">
                                    <div class="text-[10px] text-gray-500 uppercase tracking-wider">Pulang</div>
                                    <div class="font-mono font-bold text-red-600 dark:text-red-400 text-lg">${row.jam_keluar ? row.jam_keluar.substring(0, 5) : '--:--'}</div>
                                </div>
                            </div>

                            <div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-gray-700">
                                <div class="flex items-center gap-2">
                                    ${jenisBadge}
                                </div>
                                <div class="text-sm">
                                    ${photoBtn}
                                </div>
                            </div>
                        </div>
                    `;
                    mobileList.insertAdjacentHTML('beforeend', card);
                }
            });
        } else {
            const emptyMsg = 'Belum ada riwayat absensi.';
            if (tbody) tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-gray-500">${emptyMsg}</td></tr>`;
            if (mobileList) mobileList.innerHTML = `<div class="text-center py-8 text-gray-500">${emptyMsg}</div>`;
        }
    } catch (error) {
        const errorMsg = 'Gagal memuat data.';
        if (tbody) tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-red-500">${errorMsg}</td></tr>`;
        if (mobileList) mobileList.innerHTML = `<div class="text-center py-8 text-red-500">${errorMsg}</div>`;
    }
}

async function openAbsenModal(action) {
    currentAction = action;
    isSubmitting = false;
    gpsWarningShown = false;
    absenDone = false;
    document.getElementById('modal-absen-title').textContent = action === 'clock_in' ? 'Absen Masuk' : 'Absen Pulang';
    document.getElementById('absenModal').classList.remove('hidden');

    setAbsenControlsEnabled(false);
    renderLocationStatus('<div class="p-2.5 rounded-lg bg-gray-100 text-gray-600"><i class="bi bi-hourglass-split"></i> Memuat data kantor & GPS...</div>');

    await fetchOfficeInfo();
    startGpsWatch();
    switchTab('camera');
}

function closeAbsenModal() {
    isSubmitting = false;
    absenDone = false;
    document.getElementById('absenModal').classList.add('hidden');
    stopGpsWatch();
    stopCamera();
    stopQrScanner();
}

function switchTab(tab) {
    activeTab = tab;
    const tabCamera = document.getElementById('tab-camera');
    const tabQr = document.getElementById('tab-qr');
    const secCamera = document.getElementById('section-camera');
    const secQr = document.getElementById('section-qr');

    if (tab === 'camera') {
        tabCamera.classList.add('text-primary', 'border-b-2', 'border-primary');
        tabCamera.classList.remove('text-gray-500');
        tabQr.classList.remove('text-primary', 'border-b-2', 'border-primary');
        tabQr.classList.add('text-gray-500');

        secCamera.classList.remove('hidden');
        secQr.classList.add('hidden');

        stopQrScanner();
        startCamera();
    } else {
        tabQr.classList.add('text-primary', 'border-b-2', 'border-primary');
        tabQr.classList.remove('text-gray-500');
        tabCamera.classList.remove('text-primary', 'border-b-2', 'border-primary');
        tabCamera.classList.add('text-gray-500');

        secQr.classList.remove('hidden');
        secCamera.classList.add('hidden');

        stopCamera();
        // QR scanner hanya dijalankan jika GPS sudah valid
        renderLocationStatus();
    }
}

// --- Camera Logic ---
async function startCamera() {
    const video = document.getElementById('webcam');

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        Swal.fire('Kamera Tidak Tersedia', 'Browser tidak mendukung akses kamera atau koneksi tidak aman (HTTP). Harap gunakan HTTPS atau localhost.', 'error');
        return;
    }

    try {
        stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
        video.srcObject = stream;
    } catch (err) {
        console.error('Error accessing webcam', err);
        let msg = 'Gagal mengakses kamera. Pastikan izin diberikan.';
        if (!isSecureContextForGeo()) {
            msg = 'Akses kamera diblokir karena koneksi tidak aman (HTTP). Harap akses aplikasi menggunakan HTTPS atau localhost.';
        } else if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
            msg = 'Izin kamera ditolak. Silakan reset izin di pengaturan situs browser Anda.';
        } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
            msg = 'Kamera tidak ditemukan pada perangkat ini.';
        }
        Swal.fire('Kamera Gagal', msg, 'error');
    }
}

function stopCamera() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
}

function takeSnapshot() {
    if (isSubmitting) return;
    if (!currentGps) {
        Swal.fire('GPS Belum Siap', 'Mohon tunggu hingga lokasi GPS terkunci.', 'warning');
        return;
    }
    const lockedGps = { ...currentGps };

    const video = document.getElementById('webcam');
    const canvas = document.getElementById('canvas');
    const context = canvas.getContext('2d');

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    const dataURL = canvas.toDataURL('image/jpeg');
    submitAbsensi(dataURL, null, lockedGps);
}

// --- QR Logic ---
function startQrScanner() {
    if (isSubmitting) return;
    if (typeof Html5Qrcode === 'undefined') return;
    if (html5QrCode && html5QrCode.isScanning) return;

    html5QrCode = new Html5Qrcode('qr-reader');
    const config = { fps: 10, qrbox: { width: 250, height: 250 } };

    html5QrCode.start(
        { facingMode: 'environment' },
        config,
        (decodedText) => {
            if (isSubmitting) return;
            if (!currentGps) {
                if (!gpsWarningShown) {
                    gpsWarningShown = true;
                    Swal.fire('GPS Belum Siap', 'Lokasi GPS belum terkunci.', 'warning');
                }
                return;
            }
            const lockedGps = { ...currentGps };
            stopQrScanner();
            submitAbsensi(null, decodedText, lockedGps);
        }
    ).catch(err => {
        console.error('Error starting QR scanner', err);
        let msg = 'Gagal memulai scanner QR.';
        if (!isSecureContextForGeo()) {
            msg += ' Koneksi harus menggunakan HTTPS atau localhost.';
        }
        Swal.fire('QR Scanner Gagal', msg, 'error');
    });
}

function stopQrScanner() {
    if (html5QrCode && html5QrCode.isScanning) {
        html5QrCode.stop().then(() => {
            html5QrCode.clear();
        }).catch(err => console.error(err));
    }
}

// --- Submit ---
async function submitAbsensi(fotoBase64 = null, qrContent = null, lockedGps = null) {
    if (isSubmitting) return;
    isSubmitting = true;

    const gps = lockedGps || currentGps;
    if (!gps) {
        isSubmitting = false;
        Swal.fire('GPS Belum Siap', 'Lokasi belum terdeteksi. Mohon tunggu sebentar atau aktifkan GPS.', 'warning');
        return;
    }

    const formData = new FormData();
    formData.append('action', currentAction);
    formData.append('lokasi', `${gps.latitude},${gps.longitude}`);
    formData.append('latitude', String(gps.latitude));
    formData.append('longitude', String(gps.longitude));
    formData.append('accuracy', String(gps.accuracy));
    if (fotoBase64) formData.append('foto', fotoBase64);
    if (qrContent) formData.append('qr_content', qrContent);

    if (typeof addCsrfToken === 'function') {
        addCsrfToken(formData);
    }
    if (!formData.has('csrf_token') || !formData.get('csrf_token')) {
        const token = (typeof getCsrfToken === 'function' ? getCsrfToken() : '')
            || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            || document.getElementById('portal_csrf_token')?.value
            || '';
        if (token) {
            formData.set('csrf_token', token);
        }
    }

    if (!formData.get('csrf_token')) {
        isSubmitting = false;
        Swal.fire({
            icon: 'error',
            title: 'Keamanan Sesi',
            text: 'Token keamanan (CSRF) tidak ditemukan. Silakan muat ulang halaman.'
        });
        return;
    }

    Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    try {
        const response = await fetch(`${basePath}/api/hr/portal/absensi`, {
            method: 'POST',
            body: formData
        });

        if (response.status === 401) {
            absenDone = true;
            Swal.fire({
                icon: 'warning',
                title: 'Sesi Berakhir',
                text: 'Sesi login Anda telah berakhir. Silakan login kembali.',
                confirmButtonText: 'Login'
            }).then(() => {
                window.location.href = `${basePath}/login`;
            });
            return;
        }

        let result;
        try {
            result = await response.json();
        } catch (e) {
            throw new Error('Gagal memproses respon server. Kemungkinan terjadi error internal.');
        }

        if (response.status === 409) {
            absenDone = true;
            Swal.fire({
                icon: 'warning',
                title: 'Absensi Sudah Tercatat',
                text: result.message || 'Anda sudah melakukan absensi untuk aksi ini hari ini.',
                confirmButtonColor: '#F59E0B'
            }).then(() => {
                closeAbsenModal();
                loadTodayStatus();
                loadHistory();
            });
            return;
        }

        if (result.success) {
            absenDone = true;
            Swal.fire('Berhasil!', result.message, 'success').then(() => {
                closeAbsenModal();
                loadTodayStatus();
                loadHistory();
            });
        } else {
            throw new Error(result.message || 'Gagal memproses absensi.');
        }
    } catch (error) {
        Swal.fire('Gagal', error.message, 'error');
    } finally {
        if (!absenDone) isSubmitting = false;
    }
}

window.viewPhoto = function (url, title) {
    Swal.fire({
        title: title,
        imageUrl: url,
        imageAlt: 'Foto Absensi',
        imageHeight: 400,
        confirmButtonText: 'Tutup'
    });
};