import Alpine from 'alpinejs';
import axios from 'axios';

window.Alpine = Alpine;
window.axios  = axios;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// ── Toast Helper ─────────────────────────────────────────────────
window.toast = function(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const el = document.createElement('div');
    el.className = `toast toast-${type}`;
    el.innerHTML = `
        <i class="ph ${type === 'success' ? 'ph-check-circle' : 'ph-warning-circle'}"
           style="color:${type === 'success' ? '#059669' : '#EF4444'}"></i>
        <span>${message}</span>
    `;
    container.appendChild(el);

    setTimeout(() => {
        el.style.opacity = '0';
        el.style.transform = 'translateX(20px)';
        el.style.transition = 'all 0.3s';
        setTimeout(() => el.remove(), 300);
    }, 3500);
};

// ── CSRF helper ───────────────────────────────────────────────────
function getCsrf() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}
axios.defaults.headers.common['X-CSRF-TOKEN'] = getCsrf();

// ── Delete Article Modal ──────────────────────────────────────────
Alpine.data('deleteModal', () => ({
    open:        false,
    articleId:   null,
    articleTitle:'',

    show(id, title) {
        this.articleId    = id;
        this.articleTitle = title;
        this.open         = true;
    },

    async confirm() {
        try {
            await axios.delete(`/admin/artikel/${this.articleId}`, {
                headers: { 'X-CSRF-TOKEN': getCsrf() },
            });
            toast('Artikel berhasil dihapus.', 'success');
            this.open = false;
            setTimeout(() => location.reload(), 800);
        } catch {
            toast('Gagal menghapus artikel.', 'error');
        }
    },
}));

// ── Comment Detail Modal ─────────────────────────────────────────
Alpine.data('commentModal', () => ({
    open:    false,
    comment: null,
    reply:   '',
    loading: false,

    show(data) {
        this.comment = data;
        this.reply   = data.admin_reply ?? '';
        this.open    = true;
    },

    close() { this.open = false; },

    async doAction(action) {
        if (!this.comment) return;
        this.loading = true;
        try {
            const res = await axios.post(`/admin/komentar/${this.comment.id}/${action}`);
            toast(res.data.message, 'success');
            if (this.comment) this.comment.status = action === 'approve' ? 'approved' : 'rejected';
            setTimeout(() => location.reload(), 800);
        } catch {
            toast('Gagal melakukan aksi.', 'error');
        } finally {
            this.loading = false;
        }
    },

    async sendReply() {
        if (!this.reply.trim()) return;
        this.loading = true;
        try {
            const res = await axios.post(`/admin/komentar/${this.comment.id}/reply`, {
                content: this.reply,
            });
            toast(res.data.message, 'success');
        } catch {
            toast('Gagal mengirim balasan.', 'error');
        } finally {
            this.loading = false;
        }
    },
}));

// ── Article Editor (Rich Text) ────────────────────────────────────
Alpine.data('articleEditor', (initialContent = '') => ({
    content:       initialContent,
    title:         '',
    categoryId:    '',
    description:   '',
    status:        'published',
    saving:        false,
    previewMode:   false,
    revisionCount: 0,

    init() {
        const editor = this.$refs.editor;
        if (editor && this.content) {
            editor.innerHTML = this.content;
        }
        if (editor) {
            editor.addEventListener('input', () => {
                this.content = editor.innerHTML;
            });
        }
    },

    execCmd(cmd, value = null) {
        document.execCommand(cmd, false, value);
        this.$refs.editor?.focus();
    },

    insertImage() {
        const url = prompt('Masukkan URL gambar:');
        if (url) this.execCmd('insertImage', url);
    },

    insertLink() {
        const url = prompt('Masukkan URL link:');
        if (url) this.execCmd('createLink', url);
    },

    toggleFullscreen() {
        const wrap = this.$refs.editorWrap;
        wrap?.classList.toggle('fullscreen');
    },

    syncContent() {
        this.content = this.$refs.editor?.innerHTML ?? '';
    },
}));

// ── Profile Page ─────────────────────────────────────────────────
Alpine.data('profilePage', () => ({
    saving:          false,
    photoFile:       null,
    deletePhotoOpen: false,

    async saveProfile() {
        this.saving = true;
        try {
            const res = await axios.put('/admin/profil', {
                name: document.getElementById('admin-name').value,
            });
            toast(res.data.message, 'success');
        } catch (err) {
            const msg = err.response?.data?.message ?? 'Terjadi kesalahan.';
            toast(msg, 'error');
        } finally {
            this.saving = false;
        }
    },

    async savePassword() {
        this.saving = true;
        const data  = {
            current_password:          document.getElementById('current-password').value,
            new_password:              document.getElementById('new-password').value,
            new_password_confirmation: document.getElementById('confirm-password').value,
        };
        try {
            const res = await axios.put('/admin/profil/password', data);
            toast(res.data.message, 'success');
            ['current-password','new-password','confirm-password'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });
        } catch (err) {
            const msg = err.response?.data?.message ?? 'Password tidak valid.';
            toast(msg, 'error');
        } finally {
            this.saving = false;
        }
    },

    async uploadPhoto(event) {
        const file = event.target.files[0];
        if (!file) return;

        const fd = new FormData();
        fd.append('photo', file);
        fd.append('_token', getCsrf());

        try {
            const res = await axios.post('/admin/profil/photo', fd, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
            const img = document.getElementById('profile-img');
            if (img) img.src = res.data.photo_url;
            toast(res.data.message, 'success');
        } catch {
            toast('Gagal mengunggah foto.', 'error');
        }
    },

    async deletePhoto() {
        try {
            const res = await axios.delete('/admin/profil/photo');
            const img = document.getElementById('profile-img');
            if (img) img.src = res.data.photo_url;
            toast(res.data.message, 'success');
        } catch {
            toast('Gagal menghapus foto.', 'error');
        }
    },

    async requestEmailChange() {
        const email = document.getElementById('new-email')?.value;
        if (!email) return;
        try {
            const res = await axios.post('/admin/profil/email/request', { email });
            toast(res.data.message, 'success');
        } catch (err) {
            const errors = err.response?.data?.errors?.email?.[0];
            toast(errors ?? 'Terjadi kesalahan.', 'error');
        }
    },
}));

// ── OTP Input ────────────────────────────────────────────────────
Alpine.data('otpInput', () => ({
    cells: ['','','','','',''],

    onInput(idx, event) {
        const val = event.target.value.replace(/\D/g,'');
        this.cells[idx] = val ? val.charAt(val.length - 1) : '';

        const inputs = document.querySelectorAll('.otp-cell');
        if (val && idx < 5) inputs[idx + 1]?.focus();

        // Build full code for hidden input
        document.getElementById('otp-code').value = this.cells.join('');
    },

    onKeydown(idx, event) {
        if (event.key === 'Backspace' && !this.cells[idx] && idx > 0) {
            document.querySelectorAll('.otp-cell')[idx - 1]?.focus();
        }
    },

    onPaste(event) {
        event.preventDefault();
        const text = (event.clipboardData || window.clipboardData).getData('text').replace(/\D/g,'');
        [...text].slice(0, 6).forEach((ch, i) => { this.cells[i] = ch; });
        document.getElementById('otp-code').value = this.cells.join('');
    },
}));

// ── Topbar Search ─────────────────────────────────────────────────
Alpine.data('topbarSearch', () => ({
    query: '',
    results: [],
    open: false,

    async search() {
        if (this.query.length < 2) { this.results = []; this.open = false; return; }
        try {
            const res = await axios.get('/admin/artikel', {
                params: { search: this.query },
                headers: { 'Accept': 'application/json' },
            });
            this.results = res.data.data?.data ?? [];
            this.open = this.results.length > 0;
        } catch { this.results = []; }
    },
}));

// ── Resend timer ─────────────────────────────────────────────────
Alpine.data('resendTimer', (seconds = 60) => ({
    remaining: seconds,
    canResend: false,
    interval:  null,

    init() {
        this.interval = setInterval(() => {
            this.remaining--;
            if (this.remaining <= 0) {
                clearInterval(this.interval);
                this.canResend = true;
            }
        }, 1000);
    },

    get label() {
        return `Retry in ${String(Math.floor(this.remaining / 60)).padStart(1,'0')}:${String(this.remaining % 60).padStart(2,'0')}`;
    },
}));

Alpine.start();
