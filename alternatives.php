<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = "Manajemen Alternatif";
$db = db();

// Handle form submissions
if (isPost()) {
    $action = post('action');

    if ($action === 'add') {
        $code = clean(post('code'));
        $name = clean(post('name'));
        $description = clean(post('description'));

        $errors = validateRequired([
            'code' => 'Kode Alternatif',
            'name' => 'Nama E-Wallet'
        ], $_POST);

        if (empty($errors)) {
            try {
                $db->query(
                    "INSERT INTO alternatives (code, name, description) VALUES (?, ?, ?)",
                    [$code, $name, $description]
                );
                setFlash('success', 'Alternatif berhasil ditambahkan!');
                redirect('alternatives.php');
            } catch (Exception $e) {
                if (strpos($e->getMessage(), 'UNIQUE') !== false) {
                    $errors[] = 'Kode alternatif sudah digunakan!';
                } else {
                    $errors[] = $e->getMessage();
                }
            }
        }
    } elseif ($action === 'edit') {
        $id = intval(post('id'));
        $code = clean(post('code'));
        $name = clean(post('name'));
        $description = clean(post('description'));

        $errors = validateRequired([
            'code' => 'Kode Alternatif',
            'name' => 'Nama E-Wallet'
        ], $_POST);

        if (empty($errors)) {
            try {
                $db->query(
                    "UPDATE alternatives SET code=?, name=?, description=?, updated_at=CURRENT_TIMESTAMP
                    WHERE id=?",
                    [$code, $name, $description, $id]
                );
                setFlash('success', 'Alternatif berhasil diupdate!');
                redirect('alternatives.php');
            } catch (Exception $e) {
                if (strpos($e->getMessage(), 'UNIQUE') !== false) {
                    $errors[] = 'Kode alternatif sudah digunakan!';
                } else {
                    $errors[] = $e->getMessage();
                }
            }
        }
    } elseif ($action === 'delete') {
        $id = intval(post('id'));
        try {
            $db->query("DELETE FROM alternatives WHERE id=?", [$id]);
            setFlash('success', 'Alternatif berhasil dihapus!');
            redirect('alternatives.php');
        } catch (Exception $e) {
            setFlash('error', 'Gagal menghapus alternatif: ' . $e->getMessage());
            redirect('alternatives.php');
        }
    }
}

// Get action from URL
$action = get('action');
$editData = null;

if ($action === 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $db->query("SELECT * FROM alternatives WHERE id=?", [$id]);
    $editData = $stmt->fetch();
    if (!$editData) {
        setFlash('error', 'Data alternatif tidak ditemukan!');
        redirect('alternatives.php');
    }
}

// Pagination and search
$search = getSearchQuery();
$currentPage = getCurrentPage();
$perPage = 10;

// Count total records
$countSql = "SELECT COUNT(*) as count FROM alternatives";
if ($search) {
    $countSql .= " WHERE code LIKE ? OR name LIKE ? OR description LIKE ?";
    $stmt = $db->query($countSql, ["%$search%", "%$search%", "%$search%"]);
} else {
    $stmt = $db->query($countSql);
}
$totalRecords = $stmt->fetch()['count'];

$pagination = getPagination($totalRecords, $currentPage, $perPage);

// Get alternatives data
$sql = "SELECT * FROM alternatives";
if ($search) {
    $sql .= " WHERE code LIKE ? OR name LIKE ? OR description LIKE ?";
}
$sql .= " ORDER BY code LIMIT ? OFFSET ?";

if ($search) {
    $stmt = $db->query($sql, ["%$search%", "%$search%", "%$search%", $perPage, $pagination['offset']]);
} else {
    $stmt = $db->query($sql, [$perPage, $pagination['offset']]);
}
$alternatives = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-xl font-semibold text-gray-800">Daftar Alternatif E-Wallet</h3>
            <p class="text-sm text-gray-600 mt-1">Kelola data alternatif untuk perhitungan TOPSIS</p>
        </div>
        <button onclick="showModal('addModal')" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Tambah Alternatif
        </button>
    </div>

    <!-- Search Bar -->
    <div class="bg-white p-4 rounded-lg shadow-md">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                   placeholder="Cari kode, nama, atau deskripsi..."
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-search mr-2"></i>Cari
            </button>
            <?php if ($search): ?>
            <a href="alternatives.php" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">
                <i class="fas fa-times mr-2"></i>Reset
            </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Alternatives Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama E-Wallet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($alternatives)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-wallet text-4xl mb-2"></i>
                            <p>Tidak ada data alternatif</p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($alternatives as $index => $item): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo $pagination['offset'] + $index + 1; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-mono bg-green-100 text-green-800 rounded">
                                    <?php echo $item['code']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                <?php echo $item['name']; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <?php echo truncate($item['description'] ?? '-', 60); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="alternatives.php?action=edit&id=<?php echo $item['id']; ?>"
                                   class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" class="inline" onsubmit="return confirmDelete()">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['total_pages'] > 1): ?>
        <div class="bg-gray-50 px-6 py-3 flex items-center justify-between border-t border-gray-200">
            <div class="text-sm text-gray-700">
                Menampilkan <?php echo $pagination['offset'] + 1; ?> -
                <?php echo min($pagination['offset'] + $perPage, $totalRecords); ?>
                dari <?php echo $totalRecords; ?> data
            </div>
            <div class="flex space-x-2">
                <?php if ($pagination['has_previous']): ?>
                <a href="?<?php echo buildPaginationQuery($currentPage - 1, $search); ?>"
                   class="px-3 py-1 bg-white border border-gray-300 rounded hover:bg-gray-50">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                    <?php if ($i == $currentPage): ?>
                    <span class="px-3 py-1 bg-green-600 text-white rounded"><?php echo $i; ?></span>
                    <?php elseif ($i == 1 || $i == $pagination['total_pages'] || abs($i - $currentPage) <= 2): ?>
                    <a href="?<?php echo buildPaginationQuery($i, $search); ?>"
                       class="px-3 py-1 bg-white border border-gray-300 rounded hover:bg-gray-50">
                        <?php echo $i; ?>
                    </a>
                    <?php elseif (abs($i - $currentPage) == 3): ?>
                    <span class="px-3 py-1">...</span>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($pagination['has_next']): ?>
                <a href="?<?php echo buildPaginationQuery($currentPage + 1, $search); ?>"
                   class="px-3 py-1 bg-white border border-gray-300 rounded hover:bg-gray-50">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">
                <?php echo $editData ? 'Edit Alternatif' : 'Tambah Alternatif Baru'; ?>
            </h3>
            <button onclick="hideModal('addModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <?php if (isset($errors)): ?>
        <?php echo displayErrors($errors); ?>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="<?php echo $editData ? 'edit' : 'add'; ?>">
            <?php if ($editData): ?>
            <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
            <?php endif; ?>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Alternatif</label>
                <input type="text" name="code" required maxlength="10"
                       value="<?php echo $editData ? $editData['code'] : ''; ?>"
                       placeholder="Contoh: A1, A2"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama E-Wallet</label>
                <input type="text" name="name" required maxlength="100"
                       value="<?php echo $editData ? $editData['name'] : ''; ?>"
                       placeholder="Contoh: Dana, OVO, GoPay"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="3"
                          placeholder="Deskripsi singkat tentang e-wallet..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"><?php echo $editData ? $editData['description'] : ''; ?></textarea>
            </div>

            <div class="flex justify-end space-x-2 pt-4">
                <button type="button" onclick="hideModal('addModal')"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-save mr-2"></i><?php echo $editData ? 'Update' : 'Simpan'; ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function hideModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

<?php if ($action === 'add' || $action === 'edit'): ?>
    showModal('addModal');
<?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>
