</div> <!-- End Content Wrapper -->
</div> <!-- End Main Content -->

<!-- jQuery -->
<script src="../node_modules/jquery/dist/jquery.min.js"></script>

<!-- Bootstrap 5 JS -->
<script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Quill JS -->
<script src="../node_modules/quill/dist/quill.js"></script>

<!-- flatpickr JS -->
<script src="../node_modules/flatpickr/dist/flatpickr.min.js"></script>

<!-- Custom JS -->
<script src="../assets/js/public.js"></script>
<script src="../assets/js/admin.js"></script>

<script>
    // Sidebar Toggle for Mobile
    document.getElementById('sidebarToggle')?.addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('active');
    });

    // Initialize DataTables
    $(document).ready(function() {
        if ($('.datatable').length) {
            $('.datatable').DataTable({
                language: {
                    url: '../assets/js/id.json'
                },
                pageLength: 10,
                order: [
                    [1, 'asc']
                ],
                // Disable sorting on the first column
                columnDefs: [{
                    targets: [0],
                    orderable: false
                }]
            });
        }
    });

    // Initialize Flatpickr for date
    flatpickr(".flatpickr-date", {
        altInput: true,
        altFormat: "j F Y",
        dateFormat: "Y-m-d",
        locale: "id"
    });

    // Initialize Flatpickr for time
    flatpickr(".flatpickr-time", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
    });
</script>

</body>

</html>