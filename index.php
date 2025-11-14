<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = "Dashboard";

// Get statistics
$db = db();

// Count criteria
$stmt = $db->query("SELECT COUNT(*) as count FROM criteria");
$criteriaCount = $stmt->fetch()['count'];

// Count alternatives
$stmt = $db->query("SELECT COUNT(*) as count FROM alternatives");
$alternativesCount = $stmt->fetch()['count'];

// Count ratings
$stmt = $db->query("SELECT COUNT(*) as count FROM ratings");
$ratingsCount = $stmt->fetch()['count'];

// Get latest calculation
$stmt = $db->query("SELECT COUNT(*) as count FROM calculation_results");
$hasResults = $stmt->fetch()['count'] > 0;

// Get top 3 alternatives if results exist
$topAlternatives = [];
if ($hasResults) {
    $stmt = $db->query(
        "SELECT cr.ranking, cr.preference_value, a.name, a.code
        FROM calculation_results cr
        JOIN alternatives a ON cr.alternative_id = a.id
        ORDER BY cr.ranking ASC
        LIMIT 3"
    );
    $topAlternatives = $stmt->fetchAll();
}

// Get recent activities (latest updates)
$stmt = $db->query(
    "SELECT 'alternative' as type, name, created_at FROM alternatives
    UNION ALL
    SELECT 'criteria' as type, name, created_at FROM criteria
    ORDER BY created_at DESC
    LIMIT 5"
);
$recentActivities = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="space-y-6">
    <!-- Welcome Card -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow-lg p-6 text-white">
        <h2 class="text-2xl font-bold mb-2">Selamat Datang di Sistem SPK TOPSIS</h2>
        <p class="text-blue-100">Sistem Pendukung Keputusan untuk Pemilihan E-Wallet Terbaik menggunakan Metode TOPSIS</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Criteria Card -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm mb-1">Total Kriteria</p>
                    <p class="text-3xl font-bold text-blue-600"><?php echo $criteriaCount; ?></p>
                </div>
                <div class="bg-blue-100 p-4 rounded-full">
                    <i class="fas fa-list-check text-2xl text-blue-600"></i>
                </div>
            </div>
            <a href="criteria.php" class="text-blue-600 text-sm mt-3 inline-block hover:underline">
                Lihat Detail <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        <!-- Alternatives Card -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm mb-1">Total Alternatif</p>
                    <p class="text-3xl font-bold text-green-600"><?php echo $alternativesCount; ?></p>
                </div>
                <div class="bg-green-100 p-4 rounded-full">
                    <i class="fas fa-wallet text-2xl text-green-600"></i>
                </div>
            </div>
            <a href="alternatives.php" class="text-green-600 text-sm mt-3 inline-block hover:underline">
                Lihat Detail <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        <!-- Ratings Card -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm mb-1">Total Penilaian</p>
                    <p class="text-3xl font-bold text-purple-600"><?php echo $ratingsCount; ?></p>
                </div>
                <div class="bg-purple-100 p-4 rounded-full">
                    <i class="fas fa-star text-2xl text-purple-600"></i>
                </div>
            </div>
            <a href="ratings.php" class="text-purple-600 text-sm mt-3 inline-block hover:underline">
                Lihat Detail <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        <!-- Results Card -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm mb-1">Status Perhitungan</p>
                    <p class="text-lg font-bold <?php echo $hasResults ? 'text-green-600' : 'text-orange-600'; ?>">
                        <?php echo $hasResults ? 'Tersedia' : 'Belum Ada'; ?>
                    </p>
                </div>
                <div class="<?php echo $hasResults ? 'bg-green-100' : 'bg-orange-100'; ?> p-4 rounded-full">
                    <i class="fas fa-calculator text-2xl <?php echo $hasResults ? 'text-green-600' : 'text-orange-600'; ?>"></i>
                </div>
            </div>
            <a href="<?php echo $hasResults ? 'results.php' : 'calculate.php'; ?>" class="<?php echo $hasResults ? 'text-green-600' : 'text-orange-600'; ?> text-sm mt-3 inline-block hover:underline">
                <?php echo $hasResults ? 'Lihat Hasil' : 'Hitung Sekarang'; ?> <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top 3 Results (if available) -->
        <?php if ($hasResults): ?>
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold mb-4 flex items-center">
                <i class="fas fa-trophy text-yellow-500 mr-2"></i>
                Top 3 E-Wallet Terbaik
            </h3>
            <div class="space-y-3">
                <?php foreach ($topAlternatives as $alt): ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex items-center space-x-3">
                        <?php echo getRankingBadge($alt['ranking']); ?>
                        <div>
                            <p class="font-semibold"><?php echo $alt['name']; ?></p>
                            <p class="text-sm text-gray-500"><?php echo $alt['code']; ?></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Nilai Preferensi</p>
                        <p class="font-bold text-blue-600"><?php echo formatNumber($alt['preference_value']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <a href="results.php" class="mt-4 block text-center text-blue-600 hover:underline">
                Lihat Semua Hasil <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <?php else: ?>
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold mb-4 flex items-center">
                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                Informasi
            </h3>
            <div class="text-center py-8">
                <i class="fas fa-calculator text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-600 mb-4">Belum ada hasil perhitungan TOPSIS</p>
                <a href="calculate.php" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-calculator mr-2"></i>
                    Mulai Perhitungan
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold mb-4 flex items-center">
                <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                Aksi Cepat
            </h3>
            <div class="grid grid-cols-2 gap-4">
                <a href="criteria.php?action=add" class="flex flex-col items-center justify-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                    <i class="fas fa-plus-circle text-3xl text-blue-600 mb-2"></i>
                    <span class="text-sm font-semibold text-blue-600">Tambah Kriteria</span>
                </a>

                <a href="alternatives.php?action=add" class="flex flex-col items-center justify-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
                    <i class="fas fa-plus-circle text-3xl text-green-600 mb-2"></i>
                    <span class="text-sm font-semibold text-green-600">Tambah Alternatif</span>
                </a>

                <a href="ratings.php" class="flex flex-col items-center justify-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                    <i class="fas fa-edit text-3xl text-purple-600 mb-2"></i>
                    <span class="text-sm font-semibold text-purple-600">Input Penilaian</span>
                </a>

                <a href="calculate.php" class="flex flex-col items-center justify-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition">
                    <i class="fas fa-calculator text-3xl text-orange-600 mb-2"></i>
                    <span class="text-sm font-semibold text-orange-600">Hitung TOPSIS</span>
                </a>
            </div>
        </div>
    </div>

    <!-- About TOPSIS Method -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-bold mb-4 flex items-center">
            <i class="fas fa-book text-blue-500 mr-2"></i>
            Tentang Metode TOPSIS
        </h3>
        <div class="prose max-w-none">
            <p class="text-gray-700 mb-3">
                <strong>TOPSIS</strong> (Technique for Order Preference by Similarity to Ideal Solution) adalah salah satu metode pengambilan keputusan multi-kriteria yang dikembangkan oleh Yoon dan Hwang pada tahun 1981.
            </p>
            <p class="text-gray-700 mb-3">
                Metode ini bekerja berdasarkan konsep bahwa <strong>alternatif terbaik</strong> tidak hanya memiliki jarak terpendek dari solusi ideal positif, tetapi juga memiliki jarak terjauh dari solusi ideal negatif.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-blue-900 mb-2"><i class="fas fa-check-circle mr-2"></i>Kelebihan</h4>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Konsep sederhana dan mudah dipahami</li>
                        <li>• Komputasi efisien</li>
                        <li>• Hasil akurat dan terukur</li>
                    </ul>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-green-900 mb-2"><i class="fas fa-list-ol mr-2"></i>Langkah Perhitungan</h4>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>1. Normalisasi matriks</li>
                        <li>2. Matriks ternormalisasi terbobot</li>
                        <li>3. Solusi ideal positif & negatif</li>
                        <li>4. Jarak & nilai preferensi</li>
                    </ul>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-purple-900 mb-2"><i class="fas fa-chart-line mr-2"></i>Hasil</h4>
                    <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Ranking alternatif terbaik</li>
                        <li>• Nilai preferensi (0-1)</li>
                        <li>• Keputusan objektif</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
