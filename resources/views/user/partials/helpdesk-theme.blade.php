{{-- TEMA BERSAMA HELPDESK — Editorial Luxury (selaras landing page)
     Simpan sebagai: resources/views/user/partials/helpdesk-theme.blade.php --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Open+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
/* ============================================================
   TOKENS — Palet landing: Cream / Navy / Orange
   ============================================================ */
:root {
    --clr-bg:     #FBFAF5;
    --clr-navy:   #1A2D4A;
    --clr-orange: #F57E21;
    --panel:      #FEFDFA;
    --clr-muted:  rgba(26, 45, 74, 0.65);
    --line:       rgba(26, 45, 74, 0.16);
    --line-light: rgba(251, 250, 245, 0.18);
    --ok:         #2F7A55;
    --danger:     #B3362A;
    --serif:      'Cormorant Garamond', 'Times New Roman', serif;
    --sans:       'Open Sans', sans-serif;
    --ease:       cubic-bezier(0.22, 1, 0.36, 1);
    --ease-io:    cubic-bezier(0.76, 0, 0.24, 1);
}

html { scroll-behavior: smooth; }
::-webkit-scrollbar { width: 10px; height: 10px; }
::-webkit-scrollbar-track { background: #FBFAF5; }
::-webkit-scrollbar-thumb { background: #1A2D4A; border: 2px solid #FBFAF5; border-radius: 8px; }
::-webkit-scrollbar-thumb:hover { background: #F57E21; }
* { scrollbar-width: thin; scrollbar-color: #1A2D4A #FBFAF5; }

.hidden { display: none !important; }
.sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }

/* ============================================================
   PAGE BASE
   ============================================================ */
.hx-page {
    position: relative; min-height: 100vh; overflow-x: hidden;
    background: var(--clr-bg); color: var(--clr-navy); font-family: var(--sans);
    padding: 150px 0 120px;
}
.hx-page *, .hx-page *::before, .hx-page *::after { box-sizing: border-box; }
.hx-page a { text-decoration: none; }
.hx-page h1, .hx-page h2, .hx-page h3, .hx-page h4, .hx-page h5, .hx-page p { margin: 0; }
.hx-page::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 780px; pointer-events: none;
    background: radial-gradient(540px circle at var(--mx, 72%) var(--my, 22%), rgba(245,126,33,.11), transparent 62%);
}
.hx-wrap { position: relative; z-index: 2; width: 100%; max-width: 1120px; margin: 0 auto; padding: 0 28px; }

.hx-grain {
    position: fixed; inset: 0; z-index: 60; pointer-events: none; opacity: .055;
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='180' height='180'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='.85' numOctaves='2' stitchTiles='stitch'/><feColorMatrix values='0 0 0 0 .1 0 0 0 0 .18 0 0 0 0 .29 0 0 0 .9 0'/></filter><rect width='100%' height='100%' filter='url(%23n)'/></svg>");
}
.hx-progress {
    position: fixed; top: 0; left: 0; height: 3px; width: 100%; z-index: 1001;
    background: linear-gradient(90deg, var(--clr-orange), #ffb066); transform-origin: left center; transform: scaleX(0);
}

/* ============================================================
   TYPE
   ============================================================ */
.hx-eyebrow {
    display: inline-flex; align-items: center; gap: 14px;
    font-size: .7rem; font-weight: 700; letter-spacing: .32em; text-transform: uppercase; color: var(--clr-orange);
}
.hx-eyebrow::before { content: ''; width: 46px; height: 1px; background: currentColor; display: block; }
.hx-display { font-family: var(--serif); font-weight: 500; line-height: .98; letter-spacing: -.015em; color: var(--clr-navy); }
.hx-display em, .hx-h2 em { font-style: italic; font-weight: 500; color: var(--clr-orange); }
.hx-title { font-size: clamp(3rem, 7vw, 5.6rem); margin: 26px 0 28px; }
.hx-h2 { font-family: var(--serif); font-weight: 500; font-size: clamp(1.8rem, 3.2vw, 2.6rem); line-height: 1.05; color: var(--clr-navy); }
.hx-lead { font-size: 1rem; line-height: 1.85; color: var(--clr-muted); max-width: 560px; }
.hx-sub { margin-top: 10px; font-size: .9rem; color: var(--clr-muted); }
.hx-mono { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }

.hx-hero { position: relative; padding-bottom: 72px; }
.hx-ghost {
    position: absolute; right: -10px; top: -30px; pointer-events: none; user-select: none; white-space: nowrap;
    font-family: var(--serif); font-style: italic; font-weight: 500; font-size: clamp(6rem, 17vw, 14rem); line-height: 1;
    color: transparent; -webkit-text-stroke: 1px rgba(26,45,74,.1);
}

/* ============================================================
   REVEAL (tanpa floating / scale)
   ============================================================ */
.hx-mask { display: block; overflow: hidden; padding-bottom: .12em; margin-bottom: -.12em; }
.hx-mask > span { display: block; transform: translateY(112%); transition: transform 1.1s var(--ease); transition-delay: var(--d, 0s); }
.is-in .hx-mask > span { transform: translateY(0); }
.hx-fade { opacity: 0; filter: blur(8px); transition: opacity 1s var(--ease), filter 1s var(--ease); transition-delay: var(--d, 0s); }
.hx-fade.is-in, .is-in .hx-fade { opacity: 1; filter: blur(0); }
@keyframes hx-in { from { opacity: 0; filter: blur(8px); } to { opacity: 1; filter: blur(0); } }
@keyframes hx-clip { from { clip-path: inset(0 0 100% 0); } to { clip-path: inset(0 0 0 0); } }

/* ============================================================
   BUTTONS & LINKS
   ============================================================ */
/* two-layer sweep: ::before = navy base, ::after = orange sweep */
.hx-btn {
    position: relative; display: inline-flex; align-items: center; justify-content: center; gap: 14px; overflow: hidden;
    padding: 18px 32px; border: 1px solid var(--clr-navy); background: transparent; color: var(--clr-bg);
    font-family: var(--sans); font-weight: 700; font-size: .72rem; letter-spacing: .22em; text-transform: uppercase;
    cursor: pointer; transition: border-color .5s var(--ease);
}
.hx-btn::before { content: ''; position: absolute; inset: 0; z-index: 0; background: var(--clr-navy); }
.hx-btn::after  { content: ''; position: absolute; inset: 0; z-index: 1; background: var(--clr-orange); transform: translateX(-101%); transition: transform .65s var(--ease-io); }
.hx-btn > * { position: relative; z-index: 2; }
.hx-btn:hover { border-color: var(--clr-orange); }
.hx-btn:hover::after { transform: translateX(0); }
.hx-btn i { transition: transform .5s var(--ease); }
.hx-btn:hover i.arrow { transform: translateX(6px); }
.hx-btn.ghost { color: var(--clr-navy); }
.hx-btn.ghost::before { background: transparent; }
.hx-btn.ghost::after  { background: var(--clr-navy); }
.hx-btn.ghost:hover   { color: var(--clr-bg); border-color: var(--clr-navy); }
.hx-btn.sm { padding: 13px 22px; font-size: .66rem; }

.hx-link {
    position: relative; display: inline-flex; align-items: center; gap: 10px; padding-bottom: 6px; background: none; border: 0; cursor: pointer;
    font-family: var(--sans); font-weight: 700; font-size: .7rem; letter-spacing: .22em; text-transform: uppercase; color: var(--clr-navy);
    transition: color .4s var(--ease);
}
.hx-link::after { content: ''; position: absolute; left: 0; bottom: 0; height: 1px; width: 100%; background: var(--clr-orange); transform-origin: right; transition: transform .6s var(--ease-io); }
.hx-link:hover { color: var(--clr-orange); }
.hx-link:hover::after { transform-origin: left; }
.hx-link.danger:hover { color: var(--danger); }
.hx-link.danger:hover::after { background: var(--danger); }

/* ============================================================
   TAG
   ============================================================ */
.hx-tag {
    display: inline-flex; align-items: center; gap: 7px; padding: 5px 11px; border: 1px solid var(--line); white-space: nowrap;
    font-family: var(--sans); font-size: .6rem; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; color: var(--clr-navy); background: transparent;
}
.hx-tag--orange { color: var(--clr-orange); border-color: rgba(245,126,33,.5); }
.hx-tag--ok     { color: var(--ok); border-color: rgba(47,122,85,.45); }
.hx-tag--bad    { color: var(--danger); border-color: rgba(179,54,42,.45); }
.hx-tag--mute   { color: var(--clr-muted); }
.hx-tag--solid  { background: var(--clr-navy); border-color: var(--clr-navy); color: var(--clr-bg); }

/* ============================================================
   FORM CONTROLS
   ============================================================ */
.hx-label { display: block; margin-bottom: 6px; font-size: .64rem; font-weight: 700; letter-spacing: .24em; text-transform: uppercase; color: var(--clr-navy); }
.hx-label .req { color: var(--clr-orange); margin-left: 4px; }
.hx-hint { margin-top: 10px; font-size: .76rem; color: var(--clr-muted); line-height: 1.6; }

.form-input {
    display: block; width: 100%; background: transparent; border: 0; border-bottom: 1px solid rgba(26,45,74,.35); border-radius: 0;
    padding: 14px 0; font-family: var(--sans); font-size: .95rem; color: var(--clr-navy);
    transition: border-color .4s var(--ease), box-shadow .4s var(--ease), background .4s var(--ease);
}
.form-input::placeholder { color: rgba(26,45,74,.38); }
.form-input:focus { outline: none; border-bottom-color: var(--clr-orange); box-shadow: 0 1px 0 0 var(--clr-orange); }
textarea.form-input { border: 1px solid rgba(26,45,74,.28); padding: 14px 16px; line-height: 1.75; resize: vertical; background: rgba(255,255,255,.55); }
textarea.form-input:focus { border-color: var(--clr-orange); box-shadow: none; background: #fff; }
select.form-input {
    appearance: none; -webkit-appearance: none; cursor: pointer; padding-right: 30px;
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'><path d='M1 1l5 5 5-5' fill='none' stroke='%231A2D4A' stroke-width='1.5'/></svg>");
    background-repeat: no-repeat; background-position: right 4px center;
}
select.form-input option { background: #FBFAF5; color: #1A2D4A; }

.hx-check {
    appearance: none; -webkit-appearance: none; flex-shrink: 0; position: relative; margin: 0; cursor: pointer;
    width: 20px; height: 20px; border: 1px solid var(--clr-navy); background: transparent;
    transition: background .4s var(--ease), border-color .4s var(--ease);
}
.hx-check::after {
    content: ''; position: absolute; left: 6px; top: 2px; width: 5px; height: 10px;
    border: solid var(--clr-orange); border-width: 0 2px 2px 0; transform: rotate(45deg); opacity: 0; transition: opacity .3s var(--ease);
}
.hx-check:checked { background: var(--clr-navy); }
.hx-check:checked::after { opacity: 1; }
.hx-check:disabled { opacity: .3; cursor: not-allowed; }
.hx-check:focus-visible { outline: 2px solid var(--clr-orange); outline-offset: 3px; }

/* ============================================================
   SWEETALERT — selaras tema
   ============================================================ */
.swal2-popup { border-radius: 0 !important; background: #FBFAF5 !important; border: 1px solid var(--line); font-family: var(--sans) !important; padding: 2.2em 2em !important; }
.swal2-title { font-family: var(--serif) !important; font-weight: 500 !important; font-size: 2rem !important; color: var(--clr-navy) !important; }
.swal2-html-container { color: var(--clr-muted) !important; line-height: 1.75 !important; }
.swal2-styled { border-radius: 0 !important; font-size: .7rem !important; font-weight: 700 !important; letter-spacing: .18em !important; text-transform: uppercase; padding: 14px 26px !important; box-shadow: none !important; }
.swal2-styled.swal2-confirm { background: var(--clr-navy) !important; }
.swal2-styled.swal2-cancel { background: transparent !important; color: var(--clr-navy) !important; border: 1px solid var(--clr-navy) !important; }

/* ============================================================
   RESPONSIVE / MOTION
   ============================================================ */
@media (max-width: 767px) {
    .hx-page { padding: 118px 0 90px; }
    .hx-wrap { padding: 0 20px; }
    .hx-ghost { display: none; }
    .hx-hero { padding-bottom: 48px; }
}
@media (prefers-reduced-motion: reduce) {
    .hx-mask > span, .hx-fade { transition: none !important; transform: none !important; opacity: 1 !important; filter: none !important; }
    .item-card, .hx-modal-panel { animation: none !important; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Grain + progress bar scroll
    var grain = document.createElement('div');
    grain.className = 'hx-grain'; grain.setAttribute('aria-hidden', 'true');
    document.body.appendChild(grain);

    var bar = document.createElement('div');
    bar.className = 'hx-progress';
    document.body.appendChild(bar);

    function onScroll() {
        var h = document.documentElement.scrollHeight - window.innerHeight;
        bar.style.transform = 'scaleX(' + (h > 0 ? Math.min(window.scrollY / h, 1) : 0) + ')';
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll);
    onScroll();

    // Reveal saat masuk viewport
    var els = document.querySelectorAll('[data-reveal]');
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); }
            });
        }, { threshold: 0, rootMargin: '0px 0px -6% 0px' });
        els.forEach(function (el) { io.observe(el); });
    } else {
        els.forEach(function (el) { el.classList.add('is-in'); });
    }

    // Cahaya mengikuti kursor
    var page = document.querySelector('.hx-page');
    if (page && !reduce) {
        page.addEventListener('mousemove', function (e) {
            var r = page.getBoundingClientRect();
            page.style.setProperty('--mx', (e.clientX - r.left) + 'px');
            page.style.setProperty('--my', (e.clientY - r.top) + 'px');
        });
    }
});
</script>
