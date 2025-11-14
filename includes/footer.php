</main>
        </div>
    </div>

    <script>
        // Toggle sidebar on mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('sidebar-hidden');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const target = event.target;

            if (window.innerWidth < 768) {
                if (!sidebar.contains(target) && !target.closest('button[onclick="toggleSidebar()"]')) {
                    sidebar.classList.add('sidebar-hidden');
                }
            }
        });

        // Confirmation dialog for delete actions
        function confirmDelete(message = 'Apakah Anda yakin ingin menghapus data ini?') {
            return confirm(message);
        }

        // Auto-hide flash messages after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        // Search debounce
        let searchTimeout;
        function debounceSearch(func, delay = 500) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(func, delay);
        }

        // Print function
        function printPage() {
            window.print();
        }

        // Export to CSV
        function exportToCSV(tableId, filename = 'export.csv') {
            const table = document.getElementById(tableId);
            let csv = [];
            const rows = table.querySelectorAll('tr');

            rows.forEach(row => {
                const cols = row.querySelectorAll('td, th');
                const rowData = Array.from(cols).map(col => {
                    let text = col.innerText.replace(/"/g, '""');
                    return `"${text}"`;
                });
                csv.push(rowData.join(','));
            });

            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            a.click();
            window.URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>
