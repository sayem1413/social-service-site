/* ================================================================
   SUBAITA FOUNDATION — Global JavaScript
   ================================================================ */

(function () {
  'use strict';

  /* ── NAV TOGGLE ── */
  function initNav() {
    const toggle = document.getElementById('navToggle');
    const menu   = document.getElementById('mobileNav');
    const navbar = document.getElementById('navbar');
    if (!toggle || !menu) return;

    toggle.addEventListener('click', () => {
      toggle.classList.toggle('open');
      menu.classList.toggle('open');
      document.body.style.overflow = menu.classList.contains('open') ? 'hidden' : '';
    });

    menu.querySelectorAll('a').forEach(a => a.addEventListener('click', () => {
      toggle.classList.remove('open');
      menu.classList.remove('open');
      document.body.style.overflow = '';
    }));

    /* Navbar scroll effect */
    window.addEventListener('scroll', () => {
      if (navbar) navbar.classList.toggle('scrolled', window.scrollY > 40);
    });
  }

  /* ── ACTIVE NAV LINK ── */
  function setActiveLink() {
    const path = window.location.pathname.split('/').pop() || 'index.html';
    document.querySelectorAll('.nav-links a, .mobile-nav a').forEach(a => {
      const href = a.getAttribute('href') || '';
      if (href === path || (path === '' && href === 'index.html')) {
        a.classList.add('active');
      }
    });
  }

  /* ── FADE-UP ANIMATION ON SCROLL ── */
  function initScrollFade() {
    const els = document.querySelectorAll('[data-fade]');
    if (!els.length) return;
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.classList.add('visible');
          observer.unobserve(e.target);
        }
      });
    }, { threshold: 0.12 });
    els.forEach(el => observer.observe(el));
  }

  /* ── FILTER BUTTONS ── */
  function initFilters() {
    document.querySelectorAll('.filter-bar').forEach(bar => {
      bar.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function () {
          bar.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
          this.classList.add('active');
          const filter = this.dataset.filter;
          const grid   = document.querySelector(this.dataset.target || '.filterable-grid');
          if (!grid) return;
          grid.querySelectorAll('[data-cat]').forEach(card => {
            card.style.display = (filter === 'all' || card.dataset.cat === filter) ? '' : 'none';
          });
        });
      });
    });
  }

  /* ── DONATION AMOUNT SELECTOR ── */
  function initDonation() {
    document.querySelectorAll('.amount-btn').forEach(btn => {
      btn.addEventListener('click', function () {
        document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('selected'));
        this.classList.add('selected');
        const input = document.getElementById('customAmount');
        if (input && this.dataset.amount) input.value = this.dataset.amount;
      });
    });
  }

  /* ── COUNTER ANIMATION ── */
  function animateCounters() {
    document.querySelectorAll('.counter').forEach(el => {
      const target = parseInt(el.dataset.target, 10);
      const suffix = el.dataset.suffix || '';
      let current  = 0;
      const step   = Math.ceil(target / 60);
      const timer  = setInterval(() => {
        current = Math.min(current + step, target);
        el.textContent = current.toLocaleString() + suffix;
        if (current >= target) clearInterval(timer);
      }, 24);
    });
  }

  /* ── SMOOTH FORM SUBMIT (demo) ── */
  function initForms() {
    document.querySelectorAll('form[data-demo]').forEach(form => {
      form.addEventListener('submit', e => {
        e.preventDefault();
        const btn = form.querySelector('[type="submit"]');
        if (!btn) return;
        const orig = btn.textContent;
        btn.textContent = '✓ Submitted!';
        btn.style.background = 'var(--green)';
        btn.style.color = 'var(--white)';
        setTimeout(() => { btn.textContent = orig; btn.style.background = ''; btn.style.color = ''; form.reset(); }, 2800);
      });
    });
  }

  /* ── INIT ── */
  document.addEventListener('DOMContentLoaded', () => {
    initNav();
    setActiveLink();
    initScrollFade();
    initFilters();
    initDonation();
    initForms();

    /* Counter observer */
    const counterSection = document.querySelector('.stats-strip');
    if (counterSection) {
      const obs = new IntersectionObserver(entries => {
        if (entries[0].isIntersecting) { animateCounters(); obs.disconnect(); }
      }, { threshold: 0.3 });
      obs.observe(counterSection);
    }
  });
})();
