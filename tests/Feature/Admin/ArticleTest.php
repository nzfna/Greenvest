<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
    $this->admin    = User::factory()->create();
    $this->category = Category::factory()->create(['name' => 'General', 'slug' => 'general']);
});

// ── Index ─────────────────────────────────────────────────────────
test('admin dapat melihat halaman daftar artikel', function () {
    $this->actingAs($this->admin)
         ->get(route('admin.articles.index'))
         ->assertStatus(200)
         ->assertViewIs('admin.articles.index');
});

test('tamu tidak dapat akses halaman artikel admin', function () {
    $this->get(route('admin.articles.index'))
         ->assertRedirect(route('admin.login'));
});

// ── Create ────────────────────────────────────────────────────────
test('admin dapat melihat form tambah artikel', function () {
    $this->actingAs($this->admin)
         ->get(route('admin.articles.create'))
         ->assertStatus(200)
         ->assertViewIs('admin.articles.create');
});

test('admin dapat membuat artikel baru', function () {
    $response = $this->actingAs($this->admin)
         ->post(route('admin.articles.store'), [
             'title'       => 'Artikel Test Green Bonds',
             'category_id' => $this->category->id,
             'content'     => '<p>Konten artikel test ini cukup panjang untuk validasi.</p>',
             'description' => 'Deskripsi singkat artikel.',
             'status'      => 'published',
         ]);

    $response->assertRedirect(route('admin.articles.index'));
    $this->assertDatabaseHas('articles', ['title' => 'Artikel Test Green Bonds']);
});

test('membuat artikel gagal jika judul kosong', function () {
    $this->actingAs($this->admin)
         ->post(route('admin.articles.store'), [
             'category_id' => $this->category->id,
             'content'     => '<p>Konten ada.</p>',
         ])
         ->assertSessionHasErrors('title');
});

test('membuat artikel gagal jika konten kosong', function () {
    $this->actingAs($this->admin)
         ->post(route('admin.articles.store'), [
             'title'       => 'Judul Ada',
             'category_id' => $this->category->id,
             'content'     => '',
         ])
         ->assertSessionHasErrors('content');
});

test('membuat artikel dengan cover image berhasil', function () {
    $file = UploadedFile::fake()->image('cover.jpg', 1200, 800);

    $response = $this->actingAs($this->admin)
         ->post(route('admin.articles.store'), [
             'title'       => 'Artikel Dengan Gambar',
             'category_id' => $this->category->id,
             'content'     => '<p>Konten.</p>',
             'cover_image' => $file,
             'status'      => 'published',
         ]);

    $response->assertRedirect(route('admin.articles.index'));
    $article = Article::where('title', 'Artikel Dengan Gambar')->first();
    expect($article)->not->toBeNull();
    Storage::disk('public')->assertExists('articles/' . basename($article->cover_image));
});

// ── Edit/Update ───────────────────────────────────────────────────
test('admin dapat melihat form sunting artikel', function () {
    $article = Article::factory()->create([
        'category_id' => $this->category->id,
        'author_id'   => $this->admin->id,
    ]);

    $this->actingAs($this->admin)
         ->get(route('admin.articles.edit', $article))
         ->assertStatus(200)
         ->assertViewIs('admin.articles.edit');
});

test('admin dapat mengupdate artikel', function () {
    $article = Article::factory()->create([
        'category_id' => $this->category->id,
        'author_id'   => $this->admin->id,
    ]);

    $this->actingAs($this->admin)
         ->put(route('admin.articles.update', $article), [
             'title'       => 'Judul Yang Diperbarui',
             'category_id' => $this->category->id,
             'content'     => '<p>Konten baru.</p>',
             'status'      => 'published',
         ])
         ->assertRedirect(route('admin.articles.index'));

    $this->assertDatabaseHas('articles', ['title' => 'Judul Yang Diperbarui']);
});

test('update artikel menambah revision count', function () {
    $article = Article::factory()->create([
        'category_id'   => $this->category->id,
        'author_id'     => $this->admin->id,
        'revision_count'=> 1,
    ]);

    $this->actingAs($this->admin)
         ->put(route('admin.articles.update', $article), [
             'title'       => 'Update Judul',
             'category_id' => $this->category->id,
             'content'     => '<p>Konten.</p>',
         ]);

    $this->assertDatabaseHas('articles', [
        'id'             => $article->id,
        'revision_count' => 2,
    ]);
});

// ── Delete ────────────────────────────────────────────────────────
test('admin dapat menghapus artikel', function () {
    $article = Article::factory()->create([
        'category_id' => $this->category->id,
        'author_id'   => $this->admin->id,
    ]);

    $this->actingAs($this->admin)
         ->delete(route('admin.articles.destroy', $article))
         ->assertRedirect(route('admin.articles.index'));

    $this->assertSoftDeleted('articles', ['id' => $article->id]);
});

// ── Preview ───────────────────────────────────────────────────────
test('admin dapat melihat preview artikel', function () {
    $article = Article::factory()->create([
        'category_id' => $this->category->id,
        'author_id'   => $this->admin->id,
        'status'      => 'published',
    ]);

    $this->actingAs($this->admin)
         ->get(route('admin.articles.preview', $article->slug))
         ->assertStatus(200);
});

// ── JSON API ──────────────────────────────────────────────────────
test('daftar artikel dapat diambil sebagai JSON', function () {
    Article::factory(3)->create([
        'category_id' => $this->category->id,
        'author_id'   => $this->admin->id,
    ]);

    $this->actingAs($this->admin)
         ->getJson(route('admin.articles.index'))
         ->assertStatus(200)
         ->assertJsonStructure(['success', 'data']);
});

test('store artikel dapat melalui JSON request', function () {
    $this->actingAs($this->admin)
         ->postJson(route('admin.articles.store'), [
             'title'       => 'Artikel Via JSON',
             'category_id' => $this->category->id,
             'content'     => '<p>Isi konten.</p>',
             'status'      => 'published',
         ])
         ->assertStatus(200)
         ->assertJson(['success' => true]);
});
