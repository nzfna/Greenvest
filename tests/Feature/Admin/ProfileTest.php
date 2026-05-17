<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
    $this->admin = User::factory()->create([
        'password' => Hash::make('OldPass#123'),
    ]);
});

// ── Profile Page ──────────────────────────────────────────────────
test('admin dapat melihat halaman profil', function () {
    $this->actingAs($this->admin)
         ->get(route('admin.profile'))
         ->assertStatus(200)
         ->assertViewIs('admin.profile');
});

// ── Update Name ───────────────────────────────────────────────────
test('admin dapat memperbarui nama panggilan', function () {
    $this->actingAs($this->admin)
         ->putJson(route('admin.profile.update'), ['name' => 'Admin Baru'])
         ->assertJson(['success' => true, 'name' => 'Admin Baru']);

    $this->assertDatabaseHas('users', ['id' => $this->admin->id, 'name' => 'Admin Baru']);
});

test('update nama gagal jika nama kosong', function () {
    $this->actingAs($this->admin)
         ->putJson(route('admin.profile.update'), ['name' => ''])
         ->assertStatus(422);
});

// ── Change Password ───────────────────────────────────────────────
test('admin dapat mengganti password', function () {
    $this->actingAs($this->admin)
         ->putJson(route('admin.profile.password'), [
             'current_password'          => 'OldPass#123',
             'new_password'              => 'NewPass@456',
             'new_password_confirmation' => 'NewPass@456',
         ])
         ->assertJson(['success' => true]);

    $this->assertTrue(Hash::check('NewPass@456', $this->admin->fresh()->password));
});

test('ganti password gagal jika current password salah', function () {
    $this->actingAs($this->admin)
         ->putJson(route('admin.profile.password'), [
             'current_password'          => 'SalahPassword',
             'new_password'              => 'NewPass@456',
             'new_password_confirmation' => 'NewPass@456',
         ])
         ->assertJson(['success' => false]);
});

test('ganti password gagal jika konfirmasi tidak cocok', function () {
    $this->actingAs($this->admin)
         ->putJson(route('admin.profile.password'), [
             'current_password'          => 'OldPass#123',
             'new_password'              => 'NewPass@456',
             'new_password_confirmation' => 'BedaPassword',
         ])
         ->assertStatus(422);
});

test('password baru minimal 8 karakter', function () {
    $this->actingAs($this->admin)
         ->putJson(route('admin.profile.password'), [
             'current_password'          => 'OldPass#123',
             'new_password'              => 'Ab1@',
             'new_password_confirmation' => 'Ab1@',
         ])
         ->assertStatus(422);
});

test('password baru harus mengandung simbol', function () {
    $this->actingAs($this->admin)
         ->putJson(route('admin.profile.password'), [
             'current_password'          => 'OldPass#123',
             'new_password'              => 'Password123',
             'new_password_confirmation' => 'Password123',
         ])
         ->assertStatus(422);
});

// ── Photo Upload ──────────────────────────────────────────────────
test('admin dapat mengunggah foto profil', function () {
    $file = UploadedFile::fake()->image('profile.jpg', 200, 200);

    $response = $this->actingAs($this->admin)
         ->postJson(route('admin.profile.photo'), ['photo' => $file]);

    $response->assertJson(['success' => true]);
    $this->assertNotNull($this->admin->fresh()->photo_profile);
    Storage::disk('public')->assertExists('profiles/' . basename($this->admin->fresh()->photo_profile));
});

test('upload foto gagal jika bukan gambar', function () {
    $file = UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');

    $this->actingAs($this->admin)
         ->postJson(route('admin.profile.photo'), ['photo' => $file])
         ->assertStatus(422);
});

test('admin dapat menghapus foto profil', function () {
    // Upload first
    $file = UploadedFile::fake()->image('profile.jpg', 200, 200);
    $this->actingAs($this->admin)
         ->postJson(route('admin.profile.photo'), ['photo' => $file]);

    // Then delete
    $this->actingAs($this->admin)
         ->deleteJson(route('admin.profile.photo.delete'))
         ->assertJson(['success' => true]);

    $this->assertNull($this->admin->fresh()->photo_profile);
});

// ── Email Change Request ──────────────────────────────────────────
test('admin dapat request perubahan email', function () {
    $this->actingAs($this->admin)
         ->postJson(route('admin.profile.email.request'), [
             'email' => 'newemail@greenvest.co',
         ])
         ->assertJson(['success' => true]);

    $this->assertDatabaseHas('email_verifications', [
        'user_id' => $this->admin->id,
        'email'   => 'newemail@greenvest.co',
    ]);
});

test('request email gagal jika email sudah digunakan', function () {
    User::factory()->create(['email' => 'taken@greenvest.co']);

    $this->actingAs($this->admin)
         ->postJson(route('admin.profile.email.request'), [
             'email' => 'taken@greenvest.co',
         ])
         ->assertStatus(422);
});
