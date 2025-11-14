<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'classes/TOPSIS.php';

$pageTitle = "Perhitungan TOPSIS";
$db = db();

$calculationDone = false;
$error = null;
$details = null;

// Handle calculation request
if (isPost() && post('action') === 'calculate') {
    try {
        $topsis = new TOPSIS();
        $topsis->calculate();
        $details = $topsis->getCalculationDetails();
        $calculationDone = true;
        setFlash('success', 'Perhitungan TOPSIS berhasil dilakukan!');
    } catch (Exception $e) {
        $error = $e->getMessage();
        setFlash('error', 'Perhitungan gagal: ' . $e->getMessage());
    }
}

// Check if data is complete
$stmt = $db->query("SELECT COUNT(*) as count FROM criteria");
$criteriaCount = $stmt->fetch()['count'];

$stmt = $db->query("SELECT COUNT(*) as count FROM alternatives");
$alternativesCount = $stmt->fetch()['count'];

$stmt = $db->query("SELECT COUNT(*) as count FROM ratings");
$ratingsCount = $stmt->fetch()['count'];

$expectedRatings = $criteriaCount * $alternativesCount;
$isDataComplete = ($criteriaCount > 0 && $alternativesCount > 0 && $ratingsCount == $expectedRatings);

include 'includes/header.php';
?>

<div class="space-y-6">
    <!-- Status Check -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Status Kelengkapan Data</h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="border rounded-lg p-4 <?php echo $criteriaCount > 0 ? 'border-green-300 bg-green-50' : 'border-red-300 bg-red-50'; ?>">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Kriteria</p>
                        <p class="text-2xl font-bold <?php echo $criteriaCount > 0 ? 'text-green-600' : 'text-red-600'; ?>">
                            <?php echo $criteriaCount; ?>
                        </p>
                    </div>
                    <i class="fas <?php echo $criteriaCount > 0 ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500'; ?> text-3xl"></i>
                </div>
            </div>

            <div class="border rounded-lg p-4 <?php echo $alternativesCount > 0 ? 'border-green-300 bg-green-50' : 'border-red-300 bg-red-50'; ?>">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Alternatif</p>
                        <p class="text-2xl font-bold <?php echo $alternativesCount > 0 ? 'text-green-600' : 'text-red-600'; ?>">
                            <?php echo $alternativesCount; ?>
                        </p>
                    </div>
                    <i class="fas <?php echo $alternativesCount > 0 ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500'; ?> text-3xl"></i>
                </div>
            </div>

            <div class="border rounded-lg p-4 <?php echo $ratingsCount == $expectedRatings ? 'border-green-300 bg-green-50' : 'border-red-300 bg-red-50'; ?>">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Penilaian</p>
                        <p class="text-2xl font-bold <?php echo $ratingsCount == $expectedRatings ? 'text-green-600' : 'text-red-600'; ?>">
                            <?php echo $ratingsCount; ?> / <?php echo $expectedRatings; ?>
                        </p>
                    </div>
                    <i class="fas <?php echo $ratingsCount == $expectedRatings ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500'; ?> text-3xl"></i>
                </div>
            </div>
        </div>

        <?php if ($isDataComplete): ?>
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-xl mr-3"></i>
                <p>Data lengkap! Anda dapat melakukan perhitungan TOPSIS.</p>
            </div>
        </div>

        <form method="POST" onsubmit="return confirm('Lakukan perhitungan TOPSIS sekarang?')">
            <input type="hidden" name="action" value="calculate">
            <button type="submit" class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition flex items-center justify-center">
                <i class="fas fa-calculator mr-2"></i>
                Hitung Menggunakan Metode TOPSIS
            </button>
        </form>
        <?php else: ?>
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-xl mr-3"></i>
                <div>
                    <p class="font-semibold">Data Belum Lengkap</p>
                    <p class="text-sm mt-1">
                        Pastikan semua data (kriteria, alternatif, dan penilaian) telah diisi sebelum melakukan perhitungan.
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($calculationDone && $details): ?>
    <!-- Calculation Steps -->
    <div class="space-y-6">
        <!-- Step 1: Decision Matrix -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-blue-600 text-white p-4">
                <h4 class="font-semibold flex items-center">
                    <span class="bg-white text-blue-600 w-8 h-8 rounded-full flex items-center justify-center mr-3">1</span>
                    Matriks Keputusan (X)
                </h4>
            </div>
            <div class="p-6 overflow-x-auto">
                <table class="min-w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="border border-gray-300 px-4 py-2">Alternatif</th>
                            <?php foreach ($details['criteria'] as $crit): ?>
                            <th class="border border-gray-300 px-4 py-2"><?php echo $crit['code']; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($details['alternatives'] as $alt): ?>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 font-semibold"><?php echo $alt['code']; ?></td>
                            <?php foreach ($details['criteria'] as $crit): ?>
                            <td class="border border-gray-300 px-4 py-2 text-center">
                                <?php echo formatNumber($details['decision_matrix'][$alt['id']][$crit['id']], 2); ?>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Step 2: Normalized Matrix -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-green-600 text-white p-4">
                <h4 class="font-semibold flex items-center">
                    <span class="bg-white text-green-600 w-8 h-8 rounded-full flex items-center justify-center mr-3">2</span>
                    Matriks Ternormalisasi (R)
                </h4>
            </div>
            <div class="p-6 overflow-x-auto">
                <table class="min-w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="border border-gray-300 px-4 py-2">Alternatif</th>
                            <?php foreach ($details['criteria'] as $crit): ?>
                            <th class="border border-gray-300 px-4 py-2"><?php echo $crit['code']; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($details['alternatives'] as $alt): ?>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 font-semibold"><?php echo $alt['code']; ?></td>
                            <?php foreach ($details['criteria'] as $crit): ?>
                            <td class="border border-gray-300 px-4 py-2 text-center">
                                <?php echo formatNumber($details['normalized_matrix'][$alt['id']][$crit['id']], 4); ?>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Step 3: Weighted Normalized Matrix -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-purple-600 text-white p-4">
                <h4 class="font-semibold flex items-center">
                    <span class="bg-white text-purple-600 w-8 h-8 rounded-full flex items-center justify-center mr-3">3</span>
                    Matriks Ternormalisasi Terbobot (Y)
                </h4>
            </div>
            <div class="p-6 overflow-x-auto">
                <table class="min-w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="border border-gray-300 px-4 py-2">Alternatif</th>
                            <?php foreach ($details['criteria'] as $crit): ?>
                            <th class="border border-gray-300 px-4 py-2">
                                <?php echo $crit['code']; ?><br>
                                <span class="text-xs font-normal">(w=<?php echo formatNumber($crit['weight'], 4); ?>)</span>
                            </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($details['alternatives'] as $alt): ?>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 font-semibold"><?php echo $alt['code']; ?></td>
                            <?php foreach ($details['criteria'] as $crit): ?>
                            <td class="border border-gray-300 px-4 py-2 text-center">
                                <?php echo formatNumber($details['weighted_matrix'][$alt['id']][$crit['id']], 4); ?>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Step 4: Ideal Solutions -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-orange-600 text-white p-4">
                <h4 class="font-semibold flex items-center">
                    <span class="bg-white text-orange-600 w-8 h-8 rounded-full flex items-center justify-center mr-3">4</span>
                    Solusi Ideal Positif (A+) dan Negatif (A-)
                </h4>
            </div>
            <div class="p-6 overflow-x-auto">
                <table class="min-w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="border border-gray-300 px-4 py-2">Solusi</th>
                            <?php foreach ($details['criteria'] as $crit): ?>
                            <th class="border border-gray-300 px-4 py-2"><?php echo $crit['code']; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-green-50">
                            <td class="border border-gray-300 px-4 py-2 font-semibold">A+ (Positif)</td>
                            <?php foreach ($details['criteria'] as $crit): ?>
                            <td class="border border-gray-300 px-4 py-2 text-center font-semibold text-green-700">
                                <?php echo formatNumber($details['ideal_positive'][$crit['id']], 4); ?>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <tr class="bg-red-50">
                            <td class="border border-gray-300 px-4 py-2 font-semibold">A- (Negatif)</td>
                            <?php foreach ($details['criteria'] as $crit): ?>
                            <td class="border border-gray-300 px-4 py-2 text-center font-semibold text-red-700">
                                <?php echo formatNumber($details['ideal_negative'][$crit['id']], 4); ?>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Step 5: Distances and Preferences -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-indigo-600 text-white p-4">
                <h4 class="font-semibold flex items-center">
                    <span class="bg-white text-indigo-600 w-8 h-8 rounded-full flex items-center justify-center mr-3">5</span>
                    Jarak dan Nilai Preferensi
                </h4>
            </div>
            <div class="p-6 overflow-x-auto">
                <table class="min-w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="border border-gray-300 px-4 py-2">Alternatif</th>
                            <th class="border border-gray-300 px-4 py-2">D+ (Positif)</th>
                            <th class="border border-gray-300 px-4 py-2">D- (Negatif)</th>
                            <th class="border border-gray-300 px-4 py-2">Nilai Preferensi (V)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Sort by preference value
                        $sortedPreferences = $details['preferences'];
                        arsort($sortedPreferences);
                        foreach ($sortedPreferences as $altId => $preference):
                            $alt = array_filter($details['alternatives'], function($a) use ($altId) {
                                return $a['id'] == $altId;
                            });
                            $alt = reset($alt);
                        ?>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 font-semibold"><?php echo $alt['code']; ?> - <?php echo $alt['name']; ?></td>
                            <td class="border border-gray-300 px-4 py-2 text-center">
                                <?php echo formatNumber($details['distances'][$altId]['positive'], 6); ?>
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-center">
                                <?php echo formatNumber($details['distances'][$altId]['negative'], 6); ?>
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-center font-bold text-blue-600">
                                <?php echo formatNumber($preference, 6); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Action Button -->
        <div class="flex justify-center">
            <a href="results.php" class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 transition flex items-center text-lg">
                <i class="fas fa-trophy mr-3"></i>
                Lihat Hasil Ranking Final
                <i class="fas fa-arrow-right ml-3"></i>
            </a>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
