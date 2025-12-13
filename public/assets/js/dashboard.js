/**
 * Dashboard JavaScript
 */

document.addEventListener("DOMContentLoaded", function () {
  initializeDashboard();
});

function initializeDashboard() {
  initializeSidebarToggle();
  initializeDropdowns();
}

/**
 * Sidebar toggle for mobile
 */
function initializeSidebarToggle() {
  const sidebar = document.getElementById("dashboardSidebar");
  const toggleBtn = document.getElementById("sidebarToggle");
  const backdrop = document.querySelector(".sidebar-backdrop");

  if (!sidebar || !toggleBtn) {
    console.error("Sidebar elements not found");
    return;
  }

  // Use a simple click handler with no complex touch logic to avoid conflicts
  toggleBtn.onclick = function (e) {
    e.preventDefault();
    e.stopPropagation();

    sidebar.classList.toggle("active");
    if (backdrop) {
      backdrop.classList.toggle("show");
    }
    console.log("Sidebar toggled");
  };

  if (backdrop) {
    backdrop.onclick = function () {
      sidebar.classList.remove("active");
      backdrop.classList.remove("show");
    };
  }

  // Close sidebar when clicking a link on mobile
  const links = sidebar.querySelectorAll("a");
  links.forEach((link) => {
    link.addEventListener("click", () => {
      if (window.innerWidth <= 1024) {
        sidebar.classList.remove("active");
        if (backdrop) backdrop.classList.remove("show");
      }
    });
  });
}

/**
 * Dropdown handling (Profile & Notifications)
 */
function initializeDropdowns() {
  const profileMenu = document.querySelector(".profile-menu");
  const notificationIcon = document.querySelector(".notification-icon");

  const profileDropdown = document.querySelector(".profile-dropdown");
  const notificationDropdown = document.querySelector(".notification-dropdown");

  function closeAllDropdowns() {
    if (profileDropdown) profileDropdown.classList.remove("show");
    if (notificationDropdown) notificationDropdown.classList.remove("show");
  }

  if (profileMenu && profileDropdown) {
    profileMenu.addEventListener("click", (e) => {
      e.stopPropagation();
      const isVisible = profileDropdown.classList.contains("show");
      closeAllDropdowns(); // Close others
      if (!isVisible) {
        profileDropdown.classList.add("show");
      }
    });
  }

  if (notificationIcon && notificationDropdown) {
    notificationIcon.addEventListener("click", (e) => {
      e.stopPropagation();
      const isVisible = notificationDropdown.classList.contains("show");
      closeAllDropdowns(); // Close others
      if (!isVisible) {
        notificationDropdown.classList.add("show");
      }
    });
  }

  // Close dropdowns when clicking outside
  document.addEventListener("click", (e) => {
    closeAllDropdowns();
  });

  // Prevent closing when clicking inside the dropdown itself
  if (profileDropdown) {
    profileDropdown.addEventListener("click", (e) => e.stopPropagation());
  }
  if (notificationDropdown) {
    notificationDropdown.addEventListener("click", (e) => e.stopPropagation());
  }
}
