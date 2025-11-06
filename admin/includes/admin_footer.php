</div> <!-- End Content Wrapper -->
</div> <!-- End Main Content -->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

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
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 10,
                order: [
                    [0, 'desc']
                ]
            });
        }
    });

    // Auto dismiss alerts
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Confirm delete
    function confirmDelete(message = 'Apakah Anda yakin ingin menghapus data ini?') {
        return confirm(message);
    }

    // Preview image before upload
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
                document.getElementById(previewId).style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

</body>

</html>