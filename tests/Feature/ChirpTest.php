<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

describe('Chirp', function () {
    uses(RefreshDatabase::class);

    /*
    * INDEXING CHIRPS
    */
    it('should index chirps successfully', function () {
        $this->get('/')->assertStatus(200);
    });

    /*
 * EDITING CHIRPS
 */
    it('should not be able to go to edit page without logging in', function () {
        $user = User::factory()->create(['name' => 'joao silva', 'email' => 'joaos@exmaple.com', 'password' => 'password123']);

        $chirp = $user->chirps()->create(['message' => 'What is love?']);

        $this->get("/chirps/{$chirp->id}/edit")
            ->assertRedirect('/login');
        $this->assertGuest();
    });

    it('should go to the edit page successfully', function () {
        $user = User::factory()->create(['name' => 'joao silva', 'email' => 'joaos@exmaple.com', 'password' => 'password123']);

        $chirp = $user->chirps()->create(['message' => 'What is love?']);

        $this->actingAs($user)->get("/chirps/{$chirp->id}/edit")->assertStatus(200);
    });

    /*
    / CREATING CHIRPS
    */
    it('should fail to post a Chirp if not logged in', function () {
        $this->post('/chirps', ['message' => 'I want to be anonymous'])
            ->assertRedirect('/login');
        $this->assertGuest();
    });

    it('should fail to post if the message is empty', function () {
        $user = User::factory()->create(['name' => 'joao silva', 'email' => 'joaos@exmaple.com', 'password' => 'password123']);

        $this->actingAs($user)->post('/chirps', ['message' => ''])
            ->assertSessionHasErrors('message');
    });

    it('should fail if the message is not a string', function () {
        $user = User::factory()->create(['name' => 'joao silva', 'email' => 'joaos@exmaple.com', 'password' => 'password123']);

        $this->actingAs($user)->post('/chirps', ['message' => 1])
            ->assertSessionHasErrors('message');
    });

    it('should fail if the message is longer than 255 characters', function () {
        $user = User::factory()->create(['name' => 'joao silva', 'email' => 'joaos@exmaple.com', 'password' => 'password123']);

        $this->actingAs($user)->post('/chirps', ['message' => str_repeat('a', 256)])
            ->assertSessionHasErrors('message');
    });

    it('should post a Chirp successfully', function () {
        $user = User::factory()->create(['name' => 'joao silva', 'email' => 'joaos@exmaple.com', 'password' => 'password123']);

        $this->actingAs($user)->post('/chirps', ['message' => 'I want to be bake free'])
            ->assertRedirect('/')
            ->assertSessionHas('success', 'Your chirp has been posted!');
    });

    /*
    / UPDATING CHIRPS
    */
    it('should fail to update a Chirp if not logged in', function () {
        $user = User::factory()->create(['name' => 'joao silva', 'email' => 'joaos@exmaple.com', 'password' => 'password123']);

        $chirp = $user->chirps()->create(['message' => 'What is love?']);

        $this->put("/chirps/{$chirp->id}", ['message' => 'Baby dont hurt me'])
            ->assertRedirect('/login');
        $this->assertGuest();
    });

    it('should fail to update if the message is empty', function () {
        $user = User::factory()->create(['name' => 'joao silva', 'email' => 'joaos@exmaple.com', 'password' => 'password123']);

        $chirp = $user->chirps()->create(['message' => 'What is love?']);

        $this->actingAs($user)
            ->put("/chirps/{$chirp->id}", ['message' => ''])
            ->assertSessionHasErrors('message');

        // // Mostra o que está acontecendo de verdade
        // dump($response->status());
        // dump($response->headers->get('Location'));
        // dump(session()->all());
    });

    it('should fail to update if the message is not a string', function () {
        $user = User::factory()->create(['name' => 'joao silva', 'email' => 'joaos@exmaple.com', 'password' => 'password123']);

        $chirp = $user->chirps()->create(['message' => 'What is love?']);

        $this->actingAs($user)
            ->put("/chirps/{$chirp->id}", ['message' => 1])
            ->assertSessionHasErrors('message');
    });

    it('should fail to update if the message is longer than 255 characters', function () {
        $user = User::factory()->create(['name' => 'joao silva', 'email' => 'joaos@exmaple.com', 'password' => 'password123']);

        $chirp = $user->chirps()->create(['message' => 'What is love?']);

        $this->actingAs($user)
            ->put("/chirps/{$chirp->id}", ['message' => str_repeat('a', 256)])
            ->assertSessionHasErrors('message');
    });

    it('should fail to update a Chirp not owned', function () {
        $user = User::factory()->create(['name' => 'joao silva', 'email' => 'joaos@exmaple.com', 'password' => 'password123']);
        $chirp_owner = User::factory()->create();

        $chirp = $chirp_owner->chirps()->create(['message' => 'What is love?']);

        $this->actingAs($user)
            ->put("/chirps/{$chirp->id}", ['message' => 'Baby dont hurt me'])
            ->assertForbidden();
    });

    it('should update a Chirp successfully', function () {
        $user = User::factory()->create(['name' => 'joao silva', 'email' => 'joaos@exmaple.com', 'password' => 'password123']);

        $chirp = $user->chirps()->create(['message' => 'What is love?']);

        $this->actingAs($user)
            ->put("/chirps/{$chirp->id}", ['message' => 'I want to be bake free'])
            ->assertSessionHas('success', 'Chirp updated!');
    });

    /*
    / DELETING CHIRPS
    */
    it('should fail to delete a Chirp if not logged in', function () {
        $user = User::factory()->create(['name' => 'joao silva', 'email' => 'joaos@exmaple.com', 'password' => 'password123']);

        $chirp = $user->chirps()->create(['message' => 'What is love?']);

        $this->delete("/chirps/{$chirp->id}")
            ->assertRedirect('/login');
        $this->assertGuest();
    });


    it('should fail to delete a Chirp not owned', function () {
        $user = User::factory()->create(['name' => 'joao silva', 'email' => 'joaos@exmaple.com', 'password' => 'password123']);
        $chirp_owner = User::factory()->create();

        $chirp = $chirp_owner->chirps()->create(['message' => 'What is love?']);

        $this->actingAs($user)
            ->delete("/chirps/{$chirp->id}")
            ->assertForbidden();
    });

    it('should successfully delete a Chirp', function () {
        $user = User::factory()->create(['name' => 'joao silva', 'email' => 'joaos@exmaple.com', 'password' => 'password123']);

        $chirp = $user->chirps()->create(['message' => 'What is love?']);

        $this->actingAs($user)
            ->delete("/chirps/{$chirp->id}")
            ->assertSessionHas('success', 'Chirp deleted!');
    });
});
