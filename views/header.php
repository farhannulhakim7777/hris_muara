<?php
// Ambil pengaturan aplikasi dari database untuk digunakan di seluruh UI
$app_settings = [];
$settings_conn = Database::getInstance()->getConnection();
$settings_result = $settings_conn->query("SELECT setting_key, setting_value FROM settings");
if ($settings_result) {
    while ($row = $settings_result->fetch_assoc()) {
        $app_settings[$row['setting_key']] = $row['setting_value'];
    }
}
$app_name = htmlspecialchars($app_settings['app_name'] ?? 'Aplikasi RT');
$notification_interval = (int)($app_settings['notification_interval'] ?? 15000);
$log_cleanup_days = (int)($app_settings['log_cleanup_interval_days'] ?? 180);

// Determine Layout Mode
$layout_mode = $_COOKIE['layout_mode'] ?? 'sidebar'; // 'sidebar' or 'icon_menu'
?>
<!doctype html>
<html lang="en" class="<?= $layout_mode === 'icon_menu' ? 'layout-icon-menu' : '' ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= get_csrf_token() ?>">
    <title><?= $app_name ?></title>    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <?php $v=date("Ymd"); ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css?v='.$v) ?>">
    <!-- Main JS (Global Helpers) -->
    <script src="<?= base_url('assets/js/main.js?v='.$v) ?>"></script>
    <!-- Favicon  -->
    <link rel="icon" href="assets/favicon.png" />
    <script>
        // Konfigurasi Tailwind
        tailwind.config = {
            darkMode: 'class', // atau 'media'
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: 'var(--theme-color, #007aff)',
                            '50': 'var(--theme-color-50, #e6f2ff)',
                            '100': 'var(--theme-color-100, #b3d9ff)',
                            '500': 'var(--theme-color-500, #007aff)',
                            '600': 'var(--theme-color-600, #006de6)',
                        },
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', 'Helvetica', 'Arial', 'sans-serif', '"Apple Color Emoji"', '"Segoe UI Emoji"', '"Segoe UI Symbol"'],
                    }
                }
            }
        }

        const userRole = '<?= $_SESSION['role'] ?? 'warga' ?>';
        const username = '<?= $_SESSION['username'] ?? '' ?>';
        const basePath = '<?= BASE_PATH ?>';
        const notificationInterval = <?= $notification_interval ?>;
        const logCleanupDays = <?= $log_cleanup_days ?>;
        const layoutMode = '<?= $layout_mode ?>';

        // Load Sidebar State immediately to prevent flash
        if (layoutMode === 'sidebar' && localStorage.getItem('sidebar_minimized') === 'true' && window.innerWidth >= 1024) {
            document.documentElement.classList.add('sidebar-minimized'); // Use html/body class
            // We add it to body in DOMContentLoaded, but adding to html here helps early rendering
        }
    </script>
    <style>
        /* Custom scrollbar untuk sidebar */
        .sidebar-scroll::-webkit-scrollbar { width: 6px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background-color: rgba(0,0,0,0.2); border-radius: 10px; }
        html.dark .sidebar-scroll::-webkit-scrollbar-thumb { background-color: rgba(255,255,255,0.2); }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 font-sans">
<div id="app-container" class="flex h-screen overflow-hidden">
    <?php if ($layout_mode === 'sidebar'): ?>
    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar fixed inset-y-0 left-0 z-[1040] bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-700 transform -translate-x-full lg:translate-x-0 lg:relative lg:inset-0 transition-all duration-300 ease-in-out flex flex-col shadow-lg">
        <!-- Sidebar Header -->
        <div class="flex items-center justify-between h-16 px-5 border-b border-slate-200 dark:border-slate-700 flex-shrink-0 bg-gradient-to-r from-blue-50 to-white dark:from-slate-800 dark:to-slate-900">
            <a href="<?= base_url('/dashboard') ?>" class="flex items-center gap-3">
                <?php
                $logo_path = $app_settings['app_logo'] ?? null;
                $logo_url = $logo_path ? base_url($logo_path) : base_url('assets/img/logo.png');
                ?>
                <div class="relative">
                    <img src="<?= $logo_url ?>" alt="Logo" class="h-9 w-9 object-contain rounded-lg shadow-sm">
                    <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-blue-500 rounded-full border-2 border-white dark:border-slate-900"></div>
                </div>
                <span class="sidebar-text text-lg font-bold text-slate-800 dark:text-white tracking-tight"><?= $app_name ?></span>
            </a>
        </div>

        <!-- Sidebar Search -->
        <div class="px-4 pt-4 pb-2" id="sidebar-search-container">
            <div class="relative group">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 group-focus-within:text-blue-500 transition-colors pointer-events-none">
                    <i class="bi bi-search text-sm"></i>
                </span>
                <input type="text" id="sidebar-search" class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white dark:placeholder-slate-400 transition-all duration-200" placeholder="Cari menu...">
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav id="sidebar-nav" class="flex-1 overflow-y-auto sidebar-scroll px-3 py-2">
            <?php require_once __DIR__ . '/_menu_items.php'; ?>
        </nav>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-slate-200 dark:border-slate-700">
            <div class="text-xs text-slate-400 dark:text-slate-500 text-center">
                &copy; <?= date('Y') ?> <?= $app_name ?>
            </div>
        </div>

        <!-- Resizer Handle -->
        <div id="sidebar-resizer" class="hidden lg:block absolute top-0 right-0 w-1 h-full cursor-col-resize hover:bg-blue-500 transition-colors z-50 bg-transparent" title="Tarik untuk mengubah lebar"></div>
    </aside>

    <!-- Mobile Overlay --> 
    <div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[1030] hidden lg:hidden transition-opacity duration-300" onclick="toggleSidebar()"></div>
    <?php endif; ?>

    <!-- Content Area Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 relative">
        <!-- SPA Spinner Overlay (Scoped to Content) -->
        <div id="spa-spinner-overlay" class="absolute inset-0 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm z-[60] flex flex-col items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300">
            <?php
            $logo_url_spinner = isset($app_settings['app_logo']) && !empty($app_settings['app_logo']) ? base_url($app_settings['app_logo']) : base_url('assets/img/logo.png');
            ?>
            <div class="relative animate-flip-horizontal">
                <img src="<?= $logo_url_spinner ?>" alt="Loading..." class="w-16 h-16">
                <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/50 to-transparent opacity-0 animate-shine pointer-events-none"></div>
            </div>
            <div class="w-12 h-1 bg-black/20 rounded-full mt-2 animate-shadow-pulse"></div>
            <span class="mt-3 text-primary font-semibold text-sm tracking-wider loading-text">Memuat...</span>
        </div>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto content-wrapper">
        <!-- Maintenance Mode Banner for Admin -->
        <?php if (($app_settings['maintenance_mode'] ?? '0') === '1' && ($_SESSION['role'] ?? '') === 'admin'): ?>
        <div class="bg-yellow-500 text-white text-center px-4 py-2 text-sm font-bold shadow-md relative z-[60]">
            <i class="bi bi-cone-striped mr-2"></i> MODE MAINTENANCE AKTIF - User biasa tidak dapat mengakses sistem.
        </div>
        <?php endif; ?>
        <!-- Top Navbar -->
        <header class="sticky top-0 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-700 h-16 flex items-center justify-between px-4 lg:px-6 flex-shrink-0 z-50">
            <!-- Left side: Toggle & Title -->
            <div class="flex items-center gap-4">
                <?php if ($layout_mode === 'sidebar'): ?>
                <button onclick="toggleSidebar()" class="text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 focus:outline-none p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-200">
                    <i class="bi bi-list text-xl"></i>
                </button>
                <?php endif; ?>
                <div>
                    <h1 id="page-title" class="text-lg font-semibold text-slate-800 dark:text-white">Dashboard</h1>
                    <p id="page-subtitle" class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 hidden">Overview</p>
                </div>
            </div>

            <!-- Right side: Clock, Search, Profile -->
            <div class="flex items-center gap-2">
                <!-- Live Clock -->
                <div id="live-clock" class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-slate-50 dark:bg-slate-800 rounded-xl text-slate-600 dark:text-slate-300 text-sm font-medium">
                    <i class="bi bi-clock text-blue-500"></i>
                    <span id="clock-time">00:00</span>
                </div>
                
                <!-- Waffle Menu (Icon Menu Mode) -->
                <?php if ($layout_mode === 'icon_menu'): ?>
                <div class="relative" data-controller="dropdown">
                    <button id="waffle-menu-button" onclick="toggleDropdown(this)" class="p-2.5 rounded-xl text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 focus:outline-none transition-all duration-200">
                        <i class="bi bi-grid-3x3-gap-fill text-lg"></i>
                    </button>
                    <div class="dropdown-menu waffle-menu-dropdown hidden absolute right-0 mt-2 w-[480px] bg-white/95 dark:bg-slate-800/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 z-50 p-4 max-h-[80vh] overflow-y-auto">
                        <div class="mb-3 relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                                <i class="bi bi-search text-sm"></i>
                            </span>
                            <input type="text" id="icon-menu-search" class="w-full pl-9 pr-3 py-2 text-sm bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:text-white dark:placeholder-slate-400 transition-all" placeholder="Cari menu...">
                        </div>
                        <div class="grid grid-cols-3 md:grid-cols-4 gap-2" id="icon-menu-grid">
                            <?php require __DIR__ . '/_menu_items.php'; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Profile Dropdown -->
                <div class="relative" data-controller="dropdown">
                    <button onclick="toggleDropdown(this)" class="flex items-center gap-3 p-2 pr-3 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-200">
                        <div class="relative">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold text-sm shadow-lg">
                                <?php echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?>
                            </div>
                            <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white dark:border-slate-900"></div>
                        </div>
                        <div class="hidden md:block text-left">
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-200"><?= htmlspecialchars($_SESSION['username']) ?></p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 capitalize"><?= $_SESSION['role'] ?? 'User' ?></p>
                        </div>
                        <i class="bi bi-chevron-down text-xs text-slate-400 hidden md:block"></i>
                    </button>
                    <div class="dropdown-menu hidden absolute right-0 mt-2 w-72 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 z-50 overflow-hidden">
                        <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Tampilan</p>
                            <button onclick="setLayoutMode('sidebar')" class="w-full text-left flex items-center gap-3 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-all <?= $layout_mode === 'sidebar' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : '' ?>">
                                <i class="bi bi-layout-sidebar text-lg"></i> Sidebar
                            </button>
                            <button onclick="setLayoutMode('icon_menu')" class="w-full text-left flex items-center gap-3 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-all <?= $layout_mode === 'icon_menu' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : '' ?>">
                                <i class="bi bi-grid-fill text-lg"></i> Icon Menu
                            </button>
                        </div>
                        <div class="p-2">
                            <button id="theme-switcher" class="w-full text-left flex items-center gap-3 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-all">
                                <i class="bi bi-moon-stars text-lg"></i>
                                <span id="theme-switcher-text">Mode Gelap</span>
                            </button>
                            <div class="flex items-center justify-between px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-all">
                                <div class="flex items-center gap-3">
                                    <i class="bi bi-palette-fill text-lg"></i>
                                    <span>Warna Tema</span>
                                </div>
                                <input type="color" id="theme-color-picker" class="w-8 h-8 p-0 border-none rounded-lg cursor-pointer" value="#2563eb" title="Pilih warna tema">
                            </div>
                            <a href="<?= base_url('/my-profile/change-password') ?>" class="w-full text-left flex items-center gap-3 px-3 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-all">
                                <i class="bi bi-key-fill text-lg"></i> Ganti Password
                            </a>
                        </div>
                        <div class="p-2 border-t border-slate-200 dark:border-slate-700">
                            <a href="<?= base_url('/logout') ?>" data-spa-ignore id="logout-link" class="w-full text-left flex items-center gap-3 px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-xl transition-all">
                                <i class="bi bi-box-arrow-right text-lg"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Scrollable Area -->
        <main id="main-content" class="flex-1 p-4 sm:p-6">

<!-- Global Search Modal -->
<div id="globalSearchModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-start justify-center min-h-screen pt-16 px-4 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal('globalSearchModal')"></div>
        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <div class="p-4">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="bi bi-search text-gray-400"></i>
                    </span>
                    <input type="text" class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary" id="global-search-input" placeholder="Ketik disini apa yang dicari..." autocomplete="off">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3" id="global-search-spinner" style="display: none;">
                        <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-primary"></div>
                    </span>
                </div>
            </div>
            <div id="global-search-results" class="px-4 pb-4 max-h-[60vh] overflow-y-auto">
                <p class="text-gray-500 text-center py-8">Masukkan kata kunci untuk memulai pencarian.</p>
            </div>
        </div>
    </div>
</div>