<?php

use App\Models\Category;
use App\Models\SimulationType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->category = Category::factory()->create(['name' => 'Green Bonds', 'slug' => 'green-bonds']);
    $this->simType  = SimulationType::factory()->create([
        'category_id' => $this->category->id,
        'name'        => 'Green Bonds (Obligasi Hijau)',
        'return_rate' => 0.0780,
    ]);
});

test('halaman simulasi dapat diakses publik', function () {
    $this->get(route('user.simulation'))
         ->assertStatus(200)
         ->assertViewIs('user.simulation');
});

test('simulasi menghitung hasil dengan benar', function () {
    $response = $this->postJson(route('user.simulation.calculate'), [
        'simulation_type_id' => $this->simType->id,
        'initial_amount'     => 5000000,
        'duration'           => 10,
    ]);

    $response->assertStatus(200)
             ->assertJson(['success' => true])
             ->assertJsonStructure(['data' => [
                 'invested', 'duration', 'return_rate', 'estimated_return', 'total', 'type_name',
             ]]);

    $data = $response->json('data');
    expect($data['invested'])->toBe(5000000);
    expect($data['duration'])->toBe(10);
    expect($data['return_rate'])->toBe(7.8);
    // Compound: 5000000 * (1.078)^10 ≈ 10770459
    expect($data['total'])->toBeGreaterThan(10000000);
});

test('simulasi menghitung bunga majemuk dengan benar', function () {
    $response = $this->postJson(route('user.simulation.calculate'), [
        'simulation_type_id' => $this->simType->id,
        'initial_amount'     => 1000000,
        'duration'           => 1,
    ]);

    $data = $response->json('data');
    // 1000000 * 1.078 = 1078000
    expect($data['total'])->toBe(1078000);
    expect($data['estimated_return'])->toBe(78000);
});

test('simulasi gagal jika modal di bawah minimum', function () {
    $this->postJson(route('user.simulation.calculate'), [
        'simulation_type_id' => $this->simType->id,
        'initial_amount'     => 50000, // kurang dari 100000
        'duration'           => 5,
    ])->assertStatus(422)->assertJsonValidationErrors('initial_amount');
});

test('simulasi gagal jika durasi melebihi 30 tahun', function () {
    $this->postJson(route('user.simulation.calculate'), [
        'simulation_type_id' => $this->simType->id,
        'initial_amount'     => 5000000,
        'duration'           => 31,
    ])->assertStatus(422)->assertJsonValidationErrors('duration');
});

test('simulasi gagal jika durasi kurang dari 1 tahun', function () {
    $this->postJson(route('user.simulation.calculate'), [
        'simulation_type_id' => $this->simType->id,
        'initial_amount'     => 5000000,
        'duration'           => 0,
    ])->assertStatus(422)->assertJsonValidationErrors('duration');
});

test('simulasi gagal jika jenis investasi tidak valid', function () {
    $this->postJson(route('user.simulation.calculate'), [
        'simulation_type_id' => 9999,
        'initial_amount'     => 5000000,
        'duration'           => 10,
    ])->assertStatus(422)->assertJsonValidationErrors('simulation_type_id');
});

test('simulasi gagal jika field wajib kosong', function () {
    $this->postJson(route('user.simulation.calculate'), [])
         ->assertStatus(422)
         ->assertJsonValidationErrors(['simulation_type_id', 'initial_amount', 'duration']);
});

test('simulasi dengan modal maksimum berhasil', function () {
    $this->postJson(route('user.simulation.calculate'), [
        'simulation_type_id' => $this->simType->id,
        'initial_amount'     => 100000000000,
        'duration'           => 30,
    ])->assertStatus(200)->assertJson(['success' => true]);
});
