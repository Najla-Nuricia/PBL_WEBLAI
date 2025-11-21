/**
 * AI Lab Polinema - Custom JavaScript for Admin Pages
 */

document.addEventListener("DOMContentLoaded", function () {
  // Initialize dynamic components
  initAdminEventListeners();
});

function initAdminEventListeners() {
  // Image preview
  // Note: The input element needs onchange="previewImage(this, 'previewId')"
  
  // Confirm delete action
  const deleteLinks = document.querySelectorAll(
    "a[onclick*='confirmDelete']"
  );
  deleteLinks.forEach((link) => {
    // Prevent default and use SweetAlert
    link.setAttribute('onclick', `event.preventDefault(); confirmDelete(this.href);`);
  });

  // Bulk actions for tables
  const selectAll = document.getElementById("selectAll");
  const checkboxes = document.querySelectorAll(".rowCheckbox");
  const bulkAction = document.getElementById("bulkAction");
  const bulkDeleteBtn = document.getElementById("bulkDeleteBtn");

  if (selectAll && checkboxes.length > 0 && bulkAction && bulkDeleteBtn) {
    // Select/Deselect All
    selectAll.addEventListener("change", function () {
      checkboxes.forEach((cb) => (cb.checked = this.checked));
      toggleBulkAction();
    });

    // Show/hide bulk action bar
    checkboxes.forEach((cb) => {
      cb.addEventListener("change", toggleBulkAction);
    });

    function toggleBulkAction() {
      const anyChecked = [...checkboxes].some((cb) => cb.checked);
      bulkAction.classList.toggle("d-none", !anyChecked);
    }

    // Handle Bulk Delete
    bulkDeleteBtn.addEventListener("click", async function (e) {
      e.preventDefault();

      const selectedIds = [...checkboxes]
        .filter((cb) => cb.checked)
        .map((cb) => cb.value);

      if (selectedIds.length === 0) {
        showError("Pilih setidaknya satu data untuk dihapus.");
        return;
      }
      
      const confirmed = await confirmBulkDelete(selectedIds.length);

      if (confirmed) {
        const form = document.getElementById("bulkDeleteForm");
        if(form) {
            // Clear previous hidden inputs if any
            form.querySelectorAll('input[name="ids[]"]').forEach(i => i.remove());

            // Add selected IDs as hidden inputs
            selectedIds.forEach(id => {
                const input = document.createElement("input");
                input.type = "hidden";
                input.name = "ids[]";
                input.value = id;
                form.appendChild(input);
            });
            form.submit();
        } else {
            showError("Form bulk delete tidak ditemukan.");
        }
      }
    });
  }

  // Initialize Select2 for enhanced select dropdowns
  if (typeof $ !== 'undefined' && $(".select-enhanced").length) {
    $(".select-enhanced").select2({
      theme: "bootstrap-5",
      placeholder: "Pilih opsi...",
      allowClear: true,
      width: "100%",
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
      preview.style.display = "block";
    };
    reader.readAsDataURL(input.files[0]);
  }
}

/**
 * Confirm single item delete action
 * @param {string} url The URL to redirect to for deletion
 */
function confirmDelete(url) {
  Swal.fire({
    title: "Konfirmasi Hapus",
    text: "Apakah Anda yakin ingin menghapus data ini?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Ya, hapus!",
    cancelButtonText: "Batal",
    confirmButtonColor: "#d33",
    cancelButtonColor: "#6c757d",
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = url;
    }
  });
}

/**
 * Confirm bulk delete action
 * @param {number} count The number of items to be deleted
 */
async function confirmBulkDelete(count) {
    const result = await Swal.fire({
      title: "Yakin ingin menghapus?",
      text: `${count} data yang dipilih akan dihapus secara permanen!`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Ya, hapus semua",
      cancelButtonText: "Batal",
      confirmButtonColor: "#d33",
      cancelButtonColor: "#6c757d",
    });
    return result.isConfirmed;
}


/**
 * Show a success notification using SweetAlert
 * @param {string} message The message to display
 */
function showSuccess(message) {
  Swal.fire({
    icon: "success",
    title: "Berhasil!",
    text: message,
    timer: 1500,
    showConfirmButton: false,
    timerProgressBar: true,
  });
}

/**
 * Show an error notification using SweetAlert
 * @param {string} message The message to display
 */
function showError(message) {
  Swal.fire({
    icon: "error",
    title: "Oops...",
    text: message,
    timer: 2500,
    showConfirmButton: false,
    timerProgressBar: true,
  });
}

// Export functions for use in other scripts or inline event handlers
window.AILab = window.AILab || {};
Object.assign(window.AILab, {
  previewImage,
  confirmDelete,
  showSuccess,
  showError
});
