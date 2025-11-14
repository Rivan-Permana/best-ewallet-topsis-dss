<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>SPK TOPSIS E-Wallet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
        }
        @media (max-width: 768px) {
            .sidebar-hidden {
                transform: translateX(-100%);
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar-transition w-64 bg-blue-900 text-white flex-shrink-0">
            <div class="p-6">
                <h1 class="text-2xl font-bold mb-2">SPK TOPSIS</h1>
                <p class="text-sm text-blue-300">E-Wallet Selection</p>
            </div>

            <nav class="px-4 space-y-2">
                <a href="index.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-800 transition <?php echo activeMenu('index.php'); ?>">
                    <i class="fas fa-home w-6"></i>
                    <span class="ml-3">Dashboard</span>
                </a>

                <a href="criteria.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-800 transition <?php echo activeMenu('criteria.php'); ?>">
                    <i class="fas fa-list-check w-6"></i>
                    <span class="ml-3">Kriteria</span>
                </a>

                <a href="alternatives.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-800 transition <?php echo activeMenu('alternatives.php'); ?>">
                    <i class="fas fa-wallet w-6"></i>
                    <span class="ml-3">Alternatif</span>
                </a>

                <a href="ratings.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-800 transition <?php echo activeMenu('ratings.php'); ?>">
                    <i class="fas fa-star w-6"></i>
                    <span class="ml-3">Penilaian</span>
                </a>

                <a href="calculate.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-800 transition <?php echo activeMenu('calculate.php'); ?>">
                    <i class="fas fa-calculator w-6"></i>
                    <span class="ml-3">Perhitungan</span>
                </a>

                <a href="results.php" class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-800 transition <?php echo activeMenu('results.php'); ?>">
                    <i class="fas fa-trophy w-6"></i>
                    <span class="ml-3">Hasil & Ranking</span>
                </a>
            </nav>

            <div class="absolute bottom-0 w-64 p-4 border-t border-blue-800">
                <div class="text-xs text-blue-300">
                    <p>TOPSIS Method</p>
                    <p class="mt-1">© 2025 SPK E-Wallet</p>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm z-10">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center">
                        <button onclick="toggleSidebar()" class="mr-4 md:hidden text-gray-600">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h2 class="text-xl font-semibold text-gray-800">
                            <?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?>
                        </h2>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-700">Administrator</p>
                            <p class="text-xs text-gray-500">SPK TOPSIS System</p>
                        </div>
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white"></i>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                <?php
                // Display flash messages
                $flash = getFlash();
                if ($flash):
                    $bgColor = $flash['type'] === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700';
                    $icon = $flash['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
                ?>
                <div class="<?php echo $bgColor; ?> border px-4 py-3 rounded mb-4 flex items-center" role="alert">
                    <i class="fas <?php echo $icon; ?> mr-3"></i>
                    <span><?php echo $flash['message']; ?></span>
                </div>
                <?php endif; ?>
