import Alpine from 'alpinejs';
import axios from 'axios';

window.Alpine = Alpine;
window.axios  = axios;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

function getCsrf() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

// ── Navbar search toggle ──────────────────────────────────────────
Alpine.data('navSearch', () => ({
    open:  false,
    query: '',
    toggle() { this.open = !this.open; if (this.open) this.$nextTick(() => this.$refs.input?.focus()); },
    submit() { if (this.query.trim()) window.location.href = `/artikel?search=${encodeURIComponent(this.query)}`; },
}));

// ── Category carousel (home) ──────────────────────────────────────
Alpine.data('carousel', (selector) => ({
    el: null,
    init() { this.el = document.querySelector(selector); },
    prev() { this.el?.scrollBy({ left: -340, behavior: 'smooth' }); },
    next() { this.el?.scrollBy({ left:  340, behavior: 'smooth' }); },
}));

// ── Simulation ────────────────────────────────────────────────────
Alpine.data('simulation', () => ({
    typeId:      '',
    amount:      5000000,
    duration:    10,
    loading:     false,
    result:      null,
    amountRaw:   '5000000',

    formatRp(n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID');
    },

    onAmountInput(e) {
        const raw = e.target.value.replace(/\D/g, '');
        this.amountRaw = raw;
        this.amount    = parseInt(raw) || 0;
    },

    async calculate() {
        if (!this.typeId || !this.amount || !this.duration) return;
        this.loading = true;
        try {
            const res = await axios.post('/simulasi/hitung', {
                simulation_type_id: this.typeId,
                initial_amount:     this.amount,
                duration:           this.duration,
            }, { headers: { 'X-CSRF-TOKEN': getCsrf() } });

            const d     = res.data.data;
            this.result = d;

            // Animate total value
            this.$nextTick(() => animateCounter('sim-total', 0, d.total, 1200));
            animateCounter('sim-return', 0, d.estimated_return, 900);
        } catch (err) {
            const errors = err.response?.data?.errors;
            if (errors) {
                const msgs = Object.values(errors).flat().join(' ');
                alert(msgs);
            }
        } finally {
            this.loading = false;
        }
    },
}));

// ── Counter animation helper ──────────────────────────────────────
function animateCounter(id, from, to, duration) {
    const el = document.getElementById(id);
    if (!el) return;
    const start  = performance.now();
    const update = (now) => {
        const elapsed = now - start;
        const progress = Math.min(elapsed / duration, 1);
        const eased    = 1 - Math.pow(1 - progress, 3);
        const current  = Math.round(from + (to - from) * eased);
        el.textContent = 'Rp ' + current.toLocaleString('id-ID');
        if (progress < 1) requestAnimationFrame(update);
    };
    requestAnimationFrame(update);
}

// ── Native Comment Form ───────────────────────────────────────────
Alpine.data('commentForm', (slug) => ({
    name:     '',
    email:    '',
    content:  '',
    loading:  false,
    success:  false,
    error:    '',
    errors:   {},

    async submit() {
        this.loading = true;
        this.error   = '';
        this.errors  = {};

        try {
            await axios.post(`/artikel/${slug}/komentar`, {
                user_name:  this.name,
                user_email: this.email,
                content:    this.content,
                _token:     getCsrf(),
            });
            this.success = true;
            this.name    = '';
            this.email   = '';
            this.content = '';
        } catch (err) {
            if (err.response?.status === 422) {
                this.errors = err.response.data.errors ?? {};
                this.error  = 'Periksa kembali input Anda.';
            } else if (err.response?.status === 403) {
                this.error = 'Anda tidak dapat mengirim komentar.';
            } else {
                this.error = 'Terjadi kesalahan. Coba lagi.';
            }
        } finally {
            this.loading = false;
        }
    },
}));

// ── Share bar ─────────────────────────────────────────────────────
Alpine.data('shareBar', () => ({
    copied: false,
    copy() {
        navigator.clipboard.writeText(window.location.href);
        this.copied = true;
        setTimeout(() => { this.copied = false; }, 2000);
    },
}));

Alpine.start();
