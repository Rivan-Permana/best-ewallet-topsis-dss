<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = "Manajemen Penilaian";
$db = db();

// Handle form submission
if (isPost()) {
    try {
        $db->beginTransaction();

        foreach ($_POST['ratings'] as $altId => $critRatings) {
            foreach ($critRatings as $critId => $value) {
                $value = floatval($value);

                // Check if rating exists
                $stmt = $db->query(
                    "SELECT id FROM ratings WHERE alternative_id=? AND criteria_id=?",
                    [$altId, $critId]
                );
                $existing = $stmt->fetch();

                if ($existing) {
                    // Update existing rating
                    $db->query(
                        "UPDATE ratings SET value=?, updated_at=CURRENT_TIMESTAMP
                        WHERE alternative_id=? AND criteria_id=?",
                        [$value, $altId, $critId]
                    );
                } else {
                    // Insert new rating
                    $db->query(
                        "INSERT INTO ratings (alternative_id, criteria_id, value) VALUES (?, ?, ?)",
                        [$altId, $critId, $value]
                    );
                }
            }
        }

        $db->commit();
        setFlash('success', 'Data penilaian berhasil disimpan!');
        redirect('ratings.php');
    } catch (Exception $e) {
        $db->rollBack();
        setFlash('error', 'Gagal menyimpan penilaian: ' . $e->getMessage());
    }
}

// Get all criteria
$stmt = $db->query("SELECT * FROM criteria ORDER BY code");
$criteria = $stmt->fetchAll();

// Get all alternatives
$stmt = $db->query("SELECT * FROM alternatives ORDER BY code");
$alternatives = $stmt->fetchAll();

// Get existing ratings in a matrix format
$ratings = [];
foreach ($alternatives as $alt) {
    $ratings[$alt['id']] = [];
    foreach ($criteria as $crit) {
        $stmt = $db->query(
            "SELECT value FROM ratings WHERE alternative_id=? AND criteria_id=?",
            [$alt['id'], $crit['id']]
        );
        $rating = $stmt->fetch();
        $ratings[$alt['id']][$crit['id']] = $rating ? $rating['value'] : '';
    }
}

include 'includes/header.php';
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-2">Input Penilaian Alternatif</h3>
        <p class="text-sm text-gray-600">Masukkan nilai untuk setiap alternatif berdasarkan kriteria yang telah ditentukan</p>
    </div>

    <?php if (empty($criteria) || empty($alternatives)): ?>
    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-6 py-4 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle text-2xl mr-3"></i>
            <div>
                <p class="font-semibold">Data Tidak Lengkap</p>
                <p class="text-sm mt-1">
                    <?php if (empty($criteria)): ?>
                    Belum ada data kriteria. <a href="criteria.php" class="underline">Tambah kriteria</a> terlebih dahulu.
                    <?php endif; ?>
                    <?php if (empty($alternatives)): ?>
                    Belum ada data alternatif. <a href="alternatives.php" class="underline">Tambah alternatif</a> terlebih dahulu.
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
    <?php else: ?>

    <!-- Rating Matrix Form -->
    <form method="POST" class="space-y-6">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-4 bg-gray-50 border-b">
                <h4 class="font-semibold text-gray-800">Matriks Penilaian</h4>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50 z-10">
                                Alternatif / Kriteria
                            </th>
                            <?php foreach ($criteria as $crit): ?>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="font-mono text-sm mb-1"><?php echo $crit['code']; ?></div>
                                <div class="font-normal text-xs text-gray-600 normal-case"><?php echo truncate($crit['name'], 20); ?></div>
                                <div class="mt-1">
                                    <?php echo getCriteriaTypeBadge($crit['type']); ?>
                                </div>
                            </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($alternatives as $alt): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 sticky left-0 bg-white z-10">
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-1 text-xs font-mono bg-green-100 text-green-800 rounded">
                                        <?php echo $alt['code']; ?>
                                    </span>
                                    <span><?php echo $alt['name']; ?></span>
                                </div>
                            </td>
                            <?php foreach ($criteria as $crit): ?>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <input type="number"
                                       name="ratings[<?php echo $alt['id']; ?>][<?php echo $crit['id']; ?>]"
                                       value="<?php echo $ratings[$alt['id']][$crit['id']]; ?>"
                                       step="0.0001"
                                       required
                                       placeholder="0"
                                       class="w-24 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-center">
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="p-6 bg-gray-50 border-t">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-2"></i>
                        Pastikan semua nilai telah diisi dengan benar sebelum menyimpan
                    </div>
                    <button type="submit" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Semua Penilaian
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Criteria Weight Reference -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4 bg-gray-50 border-b">
            <h4 class="font-semibold text-gray-800">Referensi Bobot Kriteria</h4>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($criteria as $crit): ?>
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-start mb-2">
                        <span class="px-2 py-1 text-xs font-mono bg-gray-100 text-gray-800 rounded">
                            <?php echo $crit['code']; ?>
                        </span>
                        <?php echo getCriteriaTypeBadge($crit['type']); ?>
                    </div>
                    <h5 class="font-semibold text-gray-900 mb-1"><?php echo $crit['name']; ?></h5>
                    <p class="text-sm text-gray-600">Bobot: <span class="font-semibold text-blue-600"><?php echo formatNumber($crit['weight']); ?></span></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Value Guidelines -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h4 class="font-semibold text-blue-900 mb-3 flex items-center">
            <i class="fas fa-lightbulb mr-2"></i>
            Panduan Penilaian
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-blue-800">
            <div>
                <p class="font-semibold mb-2">Skala Penilaian yang Disarankan:</p>
                <ul class="space-y-1 ml-4">
                    <li>• <strong>4</strong> - Sangat Baik / Sangat Tinggi</li>
                    <li>• <strong>3</strong> - Baik / Tinggi</li>
                    <li>• <strong>2</strong> - Cukup / Sedang</li>
                    <li>• <strong>1</strong> - Kurang / Rendah</li>
                </ul>
            </div>
            <div>
                <p class="font-semibold mb-2">Catatan Penting:</p>
                <ul class="space-y-1 ml-4">
                    <li>• Untuk kriteria <strong>Benefit</strong>: nilai lebih tinggi lebih baik</li>
                    <li>• Untuk kriteria <strong>Cost</strong>: nilai lebih rendah lebih baik</li>
                    <li>• Gunakan nilai desimal jika diperlukan (contoh: 2.5, 3.75)</li>
                    <li>• Untuk biaya, gunakan nilai aktual (contoh: 2500 untuk Rp 2.500)</li>
                </ul>
            </div>
        </div>
    </div>

    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
