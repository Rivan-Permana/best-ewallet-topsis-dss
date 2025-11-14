<?php
/**
 * System Check Script
 * Verify that all requirements are met
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Check - SPK TOPSIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-check-circle text-green-600 mr-3"></i>
                System Requirements Check
            </h1>

            <?php
            $checks = [];
            $allPassed = true;

            // Check PHP Version
            $phpVersion = PHP_VERSION;
            $phpOK = version_compare($phpVersion, '7.4.0', '>=');
            $checks[] = [
                'name' => 'PHP Version',
                'status' => $phpOK,
                'message' => $phpOK ? "PHP $phpVersion (OK)" : "PHP $phpVersion (Requires 7.4+)",
                'required' => true
            ];
            if (!$phpOK) $allPassed = false;

            // Check SQLite
            $sqliteOK = extension_loaded('sqlite3');
            $checks[] = [
                'name' => 'SQLite Extension',
                'status' => $sqliteOK,
                'message' => $sqliteOK ? 'SQLite3 extension loaded' : 'SQLite3 extension not found',
                'required' => true
            ];
            if (!$sqliteOK) $allPassed = false;

            // Check PDO
            $pdoOK = extension_loaded('pdo');
            $checks[] = [
                'name' => 'PDO Extension',
                'status' => $pdoOK,
                'message' => $pdoOK ? 'PDO extension loaded' : 'PDO extension not found',
                'required' => true
            ];
            if (!$pdoOK) $allPassed = false;

            // Check PDO SQLite
            $pdoSqliteOK = extension_loaded('pdo_sqlite');
            $checks[] = [
                'name' => 'PDO SQLite Driver',
                'status' => $pdoSqliteOK,
                'message' => $pdoSqliteOK ? 'PDO SQLite driver loaded' : 'PDO SQLite driver not found',
                'required' => true
            ];
            if (!$pdoSqliteOK) $allPassed = false;

            // Check database directory
            $dbDir = __DIR__ . '/database';
            $dbDirOK = is_dir($dbDir);
            $checks[] = [
                'name' => 'Database Directory',
                'status' => $dbDirOK,
                'message' => $dbDirOK ? 'Database directory exists' : 'Database directory not found',
                'required' => true
            ];
            if (!$dbDirOK) $allPassed = false;

            // Check database directory writable
            $dbWritable = is_writable($dbDir);
            $checks[] = [
                'name' => 'Database Directory Writable',
                'status' => $dbWritable,
                'message' => $dbWritable ? 'Database directory is writable' : 'Database directory is not writable',
                'required' => true
            ];
            if (!$dbWritable) $allPassed = false;

            // Check init.sql exists
            $initSQL = $dbDir . '/init.sql';
            $initOK = file_exists($initSQL);
            $checks[] = [
                'name' => 'Database Schema',
                'status' => $initOK,
                'message' => $initOK ? 'init.sql file exists' : 'init.sql file not found',
                'required' => true
            ];
            if (!$initOK) $allPassed = false;

            // Check session
            $sessionOK = session_status() !== PHP_SESSION_DISABLED;
            $checks[] = [
                'name' => 'Session Support',
                'status' => $sessionOK,
                'message' => $sessionOK ? 'Session support enabled' : 'Session support disabled',
                'required' => true
            ];
            if (!$sessionOK) $allPassed = false;

            // Check required files
            $requiredFiles = [
                'index.php',
                'criteria.php',
                'alternatives.php',
                'ratings.php',
                'calculate.php',
                'results.php',
                'config/database.php',
                'classes/TOPSIS.php',
                'includes/functions.php',
                'includes/header.php',
                'includes/footer.php'
            ];

            $missingFiles = [];
            foreach ($requiredFiles as $file) {
                if (!file_exists(__DIR__ . '/' . $file)) {
                    $missingFiles[] = $file;
                }
            }

            $filesOK = empty($missingFiles);
            $checks[] = [
                'name' => 'Required Files',
                'status' => $filesOK,
                'message' => $filesOK ? 'All required files present' : 'Missing files: ' . implode(', ', $missingFiles),
                'required' => true
            ];
            if (!$filesOK) $allPassed = false;

            // Display results
            foreach ($checks as $check) {
                $bgColor = $check['status'] ? 'bg-green-50' : 'bg-red-50';
                $borderColor = $check['status'] ? 'border-green-200' : 'border-red-200';
                $textColor = $check['status'] ? 'text-green-800' : 'text-red-800';
                $icon = $check['status'] ? 'fa-check-circle' : 'fa-times-circle';
                $iconColor = $check['status'] ? 'text-green-600' : 'text-red-600';

                echo "<div class='mb-4 p-4 border rounded-lg $bgColor $borderColor'>";
                echo "<div class='flex items-center justify-between'>";
                echo "<div class='flex items-center'>";
                echo "<i class='fas $icon $iconColor text-2xl mr-3'></i>";
                echo "<div>";
                echo "<h3 class='font-semibold $textColor'>{$check['name']}</h3>";
                echo "<p class='text-sm $textColor'>{$check['message']}</p>";
                echo "</div>";
                echo "</div>";
                if ($check['required']) {
                    echo "<span class='text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded'>Required</span>";
                }
                echo "</div>";
                echo "</div>";
            }
            ?>

            <div class="mt-8 p-6 rounded-lg <?php echo $allPassed ? 'bg-green-100 border-2 border-green-400' : 'bg-red-100 border-2 border-red-400'; ?>">
                <?php if ($allPassed): ?>
                    <h2 class="text-2xl font-bold text-green-800 mb-2 flex items-center">
                        <i class="fas fa-check-circle mr-3"></i>
                        All Checks Passed!
                    </h2>
                    <p class="text-green-700 mb-4">Your system meets all requirements. You can start using the application.</p>
                    <a href="index.php" class="inline-block bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-arrow-right mr-2"></i>
                        Go to Dashboard
                    </a>
                <?php else: ?>
                    <h2 class="text-2xl font-bold text-red-800 mb-2 flex items-center">
                        <i class="fas fa-exclamation-circle mr-3"></i>
                        Some Checks Failed
                    </h2>
                    <p class="text-red-700 mb-4">Please fix the issues above before using the application.</p>

                    <div class="bg-white p-4 rounded mt-4">
                        <h3 class="font-semibold text-gray-800 mb-2">Common Solutions:</h3>
                        <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                            <li><strong>SQLite not found:</strong> Install php-sqlite3 extension</li>
                            <li><strong>Directory not writable:</strong> Run: <code class="bg-gray-200 px-2 py-1 rounded">chmod 777 database</code></li>
                            <li><strong>Missing files:</strong> Re-extract the complete project</li>
                        </ul>
                    </div>

                    <button onclick="location.reload()" class="mt-4 bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-redo mr-2"></i>
                        Check Again
                    </button>
                <?php endif; ?>
            </div>

            <div class="mt-8 border-t pt-6">
                <h3 class="font-semibold text-gray-800 mb-3">System Information</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">PHP Version:</span>
                        <span class="font-mono ml-2"><?php echo PHP_VERSION; ?></span>
                    </div>
                    <div>
                        <span class="text-gray-600">Server Software:</span>
                        <span class="font-mono ml-2"><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></span>
                    </div>
                    <div>
                        <span class="text-gray-600">Operating System:</span>
                        <span class="font-mono ml-2"><?php echo PHP_OS; ?></span>
                    </div>
                    <div>
                        <span class="text-gray-600">Max Execution Time:</span>
                        <span class="font-mono ml-2"><?php echo ini_get('max_execution_time'); ?>s</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 text-center text-sm text-gray-600">
                <p>SPK TOPSIS E-Wallet Selection System v1.0.0</p>
                <p class="mt-1">For help, see <a href="README.md" class="text-blue-600 hover:underline">README.md</a></p>
            </div>
        </div>
    </div>
</body>
</html>
