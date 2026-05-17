<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin    = User::factory()->create();
    $this->category = Category::factory()->create(['name' => 'General', 'slug' => 'general']);
});

test('halaman artikel user dapat diakses publik', function () {
    $this->get(route('user.articles.index'))
         ->assertStatus(200)
         ->assertViewIs('user.articles.index');
});

test('halaman detail artikel published dapat diakses', function () {
    $article = Article::factory()->create([
        'category_id'  => $this->category->id,
        'author_id'    => $this->admin->id,
        'status'       => 'published',
        'published_at' => now(),
    ]);

    $this->get(route('user.articles.show', $article->slug))
         ->assertStatus(200)
         ->assertViewIs('user.articles.show')
         ->assertSee($article->title);
});

test('artikel draft tidak dapat diakses user', function () {
    $article = Article::factory()->create([
        'category_id' => $this->category->id,
        'author_id'   => $this->admin->id,
        'status'      => 'draft',
    ]);

    $this->get(route('user.articles.show', $article->slug))
         ->assertStatus(404);
});

test('artikel dengan slug tidak valid mengembalikan 404', function () {
    $this->get(route('user.articles.show', 'artikel-tidak-ada'))
         ->assertStatus(404);
});

test('filter artikel berdasarkan kategori', function () {
    $otherCat = Category::factory()->create(['name' => 'ESG', 'slug' => 'esg']);
    Article::factory(2)->create(['category_id' => $this->category->id, 'author_id' => $this->admin->id, 'status' => 'published', 'published_at' => now()]);
    Article::factory(1)->create(['category_id' => $otherCat->id, 'author_id' => $this->admin->id, 'status' => 'published', 'published_at' => now()]);

    $this->get(route('user.articles.index', ['category' => 'general']))
         ->assertStatus(200)
         ->assertViewHas('articles');
});

test('pencarian artikel berhasil', function () {
    Article::factory()->create([
        'category_id'  => $this->category->id,
        'author_id'    => $this->admin->id,
        'title'        => 'Artikel Tentang Panel Surya',
        'status'       => 'published',
        'published_at' => now(),
    ]);

    $this->get(route('user.articles.index', ['search' => 'Panel Surya']))
         ->assertStatus(200)
         ->assertSee('Panel Surya');
});

test('user dapat mengirim komentar ke artikel', function () {
    $article = Article::factory()->create([
        'category_id'  => $this->category->id,
        'author_id'    => $this->admin->id,
        'status'       => 'published',
        'published_at' => now(),
    ]);

    $this->post(route('user.comments.store', $article->slug), [
        'user_name' => 'Budi Hijau',
        'user_email'=> 'budi@test.com',
        'content'   => 'Artikel yang sangat informatif dan bermanfaat!',
    ])->assertRedirect();

    $this->assertDatabaseHas('comments', [
        'article_id' => $article->id,
        'user_name'  => 'Budi Hijau',
        'status'     => 'pending',
    ]);
});

test('komentar gagal jika nama kosong', function () {
    $article = Article::factory()->create([
        'category_id'  => $this->category->id,
        'author_id'    => $this->admin->id,
        'status'       => 'published',
        'published_at' => now(),
    ]);

    $this->postJson(route('user.comments.store', $article->slug), [
        'user_name' => '',
        'content'   => 'Konten ada.',
    ])->assertStatus(422)->assertJsonValidationErrors('user_name');
});

test('komentar gagal jika konten kurang dari 5 karakter', function () {
    $article = Article::factory()->create([
        'category_id'  => $this->category->id,
        'author_id'    => $this->admin->id,
        'status'       => 'published',
        'published_at' => now(),
    ]);

    $this->postJson(route('user.comments.store', $article->slug), [
        'user_name' => 'Test',
        'content'   => 'Hi',
    ])->assertStatus(422)->assertJsonValidationErrors('content');
});

test('hanya komentar approved yang tampil di halaman artikel', function () {
    $article = Article::factory()->create([
        'category_id'  => $this->category->id,
        'author_id'    => $this->admin->id,
        'status'       => 'published',
        'published_at' => now(),
    ]);

    Comment::factory()->create(['article_id' => $article->id, 'status' => 'approved',  'content' => 'Komentar disetujui']);
    Comment::factory()->create(['article_id' => $article->id, 'status' => 'pending',   'content' => 'Komentar pending']);
    Comment::factory()->create(['article_id' => $article->id, 'status' => 'rejected',  'content' => 'Komentar ditolak']);

    $this->get(route('user.articles.show', $article->slug))
         ->assertSee('Komentar disetujui')
         ->assertDontSee('Komentar pending')
         ->assertDontSee('Komentar ditolak');
});
