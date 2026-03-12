<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

describe('Logout', function () {
    uses(RefreshDatabase::class);
    it('should logout successfully', function () {
        User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com', 'password' => '123456789']);

        $this->post('/login', ['email' => 'john@example.com', 'password' => '123456789']);

        $this->post('/logout')->assertSessionHas('success', 'You have been logged out')
            ->assertRedirect('/');
    });
});
