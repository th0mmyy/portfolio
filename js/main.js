'use strict';

const isTouch = window.matchMedia('(hover:none)').matches;
const reduced = window.matchMedia('(prefers-reduced-motion:reduce)').matches;

/* ── Cover images for projects ── */
// For each .proj-cover with data-cover, try loading the image.
// If it loads OK → apply as background. If not → keep the gradient.
document.querySelectorAll('.proj-cover[data-cover]').forEach(el => {
    const src = el.getAttribute('data-cover');
    const img = new Image();
    img.onload = () => {
        el.style.backgroundImage = `url('${src}')`;
        el.classList.add('has-photo');
    };
    img.src = src;
});

/* ── Theme toggle ── */
const html = document.documentElement;
const themeBtn = document.getElementById('theme-toggle');

// Respect system preference on first load
const saved = localStorage.getItem('theme');
if (saved) {
    html.setAttribute('data-theme', saved);
} else if (window.matchMedia('(prefers-color-scheme:light)').matches) {
    html.setAttribute('data-theme', 'light');
}

themeBtn.addEventListener('click', () => {
    const current = html.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
});

/* ── Custom cursor (desktop) ── */
if (!isTouch) {
    const cur = document.createElement('div'); cur.id = 'cursor';
    const dot = document.createElement('div'); dot.id = 'cursor-dot';
    document.body.append(cur, dot);

    document.addEventListener('mousemove', e => {
        cur.style.left = dot.style.left = e.clientX + 'px';
        cur.style.top = dot.style.top = e.clientY + 'px';
    }, { passive: true });

    document.querySelectorAll('a,button,.project,.stat,.cert,.tl-card,.skill-cat').forEach(el => {
        el.addEventListener('mouseenter', () => cur.classList.add('hover'));
        el.addEventListener('mouseleave', () => cur.classList.remove('hover'));
    });
}

/* ── Scroll progress + back to top ── */
const bar = document.getElementById('scroll-progress');
const btt = document.getElementById('btt');

btt.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

let ticking = false;
window.addEventListener('scroll', () => {
    if (ticking) return;
    requestAnimationFrame(() => {
        const s = window.scrollY;
        const h = document.documentElement.scrollHeight - window.innerHeight;
        if (bar) bar.style.width = (h > 0 ? (s / h) * 100 : 0) + '%';
        btt.classList.toggle('show', s > 400);
        ticking = false;
    });
    ticking = true;
}, { passive: true });

/* ── Smooth scroll ── */
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const href = a.getAttribute('href');
        if (href === '#') return;
        const t = document.querySelector(href);
        if (t) { e.preventDefault(); t.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
    });
});

/* ── Fade-in on scroll ── */
if (!reduced) {
    document.querySelectorAll('.section,.project,.stat,.tl-item,.skill-cat,.cert').forEach(el => {
        el.classList.add('fade');
    });
    const obs = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) { e.target.classList.add('in'); obs.unobserve(e.target); }
        });
    }, { threshold: .08, rootMargin: '0px 0px -50px 0px' });
    document.querySelectorAll('.fade').forEach(el => obs.observe(el));
}

/* ── Counter animation ── */
const countObs = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (!e.isIntersecting) return;
        const el = e.target.querySelector('strong');
        if (!el) return;
        const raw = el.textContent.trim();
        const val = parseInt(raw.replace(/\D/g, ''));
        const sfx = raw.includes('+') ? '+' : raw.includes('°') ? '°' : '';
        if (!isNaN(val) && !e.target.classList.contains('no-count') && !reduced) {
            let cur = 0; const step = val / 45;
            const t = setInterval(() => {
                cur += step;
                if (cur >= val) { el.textContent = val + sfx; clearInterval(t); }
                else { el.textContent = Math.floor(cur) + sfx; }
            }, 28);
        }
        countObs.unobserve(e.target);
    });
}, { threshold: .5 });
document.querySelectorAll('.stat').forEach(s => countObs.observe(s));

/* ── Orb parallax (desktop only) ── */
if (!isTouch && !reduced) {
    document.addEventListener('mousemove', e => {
        const cx = (e.clientX - innerWidth / 2) * .007;
        const cy = (e.clientY - innerHeight / 2) * .007;
        document.querySelectorAll('.orb').forEach((o, i) => {
            o.style.transform = `translate(${cx * (i + 1) * .6}px,${cy * (i + 1) * .6}px)`;
        });
    }, { passive: true });
}