import { Page } from '@playwright/test';
// @ts-check
const { test, expect } = require('@playwright/test');

const BASE_URL    = 'http://localhost:8000';
const ADMIN_EMAIL = 'admingreenvest@gmail.com';
const ADMIN_PASS  = 'Admin#1234';

// ══════════════════════════════════════════════════════════════════
// USER SITE TESTS
// ══════════════════════════════════════════════════════════════════

test.describe('User Site', () => {

    test.describe('Homepage', () => {
        test('homepage tampil dengan navbar dan hero', async ({ page }) => {
            await page.goto(BASE_URL);
            await expect(page).toHaveTitle(/Greenvest/i);
            await expect(page.locator('.navbar-brand')).toBeVisible();
            await expect(page.locator('.hero-title')).toBeVisible();
            await expect(page.locator('.btn-cta-primary')).toBeVisible();
            await expect(page.locator('.btn-cta-secondary')).toBeVisible();
        });

        test('navbar link Artikel menuju halaman artikel', async ({ page }) => {
            await page.goto(BASE_URL);
            await page.click('a[href*="/artikel"]:not([href*="slug"])');
            await expect(page).toHaveURL(/\/artikel/);
            await expect(page.locator('.artikel-hero-title')).toBeVisible();
        });

        test('navbar link Simulasi menuju halaman simulasi', async ({ page }) => {
            await page.goto(BASE_URL);
            await page.click('a[href*="/simulasi"]');
            await expect(page).toHaveURL(/\/simulasi/);
            await expect(page.locator('.simulation-title')).toBeVisible();
        });

        test('tombol search navbar berfungsi', async ({ page }) => {
            await page.goto(BASE_URL);
            await page.click('.navbar-search-btn');
            await expect(page.locator('input[placeholder="Cari artikel..."]')).toBeVisible();
            await page.fill('input[placeholder="Cari artikel..."]', 'Green Bonds');
            await page.keyboard.press('Enter');
            await expect(page).toHaveURL(/search=Green/);
        });

        test('featured card artikel tampil dan dapat diklik', async ({ page }) => {
            await page.goto(BASE_URL);
            const card = page.locator('.featured-card').first();
            if (await card.isVisible()) {
                await card.click();
                await expect(page).toHaveURL(/\/artikel\//);
            }
        });
    });

    test.describe('Artikel Index', () => {
        test('halaman artikel menampilkan grid artikel', async ({ page }) => {
            await page.goto(`${BASE_URL}/artikel`);
            await expect(page.locator('.artikel-hero-title')).toContainText('ARTIKEL');
            await expect(page.locator('.articles-grid')).toBeVisible();
            await expect(page.locator('.category-tabs')).toBeVisible();
        });

        test('filter kategori berfungsi', async ({ page }) => {
            await page.goto(`${BASE_URL}/artikel`);
            const tabs = page.locator('.category-tab');
            const count = await tabs.count();
            if (count > 1) {
                await tabs.nth(1).click();
                await expect(page).toHaveURL(/category=/);
            }
        });

        test('pencarian artikel berfungsi', async ({ page }) => {
            await page.goto(`${BASE_URL}/artikel`);
            // Search via navbar
            await page.click('.navbar-search-btn');
            await page.fill('input[placeholder="Cari artikel..."]', 'Greenvest');
            await page.keyboard.press('Enter');
            await expect(page).toHaveURL(/search=Greenvest/);
        });

        test('pagination tampil jika ada lebih dari 1 halaman', async ({ page }) => {
            await page.goto(`${BASE_URL}/artikel`);
            const pagination = page.locator('.pagination');
            // Only assert visible if it exists
            const count = await pagination.count();
            if (count > 0) {
                await expect(pagination).toBeVisible();
            }
        });
    });

    test.describe('Artikel Detail', () => {
        test('halaman detail artikel menampilkan konten', async ({ page }) => {
            // Navigate from index to first article
            await page.goto(`${BASE_URL}/artikel`);
            const firstLink = page.locator('.read-more-link').first();
            if (await firstLink.isVisible()) {
                await firstLink.click();
                await expect(page.locator('.article-main-title')).toBeVisible();
                await expect(page.locator('.article-prose')).toBeVisible();
                await expect(page.locator('.comments-section')).toBeVisible();
            }
        });

        test('breadcrumb tampil di halaman artikel', async ({ page }) => {
            await page.goto(`${BASE_URL}/artikel`);
            const firstLink = page.locator('.read-more-link').first();
            if (await firstLink.isVisible()) {
                await firstLink.click();
                await expect(page.locator('.article-breadcrumb')).toBeVisible();
            }
        });

        test('sidebar artikel tampil', async ({ page }) => {
            await page.goto(`${BASE_URL}/artikel`);
            const firstLink = page.locator('.read-more-link').first();
            if (await firstLink.isVisible()) {
                await firstLink.click();
                await expect(page.locator('.article-sidebar')).toBeVisible();
            }
        });

        test('form komentar dapat diisi dan dikirim', async ({ page }) => {
            await page.goto(`${BASE_URL}/artikel`);
            const firstLink = page.locator('.read-more-link').first();
            if (await firstLink.isVisible()) {
                await firstLink.click();

                // Fill comment form
                await page.fill('input[placeholder="Contoh: Budi Hijau"]', 'Test User E2E');
                await page.fill('textarea[placeholder*="pendapat"]', 'Komentar test dari Playwright end-to-end.');

                // Click submit
                await page.click('.btn-submit-comment');

                // Wait for success message
                await expect(page.locator('text=Komentar berhasil dikirim')).toBeVisible({ timeout: 5000 });
            }
        });

        test('share bar tampil di halaman artikel', async ({ page }) => {
            await page.goto(`${BASE_URL}/artikel`);
            const firstLink = page.locator('.read-more-link').first();
            if (await firstLink.isVisible()) {
                await firstLink.click();
                await expect(page.locator('.share-bar')).toBeVisible();
            }
        });
    });

    test.describe('Simulasi', () => {
        test('halaman simulasi menampilkan form dan result card', async ({ page }) => {
            await page.goto(`${BASE_URL}/simulasi`);
            await expect(page.locator('.simulation-title')).toBeVisible();
            await expect(page.locator('.simulation-form-card')).toBeVisible();
            await expect(page.locator('.simulation-result-card')).toBeVisible();
        });

        test('simulasi dapat dihitung dengan parameter valid', async ({ page }) => {
            await page.goto(`${BASE_URL}/simulasi`);

            // Select first simulation type
            await page.selectOption('.sim-select', { index: 1 });

            // Fill amount
            await page.fill('.sim-input', '5000000');

            // Set duration via slider
            await page.locator('.sim-range').fill('10');

            // Click calculate
            await page.click('.btn-simulate-action');

            // Wait for result to appear
            await expect(page.locator('#sim-total')).toBeVisible({ timeout: 8000 });

            // Result should contain "Rp"
            const totalText = await page.locator('#sim-total').textContent();
            expect(totalText).toContain('Rp');
        });

        test('tombol simulasi disabled jika belum pilih jenis investasi', async ({ page }) => {
            await page.goto(`${BASE_URL}/simulasi`);
            const btn = page.locator('.btn-simulate-action');
            await expect(btn).toBeDisabled();
        });
    });

});

// ══════════════════════════════════════════════════════════════════
// ADMIN PORTAL TESTS
// ══════════════════════════════════════════════════════════════════

test.describe('Admin Portal', () => {

    test.describe('Authentication', () => {
        test('halaman login admin tampil', async ({ page }) => {
            await page.goto(`${BASE_URL}/admin/login`);
            await expect(page.locator('h1')).toContainText('Admin Login');
            await expect(page.locator('input[name="email"]')).toBeVisible();
            await expect(page.locator('input[name="password"]')).toBeVisible();
        });

        test('login dengan kredensial valid berhasil', async ({ page }) => {
            await page.goto(`${BASE_URL}/admin/login`);
            await page.fill('input[name="email"]', ADMIN_EMAIL);
            await page.fill('input[name="password"]', ADMIN_PASS);
            await page.click('.btn-primary');

            await expect(page).toHaveURL(/admin\/dashboard/, { timeout: 8000 });
            await expect(page.locator('.page-title')).toContainText('Dashboard Admin');
        });

        test('login dengan password salah menampilkan error', async ({ page }) => {
            await page.goto(`${BASE_URL}/admin/login`);
            await page.fill('input[name="email"]', ADMIN_EMAIL);
            await page.fill('input[name="password"]', 'wrong_password');
            await page.click('.btn-primary');

            await expect(page.locator('text=Kredensial tidak valid')).toBeVisible({ timeout: 5000 });
        });

        test('halaman lupa password dapat diakses', async ({ page }) => {
            await page.goto(`${BASE_URL}/admin/lupa-password`);
            await expect(page.locator('h1')).toContainText('Lupa Kata Sandi');
        });
    });

    // Helper: login as admin
    async function loginAdmin(page) {
        await page.goto(`${BASE_URL}/admin/login`);
        await page.fill('input[name="email"]', ADMIN_EMAIL);
        await page.fill('input[name="password"]', ADMIN_PASS);
        await page.click('.btn-primary');
        await page.waitForURL(/admin\/dashboard/);
    }

    test.describe('Dashboard', () => {
        test('dashboard admin menampilkan aktivitas terkini', async ({ page }) => {
            await loginAdmin(page);
            await expect(page.locator('text=Aktivitas Terkini')).toBeVisible();
            await expect(page.locator('.activity-grid')).toBeVisible();
        });

        test('dashboard menampilkan panel artikel dan komentar terbaru', async ({ page }) => {
            await loginAdmin(page);
            await expect(page.locator('text=Artikel Terbaru')).toBeVisible();
            await expect(page.locator('text=Komentar Terbaru')).toBeVisible();
        });

        test('sidebar navigasi berfungsi', async ({ page }) => {
            await loginAdmin(page);
            await page.click('a[href*="/admin/artikel"]');
            await expect(page).toHaveURL(/admin\/artikel/);
        });
    });

    test.describe('Kelola Artikel', () => {
        test('halaman kelola artikel menampilkan daftar artikel', async ({ page }) => {
            await loginAdmin(page);
            await page.goto(`${BASE_URL}/admin/artikel`);
            await expect(page.locator('.page-title')).toContainText('Kelola Artikel');
            await expect(page.locator('.table-wrap')).toBeVisible();
        });

        test('form tambah artikel dapat diakses', async ({ page }) => {
            await loginAdmin(page);
            await page.goto(`${BASE_URL}/admin/artikel/tambah`);
            await expect(page.locator('.page-title')).toContainText('Tambah Artikel');
            await expect(page.locator('.rich-editor')).toBeVisible();
        });

        test('admin dapat membuat artikel baru', async ({ page }) => {
            await loginAdmin(page);
            await page.goto(`${BASE_URL}/admin/artikel/tambah`);

            // Fill title
            await page.fill('input[name="title"]', 'Artikel Test Playwright');

            // Type in rich editor
            await page.click('.rich-editor');
            await page.keyboard.type('Ini adalah konten artikel yang dibuat melalui Playwright.');

            // Wait for Alpine to sync content
            await page.waitForTimeout(500);

            // Select category (first option)
            await page.selectOption('select[name="category_id"]', { index: 0 });

            // Click save
            await page.click('button[type="submit"]');

            // Should redirect to articles list
            await expect(page).toHaveURL(/admin\/artikel$/, { timeout: 8000 });
        });

        test('tombol hapus artikel membuka modal konfirmasi', async ({ page }) => {
            await loginAdmin(page);
            await page.goto(`${BASE_URL}/admin/artikel`);

            const deleteBtn = page.locator('.action-btn-delete').first();
            if (await deleteBtn.isVisible()) {
                await deleteBtn.click();
                await expect(page.locator('.modal-backdrop')).toBeVisible();
                await expect(page.locator('.modal-title')).toContainText('Hapus Artikel?');

                // Click batal
                await page.click('.btn-cancel');
                await expect(page.locator('.modal-backdrop')).not.toBeVisible();
            }
        });
    });

    test.describe('Kelola Komentar', () => {
        test('halaman komentar dapat diakses', async ({ page }) => {
            await loginAdmin(page);
            await page.goto(`${BASE_URL}/admin/komentar`);
            await expect(page.locator('.page-title')).toContainText('Kelola Komentar');
        });

        test('filter komentar berfungsi', async ({ page }) => {
            await loginAdmin(page);
            await page.goto(`${BASE_URL}/admin/komentar`);
            await page.selectOption('select[name="status"]', 'pending');
            await page.click('button[type="submit"]');
            await expect(page).toHaveURL(/status=pending/);
        });
    });

    test.describe('Profil Admin', () => {
        test('halaman profil admin tampil', async ({ page }) => {
            await loginAdmin(page);
            await page.goto(`${BASE_URL}/admin/profil`);
            await expect(page.locator('.page-title')).toContainText('Profil Admin');
            await expect(page.locator('#admin-name')).toBeVisible();
        });
    });

    test.describe('Logs', () => {
        test('halaman logs aktivitas tampil', async ({ page }) => {
            await loginAdmin(page);
            await page.goto(`${BASE_URL}/admin/logs`);
            await expect(page.locator('.page-title')).toContainText('Riwayat Aktivitas');
            await expect(page.locator('.table-wrap')).toBeVisible();
        });
    });

    test.describe('Security', () => {
        test('tamu tidak dapat akses dashboard admin', async ({ page }) => {
            await page.goto(`${BASE_URL}/admin/dashboard`);
            await expect(page).toHaveURL(/admin\/login/);
        });

        test('admin dapat logout', async ({ page }) => {
            await loginAdmin(page);
            await page.click('form[action*="logout"] button');
            await expect(page).toHaveURL(/admin\/login/, { timeout: 5000 });
        });
    });

});
