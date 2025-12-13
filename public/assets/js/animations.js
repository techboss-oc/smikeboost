/**
 * Animations and Scroll Effects
 */

document.addEventListener("DOMContentLoaded", function () {
  initializeScrollReveal();
  initializeParallax();
  initializeCounterAnimation();
});

/**
 * Scroll reveal animation
 */
function initializeScrollReveal() {
  const revealElements = document.querySelectorAll(
    ".advantage-card, .stat-card, .service-card, .step-card, .blog-card, .mission-card"
  );

  const revealOnScroll = debounce(() => {
    revealElements.forEach((element) => {
      const windowHeight = window.innerHeight;
      const elementTop = element.getBoundingClientRect().top;
      const elementBottom = element.getBoundingClientRect().bottom;

      if (elementTop < windowHeight && elementBottom > 0) {
        element.style.animation = "slideUp 0.6s ease forwards";
        element.classList.add("revealed");
      }
    });
  }, 100);

  window.addEventListener("scroll", revealOnScroll);
  revealOnScroll(); // Initial call
}

/**
 * Parallax effect
 */
function initializeParallax() {
  const parallaxElements = document.querySelectorAll("[data-parallax]");

  if (parallaxElements.length === 0) return;

  window.addEventListener(
    "scroll",
    debounce(() => {
      parallaxElements.forEach((element) => {
        const scrollPosition = window.pageYOffset;
        const elementOffset = element.offsetTop;
        const distance = scrollPosition - elementOffset;
        const parallaxFactor = 0.5;

        element.style.transform = `translateY(${distance * parallaxFactor}px)`;
      });
    }, 10)
  );
}

/**
 * Counter animation for statistics
 */
function initializeCounterAnimation() {
  const counters = document.querySelectorAll(".stat-number");

  const startCounters = () => {
    counters.forEach((counter) => {
      if (counter.classList.contains("counted")) return;

      const target = parseInt(
        counter.innerText.replace(/,/g, "").match(/\d+/)[0]
      );
      const increment = target / 50;
      let current = 0;

      const updateCounter = () => {
        current += increment;
        if (current < target) {
          counter.innerText = Math.ceil(current).toLocaleString() + "+";
          requestAnimationFrame(updateCounter);
        } else {
          counter.innerText = target.toLocaleString() + "+";
          counter.classList.add("counted");
        }
      };

      // Check if element is in viewport
      const rect = counter.getBoundingClientRect();
      if (rect.top < window.innerHeight && rect.bottom > 0) {
        updateCounter();
      }
    });
  };

  window.addEventListener("scroll", debounce(startCounters, 100));
  startCounters(); // Initial call
}

/**
 * Floating animation for cards
 */
function animateFloatingCards() {
  const floatingCards = document.querySelectorAll(".card-float");

  floatingCards.forEach((card, index) => {
    card.style.animation = `float 8s ease-in-out infinite`;
    card.style.animationDelay = `${index * 2}s`;
  });
}

/**
 * Gradient animation
 */
function createGradientAnimation() {
  const style = document.createElement("style");
  style.textContent = `
        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        .gradient-animated {
            background-size: 200% 200%;
            animation: gradientShift 15s ease infinite;
        }
    `;
  document.head.appendChild(style);
}

/**
 * Add glow effect on hover
 */
function addGlowEffect() {
  const cards = document.querySelectorAll(
    ".glass-card, .service-card, .advantage-card"
  );

  cards.forEach((card) => {
    card.addEventListener("mousemove", (e) => {
      const rect = card.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;

      card.style.setProperty("--mouse-x", x + "px");
      card.style.setProperty("--mouse-y", y + "px");
    });
  });
}

/**
 * Debounce helper (redefine here for animations)
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
 * Page transition animations
 */
function initializePageTransition() {
  const links = document.querySelectorAll(
    'a:not([target="_blank"]):not([href^="#"])'
  );

  links.forEach((link) => {
    link.addEventListener("click", (e) => {
      if (link.href === window.location.href) {
        e.preventDefault();
        return;
      }

      const body = document.body;
      body.style.opacity = "0";
      body.style.transition = "opacity 0.3s ease";

      setTimeout(() => {
        window.location.href = link.href;
      }, 300);
    });
  });
}

/**
 * Initialize all animations
 */
function initializeAllAnimations() {
  animateFloatingCards();
  createGradientAnimation();
  addGlowEffect();
  initializePageTransition();
}

// Run all animations
document.addEventListener("DOMContentLoaded", initializeAllAnimations);

// Export for use
window.initializeScrollReveal = initializeScrollReveal;
window.initializeParallax = initializeParallax;
window.initializeCounterAnimation = initializeCounterAnimation;
