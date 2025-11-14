<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'classes/TOPSIS.php';

$pageTitle = "Hasil & Ranking";
$db = db();

// Get calculation results
$stmt = $db->query(
    "SELECT cr.*, a.code, a.name, a.description
    FROM calculation_results cr
    JOIN alternatives a ON cr.alternative_id = a.id
    ORDER BY cr.ranking ASC"
);
$results = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="space-y-6">
    <?php if (empty($results)): ?>
    <!-- No Results -->
    <div class="bg-white rounded-lg shadow-md p-12 text-center">
        <i class="fas fa-calculator text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">Belum Ada Hasil Perhitungan</h3>
        <p class="text-gray-600 mb-6">Silakan lakukan perhitungan TOPSIS terlebih dahulu untuk melihat hasil ranking.</p>
        <a href="calculate.php" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-calculator mr-2"></i>
            Mulai Perhitungan
        </a>
    </div>
    <?php else: ?>

    <!-- Winner Card -->
    <div class="bg-gradient-to-r from-yellow-400 to-yellow-600 rounded-lg shadow-lg p-8 text-center text-white">
        <i class="fas fa-trophy text-6xl mb-4"></i>
        <h2 class="text-3xl font-bold mb-2">E-Wallet Terbaik</h2>
        <h3 class="text-4xl font-bold mb-2"><?php echo $results[0]['name']; ?></h3>
        <p class="text-xl mb-1">Kode: <?php echo $results[0]['code']; ?></p>
        <p class="text-2xl font-semibold">Nilai Preferensi: <?php echo formatNumber($results[0]['preference_value'], 4); ?></p>
    </div>

    <!-- Top 3 Podium -->
    <?php if (count($results) >= 3): ?>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Second Place -->
        <div class="bg-white rounded-lg shadow-md p-6 transform md:translate-y-8">
            <div class="text-center">
                <div class="inline-block bg-gray-300 text-gray-900 w-16 h-16 rounded-full flex items-center justify-center mb-3">
                    <span class="text-2xl font-bold">2</span>
                </div>
                <h4 class="text-xl font-bold mb-2"><?php echo $results[1]['name']; ?></h4>
                <p class="text-sm text-gray-600 mb-2"><?php echo $results[1]['code']; ?></p>
                <p class="text-lg font-semibold text-blue-600"><?php echo formatNumber($results[1]['preference_value'], 4); ?></p>
            </div>
        </div>

        <!-- First Place -->
        <div class="bg-white rounded-lg shadow-md p-6 border-4 border-yellow-400">
            <div class="text-center">
                <div class="inline-block bg-yellow-400 text-yellow-900 w-20 h-20 rounded-full flex items-center justify-center mb-3">
                    <span class="text-3xl font-bold">1</span>
                </div>
                <h4 class="text-2xl font-bold mb-2"><?php echo $results[0]['name']; ?></h4>
                <p class="text-sm text-gray-600 mb-2"><?php echo $results[0]['code']; ?></p>
                <p class="text-xl font-semibold text-blue-600"><?php echo formatNumber($results[0]['preference_value'], 4); ?></p>
            </div>
        </div>

        <!-- Third Place -->
        <div class="bg-white rounded-lg shadow-md p-6 transform md:translate-y-16">
            <div class="text-center">
                <div class="inline-block bg-orange-400 text-orange-900 w-16 h-16 rounded-full flex items-center justify-center mb-3">
                    <span class="text-2xl font-bold">3</span>
                </div>
                <h4 class="text-xl font-bold mb-2"><?php echo $results[2]['name']; ?></h4>
                <p class="text-sm text-gray-600 mb-2"><?php echo $results[2]['code']; ?></p>
                <p class="text-lg font-semibold text-blue-600"><?php echo formatNumber($results[2]['preference_value'], 4); ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Full Ranking Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-6">
            <h3 class="text-xl font-semibold flex items-center">
                <i class="fas fa-list-ol mr-3"></i>
                Ranking Lengkap Semua Alternatif
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table id="resultsTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama E-Wallet</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">D+ (Positif)</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">D- (Negatif)</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Preferensi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($results as $result): ?>
                    <tr class="hover:bg-gray-50 <?php echo $result['ranking'] <= 3 ? 'bg-blue-50' : ''; ?>">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php echo getRankingBadge($result['ranking']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-mono bg-gray-100 text-gray-800 rounded">
                                <?php echo $result['code']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <div class="text-sm font-semibold text-gray-900"><?php echo $result['name']; ?></div>
                                <?php if ($result['description']): ?>
                                <div class="text-xs text-gray-500"><?php echo truncate($result['description'], 50); ?></div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            <?php echo formatNumber($result['positive_distance'], 6); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            <?php echo formatNumber($result['negative_distance'], 6); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full font-bold text-sm">
                                <?php echo formatNumber($result['preference_value'], 4); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Analysis & Interpretation -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-chart-line mr-3 text-blue-600"></i>
            Analisis & Interpretasi Hasil
        </h3>

        <div class="space-y-4">
            <div class="border-l-4 border-blue-600 pl-4">
                <h4 class="font-semibold text-gray-900 mb-2">Alternatif Terbaik</h4>
                <p class="text-gray-700">
                    Berdasarkan perhitungan TOPSIS, <strong><?php echo $results[0]['name']; ?> (<?php echo $results[0]['code']; ?>)</strong>
                    mendapatkan nilai preferensi tertinggi sebesar <strong><?php echo formatNumber($results[0]['preference_value'], 4); ?></strong>,
                    yang berarti alternatif ini memiliki jarak terdekat dengan solusi ideal positif dan jarak terjauh dari solusi ideal negatif.
                </p>
            </div>

            <div class="border-l-4 border-green-600 pl-4">
                <h4 class="font-semibold text-gray-900 mb-2">Interpretasi Nilai Preferensi</h4>
                <p class="text-gray-700">
                    Nilai preferensi berkisar antara 0 hingga 1. Semakin mendekati 1, semakin baik alternatif tersebut.
                    Nilai preferensi menunjukkan tingkat kedekatan relatif terhadap solusi ideal.
                </p>
            </div>

            <div class="border-l-4 border-purple-600 pl-4">
                <h4 class="font-semibold text-gray-900 mb-2">Perbandingan Alternatif</h4>
                <p class="text-gray-700">
                    Selisih nilai preferensi antara ranking 1 dan 2 adalah
                    <strong><?php echo formatNumber($results[0]['preference_value'] - $results[1]['preference_value'], 4); ?></strong>.
                    Hal ini menunjukkan tingkat perbedaan kualitas antara kedua alternatif tersebut.
                </p>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-wrap gap-4 justify-center">
        <button onclick="window.print()" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition flex items-center">
            <i class="fas fa-print mr-2"></i>
            Cetak Hasil
        </button>

        <button onclick="exportToCSV('resultsTable', 'topsis-ranking.csv')" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition flex items-center">
            <i class="fas fa-file-csv mr-2"></i>
            Export CSV
        </button>

        <a href="calculate.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition flex items-center">
            <i class="fas fa-redo mr-2"></i>
            Hitung Ulang
        </a>
    </div>

    <!-- Calculation Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-600 text-2xl mr-4 mt-1"></i>
            <div>
                <h4 class="font-semibold text-blue-900 mb-2">Tentang Hasil Perhitungan</h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• Hasil ini dihitung menggunakan metode TOPSIS (Technique for Order Preference by Similarity to Ideal Solution)</li>
                    <li>• Perhitungan mempertimbangkan semua kriteria dengan bobot yang telah ditentukan</li>
                    <li>• Nilai D+ menunjukkan jarak dari solusi ideal positif (semakin kecil semakin baik)</li>
                    <li>• Nilai D- menunjukkan jarak dari solusi ideal negatif (semakin besar semakin baik)</li>
                    <li>• Nilai preferensi adalah rasio D- terhadap total jarak (D+ + D-)</li>
                    <li>• Tanggal perhitungan: <?php echo !empty($results) ? formatDate($results[0]['calculation_date']) : '-'; ?></li>
                </ul>
            </div>
        </div>
    </div>

    <?php endif; ?>
</div>

<style>
@media print {
    .sidebar, header, .no-print {
        display: none !important;
    }
    body {
        background: white !important;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
