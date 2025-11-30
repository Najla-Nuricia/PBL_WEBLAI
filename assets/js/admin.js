/**
 * AI Lab Polinema - Custom JavaScript for Admin Pages
 */

document.addEventListener('DOMContentLoaded', function () {
    // Initialize dynamic components
    initAdminEventListeners();
});

function initAdminEventListeners() {
    // Image preview
    // Note: The input element needs onchange="previewImage(this, 'previewId')"

    // Confirm delete action
    const deleteLinks = document.querySelectorAll("a[onclick*='confirmDelete']");
    deleteLinks.forEach((link) => {
        // Prevent default and use SweetAlert
        link.setAttribute('onclick', `event.preventDefault(); confirmDelete(this.href);`);
    });

    // Bulk actions for tables
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.rowCheckbox');
    const bulkAction = document.getElementById('bulkAction');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

    if (selectAll && checkboxes.length > 0 && bulkAction && bulkDeleteBtn) {
        // Select/Deselect All
        selectAll.addEventListener('change', function () {
            checkboxes.forEach((cb) => (cb.checked = this.checked));
            toggleBulkAction();
        });

        // Show/hide bulk action bar
        checkboxes.forEach((cb) => {
            cb.addEventListener('change', toggleBulkAction);
        });

        function toggleBulkAction() {
            const anyChecked = [...checkboxes].some((cb) => cb.checked);
            bulkAction.classList.toggle('d-none', !anyChecked);
        }

        // Handle Bulk Delete
        bulkDeleteBtn.addEventListener('click', async function (e) {
            e.preventDefault();

            const selectedIds = [...checkboxes].filter((cb) => cb.checked).map((cb) => cb.value);

            if (selectedIds.length === 0) {
                showError('Pilih setidaknya satu data untuk dihapus.');
                return;
            }

            const confirmed = await confirmBulkDelete(selectedIds.length);

            if (confirmed) {
                const form = document.getElementById('bulkDeleteForm');
                if (form) {
                    form.submit();
                } else {
                    showError('Form bulk delete tidak ditemukan.');
                }
            }
        });
    }

    // Initialize Select2 for enhanced select dropdowns
    if (typeof $ !== 'undefined' && $('.select-enhanced').length) {
        $('.select-enhanced').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih opsi...',
            allowClear: true,
            width: '100%',
        });
    }
}

/**
 * Image preview before upload
 * @param {HTMLInputElement} input The file input element
 * @param {string} previewId The ID of the img element for preview
 */
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

/**
 * Confirm single item delete action with smooth animation
 * @param {string} url The URL to redirect to for deletion
 */
function confirmDelete(url) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Apakah Anda yakin ingin menghapus data ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        reverseButtons: true,

        // Animasi masuk
        showClass: {
            popup: `
        animate__animated
        animate__fadeInDown
        animate__faster
      `,
        },

        // Animasi keluar
        hideClass: {
            popup: `
        animate__animated
        animate__fadeOutUp
        animate__faster
      `,
        },
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

/**
 * Confirm bulk delete action with smooth animation
 * @param {number} count The number of items to be deleted
 */
async function confirmBulkDelete(count) {
    const result = await Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: `${count} data yang dipilih akan dihapus secara permanen!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus semua',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',

        // Animasi masuk
        showClass: {
            popup: `
        animate__animated
        animate__fadeInDown
        animate__faster
      `,
        },

        // Animasi keluar
        hideClass: {
            popup: `
        animate__animated
        animate__fadeOutUp
        animate__faster
      `,
        },
    });

    return result.isConfirmed;
}

/**
 * Show a smooth success notification using SweetAlert
 * @param {string} message The message to display
 */
function showSuccess(message) {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: message,
        timer: 1500,
        showConfirmButton: false,
        timerProgressBar: true,

        // Animasi masuk
        showClass: {
            popup: `
        animate__animated 
        animate__fadeInDown 
        animate__faster
      `,
        },

        // Animasi keluar
        hideClass: {
            popup: `
        animate__animated 
        animate__fadeOutUp 
        animate__faster
      `,
        },
    });
}

/**
 * Show an error notification using SweetAlert with smooth animation
 * @param {string} message The message to display
 */
function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: message,
        timer: 2500,
        showConfirmButton: false,
        timerProgressBar: true,

        // Animasi masuk
        showClass: {
            popup: `
        animate__animated 
        animate__fadeInDown 
        animate__faster
      `,
        },

        // Animasi keluar
        hideClass: {
            popup: `
        animate__animated 
        animate__fadeOutUp 
        animate__faster
      `,
        },
    });
}

// Export functions for use in other scripts or inline event handlers
window.AILab = window.AILab || {};
Object.assign(window.AILab, {
    previewImage,
    confirmDelete,
    showSuccess,
    showError,
});

/* ===== FILE 1: swipeable-table.js ===== */

(function () {
    'use strict';

    /**
     * Inisialisasi swipeable table
     */
    function initSwipeableTable() {
        const swipeTable = document.getElementById('swipeTable');
        const hint = document.querySelector('.swipe-hint');

        if (!swipeTable) return;

        let isDown = false;
        let startX;
        let scrollLeft;
        let hasSwiped = false;

        /**
         * Update shadow indicators berdasarkan posisi scroll
         */
        function updateShadows() {
            const scrollLeft = swipeTable.scrollLeft;
            const maxScroll = swipeTable.scrollWidth - swipeTable.clientWidth;

            if (scrollLeft > 10) {
                swipeTable.classList.add('scrolled-left');
            } else {
                swipeTable.classList.remove('scrolled-left');
            }

            if (scrollLeft < maxScroll - 10) {
                swipeTable.classList.remove('scrolled-right');
            } else {
                swipeTable.classList.add('scrolled-right');
            }
        }

        /**
         * Hide hint banner dengan animasi
         */
        function hideHint() {
            if (hint) {
                hint.style.transition = 'opacity 0.5s, transform 0.5s';
                hint.style.opacity = '0';
                hint.style.transform = 'translateY(-10px)';
                setTimeout(() => hint.remove(), 500);
            }
        }

        /**
         * Check apakah element yang diklik adalah interactive element
         */
        function isInteractiveElement(target) {
            return target.closest('a, button, input, select, textarea');
        }

        // ===== MOUSE EVENTS (Desktop) =====
        swipeTable.addEventListener('mousedown', (e) => {
            if (isInteractiveElement(e.target)) return;

            isDown = true;
            swipeTable.classList.add('dragging');
            startX = e.pageX - swipeTable.offsetLeft;
            scrollLeft = swipeTable.scrollLeft;
        });

        swipeTable.addEventListener('mouseleave', () => {
            isDown = false;
            swipeTable.classList.remove('dragging');
        });

        swipeTable.addEventListener('mouseup', () => {
            isDown = false;
            swipeTable.classList.remove('dragging');
        });

        swipeTable.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();

            const x = e.pageX - swipeTable.offsetLeft;
            const walk = (x - startX) * 2;
            swipeTable.scrollLeft = scrollLeft - walk;

            if (!hasSwiped) {
                hasSwiped = true;
                hideHint();
            }
        });

        // ===== TOUCH EVENTS (Mobile/Tablet) =====
        let touchStartX = 0;
        let touchScrollLeft = 0;

        swipeTable.addEventListener(
            'touchstart',
            (e) => {
                if (isInteractiveElement(e.target)) return;

                touchStartX = e.touches[0].pageX;
                touchScrollLeft = swipeTable.scrollLeft;
                swipeTable.classList.add('dragging');
            },
            { passive: true }
        );

        swipeTable.addEventListener(
            'touchmove',
            (e) => {
                if (!touchStartX) return;

                const touchX = e.touches[0].pageX;
                const walk = (touchStartX - touchX) * 1.5;
                swipeTable.scrollLeft = touchScrollLeft + walk;

                if (!hasSwiped) {
                    hasSwiped = true;
                    hideHint();
                }
            },
            { passive: true }
        );

        swipeTable.addEventListener(
            'touchend',
            () => {
                touchStartX = 0;
                swipeTable.classList.remove('dragging');
            },
            { passive: true }
        );

        // ===== EVENT LISTENERS =====
        swipeTable.addEventListener('scroll', updateShadows);

        // Initial shadow check
        updateShadows();

        // Auto hide hint after 8 seconds
        setTimeout(() => {
            if (!hasSwiped) hideHint();
        }, 8000);
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSwipeableTable);
    } else {
        initSwipeableTable();
    }
})();

// Function Side bar Mobile
(function () {
    'use strict';

    function initSidebar() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');
        const body = document.body;

        if (!sidebarToggle || !sidebar) {
            return;
        }

        // Toggle sidebar
        sidebarToggle.addEventListener(
            'click',
            function (e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();

                if (sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                    body.classList.remove('sidebar-open');
                } else {
                    sidebar.classList.add('active');
                    body.classList.add('sidebar-open');
                }
            },
            true
        );

        // Close when clicking outside
        body.addEventListener('click', function (event) {
            if (window.innerWidth <= 768 && sidebar.classList.contains('active')) {
                const clickedInsideSidebar = sidebar.contains(event.target);
                const clickedToggle = sidebarToggle.contains(event.target);

                if (!clickedInsideSidebar && !clickedToggle) {
                    sidebar.classList.remove('active');
                    body.classList.remove('sidebar-open');
                }
            }
        });

        // Close when clicking menu item
        sidebar.querySelectorAll('.sidebar-menu a').forEach((link) => {
            link.addEventListener('click', function () {
                if (window.innerWidth <= 768) {
                    setTimeout(function () {
                        sidebar.classList.remove('active');
                        body.classList.remove('sidebar-open');
                    }, 200);
                }
            });
        });

        // Close on resize to desktop
        let resizeTimer;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('active');
                    body.classList.remove('sidebar-open');
                }
            }, 250);
        });
    }

    // Topbar scroll effect
    function initTopbar() {
        const topbar = document.querySelector('.topbar');
        if (topbar) {
            window.addEventListener('scroll', function () {
                topbar.classList.toggle('scrolled', window.scrollY > 10);
            });
        }
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initSidebar();
            initTopbar();
        });
    } else {
        initSidebar();
        initTopbar();
    }
})();
