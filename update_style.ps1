$content = @"
/* ===================================
   SmikeBoost - Modern Glassmorphism UI
   =================================== */

:root {
    /* Brand Colors */
    --color-primary: #a855f7;       /* Purple 500 */
    --color-primary-dark: #7e22ce;  /* Purple 700 */
    --color-secondary: #ec4899;     /* Pink 500 */
    --color-accent: #06b6d4;        /* Cyan 500 */
    --color-success: #10b981;
    --color-warning: #f59e0b;
    --color-danger: #ef4444;
    
    /* Backgrounds */
    --bg-body: #0f0a1f;
    --bg-gradient-start: #130c25;
    --bg-gradient-end: #2e1065;
    
    /* Glassmorphism */
    --glass-bg: rgba(255, 255, 255, 0.03);
    --glass-bg-hover: rgba(255, 255, 255, 0.07);
    --glass-border: rgba(255, 255, 255, 0.1);
    --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
    --glass-blur: 12px;
    
    /* Typography */
    --font-sans: 'Inter', system-ui, -apple-system, sans-serif;
    --text-main: #ffffff;
    --text-muted: #94a3b8;
    --text-dim: #64748b;
    
    /* Spacing & Layout */
    --container-width: 1200px;
    --header-height: 80px;
    --radius-sm: 8px;
    --radius-md: 16px;
    --radius-lg: 24px;
    --radius-xl: 32px;
    
    /* Animations */
    --ease-out: cubic-bezier(0.215, 0.61, 0.355, 1);
}

/* Reset & Base */
*, *::before, *::after {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: var(--font-sans);
    background-color: var(--bg-body);
    background-image: 
        radial-gradient(circle at 15% 50%, rgba(168, 85, 247, 0.15), transparent 25%),
        radial-gradient(circle at 85% 30%, rgba(236, 72, 153, 0.15), transparent 25%);
    background-attachment: fixed;
    color: var(--text-main);
    line-height: 1.6;
    overflow-x: hidden;
    -webkit-font-smoothing: antialiased;
}

a { text-decoration: none; color: inherit; transition: 0.3s var(--ease-out); }
ul { list-style: none; }
img { max-width: 100%; display: block; }

/* Typography */
h1, h2, h3, h4, h5, h6 {
    line-height: 1.2;
    font-weight: 700;
    letter-spacing: -0.02em;
    color: var(--text-main);
}

h1 { font-size: clamp(2.5rem, 5vw, 4rem); }
h2 { font-size: clamp(2rem, 4vw, 3rem); }
h3 { font-size: 1.5rem; }

.text-gradient {
    background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.eyebrow {
    text-transform: uppercase;
    font-size: 0.875rem;
    font-weight: 600;
    letter-spacing: 0.1em;
    color: var(--color-secondary);
    margin-bottom: 1rem;
    display: block;
}

/* Layout Utilities */
.container {
    width: 100%;
    max-width: var(--container-width);
    margin: 0 auto;
    padding: 0 1.5rem;
}

.section {
    padding: 6rem 0;
    position: relative;
}

.grid {
    display: grid;
    gap: 2rem;
}

.grid-1 { grid-template-columns: 1fr; }
.grid-2 { grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); }
.grid-3 { grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); }
.grid-4 { grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); }

/* Glass Components */
.glass-card {
    background: var(--glass-bg);
    backdrop-filter: blur(var(--glass-blur));
    -webkit-backdrop-filter: blur(var(--glass-blur));
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-md);
    padding: 2rem;
    transition: transform 0.3s var(--ease-out), border-color 0.3s, background 0.3s;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.glass-card:hover {
    transform: translateY(-5px);
    border-color: rgba(168, 85, 247, 0.3);
    background: var(--glass-bg-hover);
    box-shadow: var(--glass-shadow);
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.75rem 1.5rem;
    border-radius: var(--radius-full);
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s var(--ease-out);
    gap: 0.5rem;
    border: none;
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}

.btn-primary {
    background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
    color: white;
    box-shadow: 0 4px 15px rgba(168, 85, 247, 0.4);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(168, 85, 247, 0.5);
}

.btn-outline {
    background: transparent;
    border: 1px solid var(--glass-border);
    color: var(--text-main);
}

.btn-outline:hover {
    border-color: var(--color-primary);
    background: rgba(168, 85, 247, 0.1);
}

.btn-lg {
    padding: 1rem 2rem;
    font-size: 1.125rem;
}

/* Navbar */
.navbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: var(--header-height);
    z-index: 1000;
    background: rgba(15, 10, 31, 0.8);
    backdrop-filter: blur(20px);
    border-bottom: 1px solid var(--glass-border);
    display: flex;
    align-items: center;
}

.nav-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}

.nav-logo {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--text-main);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.nav-logo i { color: var(--color-primary); }

.nav-links {
    display: flex;
    gap: 2rem;
    align-items: center;
}

.nav-link {
    color: var(--text-muted);
    font-weight: 500;
    font-size: 0.95rem;
}

.nav-link:hover, .nav-link.active {
    color: var(--text-main);
}

/* Mobile Menu */
.mobile-toggle { display: none; font-size: 1.5rem; cursor: pointer; }

@media (max-width: 768px) {
    .nav-links {
        position: fixed;
        top: var(--header-height);
        left: 0;
        width: 100%;
        background: var(--bg-body);
        flex-direction: column;
        padding: 2rem;
        border-bottom: 1px solid var(--glass-border);
        transform: translateY(-150%);
        transition: transform 0.3s ease;
    }
    .nav-links.active { transform: translateY(0); }
    .mobile-toggle { display: block; }
}

/* Hero Section */
.hero {
    min-height: 100vh;
    display: flex;
    align-items: center;
    padding-top: var(--header-height);
    position: relative;
    overflow: hidden;
}

.hero-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
}

.hero-text h1 { margin-bottom: 1.5rem; }
.hero-text p { font-size: 1.125rem; color: var(--text-muted); margin-bottom: 2rem; }

.hero-visual {
    position: relative;
}

.hero-card {
    position: absolute;
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
}

/* Forms */
.form-group { margin-bottom: 1.5rem; }
.form-label { display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.9rem; }
.form-control {
    width: 100%;
    padding: 0.875rem 1rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-sm);
    color: var(--text-main);
    font-family: inherit;
    transition: 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: var(--color-primary);
    background: rgba(255, 255, 255, 0.08);
}

/* Footer */
.footer {
    background: rgba(0, 0, 0, 0.2);
    border-top: 1px solid var(--glass-border);
    padding: 4rem 0 2rem;
    margin-top: 4rem;
}

.footer-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    gap: 3rem;
    margin-bottom: 3rem;
}

.footer-col h4 { margin-bottom: 1.5rem; }
.footer-links li { margin-bottom: 0.75rem; }
.footer-links a { color: var(--text-muted); }
.footer-links a:hover { color: var(--color-primary); }

@media (max-width: 768px) {
    .hero-content { grid-template-columns: 1fr; text-align: center; }
    .footer-grid { grid-template-columns: 1fr; }
}

/* Animations */
.fade-in-up { animation: fadeInUp 0.8s var(--ease-out) forwards; opacity: 0; transform: translateY(20px); }
.delay-100 { animation-delay: 0.1s; }
.delay-200 { animation-delay: 0.2s; }
.delay-300 { animation-delay: 0.3s; }

@keyframes fadeInUp {
    to { opacity: 1; transform: translateY(0); }
}

/* Page Specifics */
.page-header {
    text-align: center;
    padding: 4rem 0;
    background: radial-gradient(circle at center, rgba(168, 85, 247, 0.1), transparent 70%);
}

.service-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    background: rgba(168, 85, 247, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--color-primary);
    margin-bottom: 1.5rem;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--text-main);
    margin-bottom: 0.5rem;
}

.badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: var(--radius-full);
    font-size: 0.75rem;
    font-weight: 600;
    background: rgba(168, 85, 247, 0.2);
    color: var(--color-primary);
}
"@
Set-Content -Path "c:\laragon\www\boost\public\assets\css\style.css" -Value $content
