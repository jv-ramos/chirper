<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

describe('Register', function () {
    uses(RefreshDatabase::class);
    it('register a new user with valid data', function () {
        $response = $this->post('/register', [
            'name'                  => 'John Doe',
            'email'                 => 'jhon@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'Welcome to Chirper!');
    });

    it('creates an user in the database', function () {
        $this->post('/register', [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    });

    it('login automatically after registration', function () {
        $this->post('/register', [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
    });

    it("won't save password as plain text", function () {
        $this->post('/register', [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'john@example.com')->first();

        expect($user->password)->not->toBe('password123');
        expect(\Hash::check('password123', $user->password))->toBeTrue();
    });

    it('fails when name is not provided', function () {
        $response = $this->post('/register', [
            'email'                 => 'john@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertGuest();
    });

    it('fails when email is invalid', function () {
        $response = $this->post('/register', [
            'name'                  => 'John Doe',
            'email'                 => 'john-example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    });

    it('fails when email is already registered', function () {
        User::factory()->create(['email' => 'john@example.com']);

        $response = $this->post('/register', [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    });

    it('fails when password is shorter than 8 characters', function () {
        $response = $this->post('/register', [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'password'              => '123',
            'password_confirmation' => '123',
        ]);

        $response->assertSessionHasErrors('password');
    });

    it('fails when password confirmation does not match', function () {
        $response = $this->post('/register', [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password1234',
        ]);

        $response->assertSessionHasErrors('password');
    });

    it('fails when required field is missing', function (string $field) {
        $data = [
            'name'                  => 'João Silva',
            'email'                 => 'joao@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ];

        unset($data[$field]);

        $this->post('/register', $data)
            ->assertSessionHasErrors($field);
    })->with(['name', 'email', 'password']);
});
