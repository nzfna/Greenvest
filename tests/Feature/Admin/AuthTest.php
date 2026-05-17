<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create([
        'email'    => 'test@greenvest.co',
        'password' => bcrypt('Admin#1234'),
    ]);
});

// ── Login ─────────────────────────────────────────────────────────
test('halaman login dapat diakses', function () {
    $response = $this->get(route('admin.login'));
    $response->assertStatus(200);
    $response->assertViewIs('admin.auth.login');
});

test('admin dapat login dengan kredensial valid', function () {
    $response = $this->post(route('admin.login'), [
        'email'    => 'test@greenvest.co',
        'password' => 'Admin#1234',
    ]);
    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticatedAs($this->admin);
});

test('admin tidak dapat login dengan password salah', function () {
    $response = $this->post(route('admin.login'), [
        'email'    => 'test@greenvest.co',
        'password' => 'password_salah',
    ]);
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('admin tidak dapat login dengan email tidak terdaftar', function () {
    $response = $this->post(route('admin.login'), [
        'email'    => 'notfound@greenvest.co',
        'password' => 'Admin#1234',
    ]);
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('login memerlukan field email dan password', function () {
    $response = $this->post(route('admin.login'), []);
    $response->assertSessionHasErrors(['email', 'password']);
});

test('login memerlukan format email valid', function () {
    $response = $this->post(route('admin.login'), [
        'email'    => 'bukan-email',
        'password' => 'Admin#1234',
    ]);
    $response->assertSessionHasErrors('email');
});

// ── Logout ────────────────────────────────────────────────────────
test('admin yang login dapat logout', function () {
    $this->actingAs($this->admin);
    $response = $this->post(route('admin.logout'));
    $response->assertRedirect(route('admin.login'));
    $this->assertGuest();
});

// ── Route Protection ──────────────────────────────────────────────
test('tamu diarahkan ke login saat akses dashboard', function () {
    $response = $this->get(route('admin.dashboard'));
    $response->assertRedirect(route('admin.login'));
});

test('admin yang sudah login dapat akses dashboard', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
    $response->assertStatus(200);
});

// ── Forgot Password ───────────────────────────────────────────────
test('halaman lupa password dapat diakses', function () {
    $response = $this->get(route('admin.forgot'));
    $response->assertStatus(200);
});

test('request reset password dengan email valid', function () {
    $response = $this->post(route('admin.forgot'), [
        'email' => $this->admin->email,
    ]);
    $response->assertRedirect(route('admin.verify'));
});

test('request reset password gagal dengan email tidak terdaftar', function () {
    $response = $this->post(route('admin.forgot'), [
        'email' => 'tidakterdaftar@test.com',
    ]);
    $response->assertSessionHasErrors('email');
});

// ── Redirect if already authenticated ────────────────────────────
test('admin yang sudah login diarahkan dari halaman login', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.login'));
    $response->assertRedirect(); // redirected away from login
});
