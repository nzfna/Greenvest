import Alpine from 'alpinejs';
import axios from 'axios';

window.Alpine = Alpine;
window.axios  = axios;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

function getCsrf() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

// ══════════════════════════════════════════════════════════════════
// LITERACY PROGRESS SYSTEM
// Disimpan di localStorage — admin tidak tahu, tidak ada di database.
//
// Aturan poin per minggu:
//   +1%  baca 1 artikel (per artikel unik)
//   +3%  tulis 1 komentar (per artikel unik)
//   +2%  coba simulasi klik "Hitung" (cukup sekali per minggu)
//   Max: 100%, reset setiap minggu baru
// ══════════════════════════════════════════════════════════════════

const LITERACY_KEY = 'gv_literacy';

function getISOWeek() {
    const d    = new Date();
    const jan1 = new Date(d.getFullYear(), 0, 1);
    const week = Math.ceil(((d - jan1) / 86400000 + jan1.getDay() + 1) / 7);
    return `${d.getFullYear()}-W${String(week).padStart(2, '0')}`;
}

function getLiteracy() {
    try {
        const raw      = localStorage.getItem(LITERACY_KEY);
        const data     = raw ? JSON.parse(raw) : null;
        const thisWeek = getISOWeek();
        if (!data || data.week !== thisWeek) {
            return { pct: 0, week: thisWeek, readArticles: [], commentedArticles: [], simulationDone: false };
        }
        return data;
    } catch {
        return { pct: 0, week: getISOWeek(), readArticles: [], commentedArticles: [], simulationDone: false };
    }
}

function saveLiteracy(d) {
    try { localStorage.setItem(LITERACY_KEY, JSON.stringify(d)); } catch { /* private mode */ }
}

function dispatchUpdate(pct) {
    window.dispatchEvent(new CustomEvent('literacy-updated', { detail: pct }));
}

// +1% per artikel unik yang dibaca
window.literacyReadArticle = function (slug) {
    const d = getLiteracy();
    if (d.readArticles.includes(slug)) return;
    d.readArticles.push(slug);
    d.pct = Math.min(100, d.pct + 1);
    saveLiteracy(d);
    dispatchUpdate(d.pct);
};

// +3% per artikel unik yang dikomentar
window.literacyComment = function (slug) {
    const d = getLiteracy();
    if (d.commentedArticles.includes(slug)) return;
    d.commentedArticles.push(slug);
    d.pct = Math.min(100, d.pct + 3);
    saveLiteracy(d);
    dispatchUpdate(d.pct);
};

// +2% sekali per minggu saat klik Hitung Simulasi
window.literacySimulation = function () {
    const d = getLiteracy();
    if (d.simulationDone) return;
    d.simulationDone = true;
    d.pct = Math.min(100, d.pct + 2);
    saveLiteracy(d);
    dispatchUpdate(d.pct);
};

// ── Alpine: Literacy Widget ───────────────────────────────────────
Alpine.data('literacyWidget', () => ({
    pct:         0,
    animatedPct: 0,

    init() {
        this.pct         = getLiteracy().pct;
        this.animatedPct = this.pct;
        this.$nextTick(() => this.animate(0, this.pct));

        window.addEventListener('literacy-updated', (e) => {
            const prev = this.pct;
            this.pct   = e.detail;
            this.animate(prev, this.pct);
        });
    },

    animate(from, to) {
        const dur = 700;
        const t0  = performance.now();
        const run = (now) => {
            const p    = Math.min((now - t0) / dur, 1);
            const ease = 1 - Math.pow(1 - p, 3);
            this.animatedPct = Math.round(from + (to - from) * ease);
            if (p < 1) requestAnimationFrame(run);
        };
        requestAnimationFrame(run);
    },

    get displayText() {
        return this.pct >= 100 ? '100 🔥' : this.animatedPct + '%';
    },

    get barColor() {
        if (this.pct >= 100) return '#D97706';
        if (this.pct >= 75)  return '#059669';
        if (this.pct >= 50)  return '#2D6A4F';
        return '#40916C';
    },

    get statusLabel() {
        if (this.pct === 0)  return 'Mulai baca artikel pertamamu!';
        if (this.pct < 25)   return 'Awal yang bagus, terus belajar!';
        if (this.pct < 50)   return 'Semangat, sudah ' + this.pct + '%!';
        if (this.pct < 75)   return 'Hampir setengah jalan!';
        if (this.pct < 100)  return 'Luar biasa, sedikit lagi!';
        return 'Literasi penuh minggu ini!';
    },
}));

// ── Navbar search toggle ──────────────────────────────────────────
Alpine.data('navSearch', () => ({
    open: false, query: '',
    toggle() { this.open = !this.open; if (this.open) this.$nextTick(() => this.$refs.input?.focus()); },
    submit() { if (this.query.trim()) window.location.href = `/artikel?search=${encodeURIComponent(this.query)}`; },
}));

// ── Category carousel ─────────────────────────────────────────────
Alpine.data('carousel', (selector) => ({
    el: null,
    init() { this.el = document.querySelector(selector); },
    prev() { this.el?.scrollBy({ left: -340, behavior: 'smooth' }); },
    next() { this.el?.scrollBy({ left:  340, behavior: 'smooth' }); },
}));

// ── Simulation ────────────────────────────────────────────────────
Alpine.data('simulation', () => ({
    typeId: '', amount: 5000000, duration: 10, loading: false, result: null,

    formatRp(n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); },

    onAmountInput(e) {
        const raw   = e.target.value.replace(/\D/g, '');
        this.amount = parseInt(raw) || 0;
    },

    async calculate() {
        if (!this.typeId || !this.amount || !this.duration) return;
        this.loading = true;
        try {
            const res   = await axios.post('/simulasi/hitung', {
                simulation_type_id: this.typeId,
                initial_amount:     this.amount,
                duration:           this.duration,
            }, { headers: { 'X-CSRF-TOKEN': getCsrf() } });

            const d     = res.data.data;
            this.result = d;

            // +2% literacy: coba simulasi
            window.literacySimulation();

            this.$nextTick(() => {
                animateCounter('sim-total',  0, d.total,            1200);
                animateCounter('sim-return', 0, d.estimated_return,  900);
            });
        } catch (err) {
            const errors = err.response?.data?.errors;
            if (errors) alert(Object.values(errors).flat().join('\n'));
        } finally {
            this.loading = false;
        }
    },
}));

function animateCounter(id, from, to, duration) {
    const el = document.getElementById(id);
    if (!el) return;
    const t0 = performance.now();
    const go = (now) => {
        const p = Math.min((now - t0) / duration, 1);
        el.textContent = 'Rp ' + Math.round(from + (to - from) * (1 - Math.pow(1 - p, 3))).toLocaleString('id-ID');
        if (p < 1) requestAnimationFrame(go);
    };
    requestAnimationFrame(go);
}

// ── Comment Form ──────────────────────────────────────────────────
Alpine.data('commentForm', (slug) => ({
    name: '', email: '', content: '', loading: false, success: false, error: '', errors: {},

    async submit() {
        this.loading = true; this.error = ''; this.errors = {};
        try {
            await axios.post(`/artikel/${slug}/komentar`, {
                user_name: this.name, user_email: this.email, content: this.content, _token: getCsrf(),
            });
            this.success = true; this.name = ''; this.email = ''; this.content = '';
            window.literacyComment(slug); // +3% literacy
        } catch (err) {
            if (err.response?.status === 422) { this.errors = err.response.data.errors ?? {}; this.error = 'Periksa kembali input Anda.'; }
            else if (err.response?.status === 403) { this.error = 'Anda tidak dapat mengirim komentar.'; }
            else { this.error = 'Terjadi kesalahan. Coba lagi.'; }
        } finally { this.loading = false; }
    },
}));

// ── Share bar ─────────────────────────────────────────────────────
Alpine.data('shareBar', () => ({
    copied: false,
    copy() { navigator.clipboard.writeText(window.location.href); this.copied = true; setTimeout(() => { this.copied = false; }, 2000); },
}));

Alpine.start();
