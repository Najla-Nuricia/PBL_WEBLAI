/**
 * AI Lab Polinema - Custom JavaScript
 */

// Document Ready
document.addEventListener("DOMContentLoaded", function () {
  // Initialize tooltips
  initTooltips();

  // Smooth scrolling
  initSmoothScroll();

  // Navbar scroll effect
  initNavbarScroll();

  // Auto-hide alerts
  autoHideAlerts();

  // Image lazy loading
  initLazyLoading();

  // Form validation
  initFormValidation();

  // Counter animation
  initCounterAnimation();
});

/**
 * Initialize Bootstrap tooltips
 */
function initTooltips() {
  const tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
}

/**
 * Smooth scroll for anchor links
 */
function initSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      const href = this.getAttribute("href");
      if (href !== "#" && href !== "") {
        e.preventDefault();
        const target = document.querySelector(href);
        if (target) {
          target.scrollIntoView({
            behavior: "smooth",
            block: "start",
          });
        }
      }
    });
  });
}

/**
 * Navbar background change on scroll
 */
function initNavbarScroll() {
  const navbar = document.querySelector(".navbar");
  if (navbar) {
    window.addEventListener("scroll", function () {
      if (window.scrollY > 50) {
        navbar.classList.add("scrolled");
        navbar.style.boxShadow = "0 5px 20px rgba(0,0,0,0.1)";
      } else {
        navbar.classList.remove("scrolled");
        navbar.style.boxShadow = "0 2px 10px rgba(0,0,0,0.05)";
      }
    });
  }
}

/**
 * Auto-hide alerts after 5 seconds
 */
function autoHideAlerts() {
  const alerts = document.querySelectorAll(".alert:not(.alert-permanent)");
  alerts.forEach((alert) => {
    setTimeout(() => {
      const bsAlert = new bootstrap.Alert(alert);
      bsAlert.close();
    }, 5000);
  });
}

/**
 * Lazy loading for images
 */
function initLazyLoading() {
  const images = document.querySelectorAll("img[data-src]");

  const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const img = entry.target;
        img.src = img.dataset.src;
        img.classList.add("fade-in");
        observer.unobserve(img);
      }
    });
  });

  images.forEach((img) => imageObserver.observe(img));
}

/**
 * Enhanced form validation
 */
function initFormValidation() {
  const forms = document.querySelectorAll(".needs-validation");

  forms.forEach((form) => {
    form.addEventListener(
      "submit",
      function (event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add("was-validated");
      },
      false
    );
  });
}

/**
 * Counter animation for statistics
 */
function initCounterAnimation() {
  const counters = document.querySelectorAll(".counter");

  const counterObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const counter = entry.target;
          const target = parseInt(counter.getAttribute("data-target"));
          const duration = 2000;
          const step = target / (duration / 16);
          let current = 0;

          const updateCounter = () => {
            current += step;
            if (current < target) {
              counter.textContent = Math.floor(current);
              requestAnimationFrame(updateCounter);
            } else {
              counter.textContent = target;
            }
          };

          updateCounter();
          counterObserver.unobserve(counter);
        }
      });
    },
    { threshold: 0.5 }
  );

  counters.forEach((counter) => counterObserver.observe(counter));
}

/**
 * Image preview before upload
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
 * Confirm delete action
 */
function confirmDelete(
  message = "Apakah Anda yakin ingin menghapus data ini?"
) {
  return Swal.fire({
    title: "Konfirmasi",
    text: message,
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Ya, hapus",
    cancelButtonText: "Batal",
    cancelButtonColor: "#6c757d",
    confirmButtonColor: "#d33",
    reverseButtons: true,
    showClass: {
      popup: "animate__animated animate__fadeInDown faster", // animasi masuk
    },
    hideClass: {
      popup: "animate__animated animate__fadeOutUp faster", // animasi keluar
    },
  }).then((result) => result.isConfirmed);
}

document.addEventListener("DOMContentLoaded", () => {
  const deleteLinks = document.querySelectorAll(
    "a[onclick^='return confirmDelete']"
  );
  deleteLinks.forEach((link) => {
    link.addEventListener("click", async function (e) {
      e.preventDefault();
      const confirmed = await confirmDelete(
        "Apakah Anda yakin ingin menghapus data ini?"
      );
      if (confirmed) window.location.href = this.href;
    });
  });
});

// Succes Alert
function showSuccess(message) {
  Swal.fire({
    icon: "success",
    title: "Berhasil!",
    text: message,
    timer: 1300,
    showConfirmButton: false,
    timerProgressBar: true,
    showClass: {
      popup: "animate__animated animate__fadeInDown faster",
    },
    hideClass: {
      popup: "animate__animated animate__fadeOutUp faster",
    },
  });
}
// show error
function showError(message) {
  Swal.fire({
    icon: "error",
    title: "Terjadi Kesalahan!",
    text: message,
    timer: 1500, // ⏱ Sedikit lebih cepat
    showConfirmButton: false,
    timerProgressBar: true,
    showClass: {
      popup: "animate__animated animate__fadeInDown faster",
    },
    hideClass: {
      popup: "animate__animated animate__fadeOutUp faster",
    },
  });
}

document.addEventListener("DOMContentLoaded", function () {
  const selectAll = document.getElementById("selectAll");
  const checkboxes = document.querySelectorAll(".rowCheckbox");
  const bulkAction = document.getElementById("bulkAction");
  const bulkDeleteBtn = document.getElementById("bulkDeleteBtn");
  const bulkDeleteForm = document.getElementById("bulkDeleteForm");

  // ✅ Select/Deselect All
  selectAll.addEventListener("change", function () {
    checkboxes.forEach((cb) => (cb.checked = this.checked));
    toggleBulkAction();
  });

  // ✅ Show/hide bulk action bar
  checkboxes.forEach((cb) => {
    cb.addEventListener("change", toggleBulkAction);
  });

  function toggleBulkAction() {
    const anyChecked = [...checkboxes].some((cb) => cb.checked);
    bulkAction.classList.toggle("d-none", !anyChecked);
  }

  // ✅ Handle Bulk Delete
  bulkDeleteBtn.addEventListener("click", async function (e) {
    e.preventDefault();

    const selected = [...checkboxes]
      .filter((cb) => cb.checked)
      .map((cb) => cb.value);

    if (selected.length === 0) return;

    const confirm = await Swal.fire({
      title: "Yakin ingin menghapus?",
      text: `${selected.length} data akan dihapus!`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Ya, hapus",
      cancelButtonText: "Batal",
      confirmButtonColor: "#d33",
      cancelButtonColor: "#6c757d",
    });

    if (confirm.isConfirmed) {
      const form = document.getElementById("bulkDeleteForm");
      form.action = ""; // biar kirim ke halaman yang sama
      const input = document.createElement("input");
      input.type = "hidden";
      input.name = "bulk_delete";
      input.value = "1";
      form.appendChild(input);
      form.submit();
    }
  });
});
// Animasi select
document.addEventListener("DOMContentLoaded", function () {
  // Aktifkan Select2 di semua elemen <select> yang punya class "select-enhanced"
  if ($(".select-enhanced").length) {
    $(".select-enhanced").select2({
      theme: "bootstrap-5",
      placeholder: "Pilih opsi...",
      allowClear: true,
      width: "100%",
      minimumResultsForSearch: Infinity, // nonaktifkan search bar
    });
  }
});

/**
 * Copy to clipboard
 */
function copyToClipboard(text) {
  navigator.clipboard.writeText(text).then(
    function () {
      showToast("Berhasil disalin ke clipboard!", "success");
    },
    function (err) {
      showToast("Gagal menyalin!", "danger");
    }
  );
}

/**
 * Show toast notification
 */
function showToast(message, type = "info") {
  const toastHTML = `
        <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;

  let toastContainer = document.querySelector(".toast-container");
  if (!toastContainer) {
    toastContainer = document.createElement("div");
    toastContainer.className =
      "toast-container position-fixed bottom-0 end-0 p-3";
    document.body.appendChild(toastContainer);
  }

  toastContainer.insertAdjacentHTML("beforeend", toastHTML);
  const toastElement = toastContainer.lastElementChild;
  const toast = new bootstrap.Toast(toastElement);
  toast.show();

  toastElement.addEventListener("hidden.bs.toast", function () {
    toastElement.remove();
  });
}

/**
 * Loading overlay
 */
function showLoading() {
  const loadingHTML = `
        <div id="loadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" 
             style="background: rgba(0,0,0,0.5); z-index: 9999;">
            <div class="spinner-border text-light" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
  document.body.insertAdjacentHTML("beforeend", loadingHTML);
}

function hideLoading() {
  const overlay = document.getElementById("loadingOverlay");
  if (overlay) {
    overlay.remove();
  }
}

/**
 * Format number with thousand separator
 */
function formatNumber(num) {
  return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

/**
 * Format date to Indonesian format
 */
function formatDate(dateString) {
  const months = [
    "Januari",
    "Februari",
    "Maret",
    "April",
    "Mei",
    "Juni",
    "Juli",
    "Agustus",
    "September",
    "Oktober",
    "November",
    "Desember",
  ];
  const date = new Date(dateString);
  return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
}

/**
 * Debounce function
 */
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

/**
 * Check if element is in viewport
 */
function isInViewport(element) {
  const rect = element.getBoundingClientRect();
  return (
    rect.top >= 0 &&
    rect.left >= 0 &&
    rect.bottom <=
      (window.innerHeight || document.documentElement.clientHeight) &&
    rect.right <= (window.innerWidth || document.documentElement.clientWidth)
  );
}

/**
 * Animate elements on scroll
 */
function animateOnScroll() {
  const elements = document.querySelectorAll(".animate-on-scroll");

  elements.forEach((element) => {
    if (isInViewport(element)) {
      element.classList.add("animated", "fadeInUp");
    }
  });
}

// Add scroll event listener for animations
window.addEventListener("scroll", debounce(animateOnScroll, 50));

/**
 * Back to top button
 */
const backToTopBtn = document.createElement("button");
backToTopBtn.innerHTML = '<i class="bi bi-arrow-up"></i>';
backToTopBtn.className = "btn btn-primary position-fixed bottom-0 end-0 m-4";
backToTopBtn.style.display = "none";
backToTopBtn.style.zIndex = "1000";
backToTopBtn.onclick = () => window.scrollTo({ top: 0, behavior: "smooth" });
document.body.appendChild(backToTopBtn);

window.addEventListener("scroll", function () {
  if (window.scrollY > 300) {
    backToTopBtn.style.display = "block";
  } else {
    backToTopBtn.style.display = "none";
  }
});

// Export functions for use in other scripts
window.AILab = {
  previewImage,
  confirmDelete,
  copyToClipboard,
  showToast,
  showLoading,
  hideLoading,
  formatNumber,
  formatDate,
};