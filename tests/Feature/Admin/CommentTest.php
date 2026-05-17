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
    $this->article  = Article::factory()->create([
        'category_id' => $this->category->id,
        'author_id'   => $this->admin->id,
        'status'      => 'published',
    ]);
    $this->comment = Comment::factory()->create([
        'article_id' => $this->article->id,
        'status'     => 'pending',
    ]);
});

test('admin dapat melihat halaman komentar', function () {
    $this->actingAs($this->admin)
         ->get(route('admin.comments.index'))
         ->assertStatus(200)
         ->assertViewIs('admin.comments.index');
});

test('admin dapat menyetujui komentar', function () {
    $this->actingAs($this->admin)
         ->postJson(route('admin.comments.approve', $this->comment->id))
         ->assertJson(['success' => true]);

    $this->assertDatabaseHas('comments', ['id' => $this->comment->id, 'status' => 'approved']);
});

test('admin dapat menolak komentar', function () {
    $this->actingAs($this->admin)
         ->postJson(route('admin.comments.reject', $this->comment->id))
         ->assertJson(['success' => true]);

    $this->assertDatabaseHas('comments', ['id' => $this->comment->id, 'status' => 'rejected']);
});

test('admin dapat melakukan shadowban komentar', function () {
    $this->actingAs($this->admin)
         ->postJson(route('admin.comments.shadowban', $this->comment->id))
         ->assertJson(['success' => true]);

    $this->assertDatabaseHas('comments', ['id' => $this->comment->id, 'status' => 'shadowbanned']);
});

test('admin dapat memblokir IP dari komentar', function () {
    $this->comment->update(['ip_address' => '192.168.1.100', 'device_fingerprint' => hash('sha256', '192.168.1.100|test-agent')]);

    $this->actingAs($this->admin)
         ->postJson(route('admin.comments.block-ip', $this->comment->id))
         ->assertJson(['success' => true]);

    $this->assertDatabaseHas('device_bans', ['ip_address' => '192.168.1.100']);
});

test('admin dapat membalas komentar', function () {
    $this->actingAs($this->admin)
         ->postJson(route('admin.comments.reply', $this->comment->id), [
             'content' => 'Terima kasih atas komentar Anda!',
         ])
         ->assertJson(['success' => true]);

    $this->assertDatabaseHas('comments', [
        'id'          => $this->comment->id,
        'admin_reply' => 'Terima kasih atas komentar Anda!',
    ]);
});

test('balasan admin gagal jika konten kosong', function () {
    $this->actingAs($this->admin)
         ->postJson(route('admin.comments.reply', $this->comment->id), [
             'content' => '',
         ])
         ->assertStatus(422);
});

test('admin dapat export log komentar sebagai CSV', function () {
    $this->actingAs($this->admin)
         ->get(route('admin.comments.export'))
         ->assertStatus(200)
         ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
});

test('filter komentar berdasarkan status pending', function () {
    Comment::factory()->create(['article_id' => $this->article->id, 'status' => 'approved']);

    $this->actingAs($this->admin)
         ->get(route('admin.comments.index', ['status' => 'pending']))
         ->assertStatus(200)
         ->assertViewHas('comments');
});

test('tamu tidak bisa akses moderasi komentar', function () {
    $this->postJson(route('admin.comments.approve', $this->comment->id))
         ->assertStatus(401);
});
