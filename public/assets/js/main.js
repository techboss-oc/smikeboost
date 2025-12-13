/**
 * Main JavaScript File
 * SmikeBoost - SMM Panel
 */

// DOM Ready
document.addEventListener("DOMContentLoaded", function () {
  // Initialize all features
  initializeNavigation();
  initializeFAQ();
  initializeForm();
  const resetButton = document.getElementById("resetCookiePreferences");
  if (resetButton) {
    resetButton.addEventListener("click", () => {
      localStorage.removeItem("smikeboostCookieConsent");
      showNotification("Cookie preferences cleared.", "info");
    });
  }
});

/**
 * Navigation Toggle
 */
function initializeNavigation() {
  const hamburger = document.getElementById("hamburger");
  const navMenu = document.getElementById("navMenu");

  if (hamburger && navMenu) {
    hamburger.addEventListener("click", function () {
      navMenu.classList.toggle("active");
      hamburger.classList.toggle("active");
    });

    // Close menu when a link is clicked
    const navLinks = navMenu.querySelectorAll(".nav-link");
    navLinks.forEach((link) => {
      link.addEventListener("click", () => {
        navMenu.classList.remove("active");
        hamburger.classList.remove("active");
      });
    });
  }
}

/**
 * FAQ Accordion
 */
function initializeFAQ() {
  const faqItems = document.querySelectorAll(".faq-item");

  faqItems.forEach((item) => {
    const question = item.querySelector(".faq-question");

    if (question) {
      question.addEventListener("click", function () {
        // Close other items
        faqItems.forEach((otherItem) => {
          if (otherItem !== item) {
            otherItem.classList.remove("active");
          }
        });

        // Toggle current item
        item.classList.toggle("active");
      });
    }
  });
}

/**
 * Form Validation
 */
function initializeForm() {
  const forms = document.querySelectorAll("form");

  forms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      if (!validateForm(this)) {
        e.preventDefault();
      }
    });
  });
}

/**
 * Validate form inputs
 */
function validateForm(form) {
  let isValid = true;
  const inputs = form.querySelectorAll(
    "input[required], select[required], textarea[required]"
  );

  inputs.forEach((input) => {
    if (!input.value.trim()) {
      input.classList.add("error");
      isValid = false;
    } else {
      input.classList.remove("error");
    }
  });

  // Email validation
  const emailInputs = form.querySelectorAll('input[type="email"]');
  emailInputs.forEach((input) => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(input.value)) {
      input.classList.add("error");
      isValid = false;
    }
  });

  // Password confirmation
  const password = form.querySelector('input[name="password"]');
  const passwordConfirm = form.querySelector('input[name="password_confirm"]');
  if (password && passwordConfirm && password.value !== passwordConfirm.value) {
    passwordConfirm.classList.add("error");
    isValid = false;
  }

  return isValid;
}

/**
 * Format currency (used in dashboard)
 */
function formatCurrency(amount) {
  return (
    "₦" +
    parseFloat(amount).toLocaleString("en-US", {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    })
  );
}

/**
 * Show notification/toast
 */
function showNotification(message, type = "info", duration = 3000) {
  const toast = document.createElement("div");
  toast.className = `toast toast-${type}`;
  toast.textContent = message;
  toast.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: var(--color-${
          type === "success" ? "success" : type === "danger" ? "danger" : "info"
        });
        color: white;
        padding: 16px 24px;
        border-radius: var(--radius-full);
        font-weight: 500;
        z-index: 9999;
        animation: slideUp 0.3s ease;
    `;

  document.body.appendChild(toast);

  setTimeout(() => {
    toast.style.animation = "slideDown 0.3s ease";
    setTimeout(() => toast.remove(), 300);
  }, duration);
}

/**
 * Lazy load images
 */
function lazyLoadImages() {
  const images = document.querySelectorAll("img[data-src]");

  const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const img = entry.target;
        img.src = img.dataset.src;
        img.removeAttribute("data-src");
        observer.unobserve(img);
      }
    });
  });

  images.forEach((img) => imageObserver.observe(img));
}

/**
 * Smooth scroll
 */
function smoothScroll(target) {
  document.querySelector(target).scrollIntoView({
    behavior: "smooth",
    block: "start",
  });
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
 * Reset form
 */
function resetFilters() {
  document.querySelectorAll(".filter-select").forEach((select) => {
    select.value = "";
  });
  // Trigger filter update if needed
}

/**
 * Update services based on platform selection
 */
function updateServices() {
  // This would be connected to backend to fetch available services
  console.log("Services updated");
}

/**
 * Calculate total price
 */
function calculateTotal() {
  const quantity = document.getElementById("quantity")?.value || 0;
  const pricePerThousand = 2500; // Example price
  const total = (quantity / 1000) * pricePerThousand;
  const totalElement = document.getElementById("total-amount");
  if (totalElement) {
    totalElement.textContent = formatCurrency(total);
  }
}

/**
 * Update price display
 */
function updatePrice() {
  // Update based on service selection
  console.log("Price updated");
}

/**
 * Copy to clipboard
 */
function copyToClipboard(text, feedbackElement = null) {
  navigator.clipboard
    .writeText(text)
    .then(() => {
      if (feedbackElement) {
        const original = feedbackElement.textContent;
        feedbackElement.textContent = "Copied!";
        setTimeout(() => {
          feedbackElement.textContent = original;
        }, 2000);
      }
      showNotification("Copied to clipboard", "success");
    })
    .catch(() => {
      showNotification("Failed to copy", "danger");
    });
}

/**
 * Toggle Password Visibility
 */
function togglePassword(inputId, icon) {
  const input = document.getElementById(inputId);
  if (input.type === "password") {
    input.type = "text";
    icon.classList.remove("fa-eye");
    icon.classList.add("fa-eye-slash");
  } else {
    input.type = "password";
    icon.classList.remove("fa-eye-slash");
    icon.classList.add("fa-eye");
  }
}

// Export functions for global use
window.formatCurrency = formatCurrency;
window.showNotification = showNotification;
window.smoothScroll = smoothScroll;
window.resetFilters = resetFilters;
window.updateServices = updateServices;
window.updatePrice = updatePrice;
window.calculateTotal = calculateTotal;
window.copyToClipboard = copyToClipboard;
window.initializeCookieConsent = initializeCookieConsent;
window.togglePassword = togglePassword;
