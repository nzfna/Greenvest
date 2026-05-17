<?php

// This file is the Pest configuration.
// Tells Pest which test files to use and sets global behavior.

uses(
    Tests\TestCase::class,
    Illuminate\Foundation\Testing\RefreshDatabase::class,
)->in('Feature');
