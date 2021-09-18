<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
// uses(RefreshDatabase::class);

test('example', function () {
    $this->get('/')->assertStatus(200);
});
