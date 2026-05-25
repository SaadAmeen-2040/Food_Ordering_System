    <?php if(isset($_SESSION['admin_id'])): ?>
            </div> <!-- End Page Content -->
        </div> <!-- End Main Content Wrapper -->
    </div> <!-- End d-flex wrapper -->
    <?php endif; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-close alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 4000);
    </script>
</body>
</html>
