<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    protected $credentials = ['admin@buckhill.co.uk','admin'];

    protected function createUser($admin = false, $credentials = null): User
    {
        return User::factory()->create([
            'email' => $credentials ? $credentials[0] : $this->credentials[0],
            'password' => Hash::make($credentials ? $credentials[0] : $this->credentials[1]),
            'is_admin' => $admin
        ]);
    }

    public function test_login(): void
    {
        $user = $this->createUser(); // Admin or not admin won't be an issue here

        $response = $this->postJson(route('userAdmin.login'), [
            'email' => $this->credentials[0],
            'password' => $this->credentials[1]
        ]);

        // check if the response is successful and the response has a token
        $response->assertStatus(200)->assertJsonStructure([
            'success',
            'data' => [
                'token',
            ],
            'error',
            'errors',
            'extra',
        ]);

        $this->assertDatabaseHas('jwt_tokens', ['user_uuid' => $user->uuid]);
    }

    public function test_login_with_wrong_credentials(): void
    {
        $user = $this->createUser(); // Admin or not admin won't be an issue here

        $response = $this->postJson(route('userAdmin.login'), [
            'email' => $this->credentials[0],
            'password' => 'wrongpassword'
        ]);

        // check if the response is successful and the response has a token
        $response->assertStatus(401)->assertJsonStructure([
            'success',
            'data',
            'error',
            'errors',
            'trace',
        ]);

        $this->assertDatabaseMissing('jwt_tokens', ['user_uuid' => $user->uuid]);
    }

    public function test_logout(): void
    {
        $user = $this->createUser(); // Admin or not admin won't be an issue here
        $token = $user->createToken('registerToken');

        // get in route name admin.logout bearer token auth
        $response = $this->withToken($token)->getJson(route('userAdmin.logout'));

        // check if the response is successful and the response has a message
        $response->assertStatus(200)->assertJsonStructure([
            'success',
            'data',
            'error',
            'errors',
            'extra'
        ]);

        $this->assertDatabaseMissing('jwt_tokens', ['user_uuid' => $user->uuid]);
    }

    public function test_create_user_as_admin(): void
    {
        $user = $this->createUser(true);
        $token = $user->createToken('registerToken');

        // get in route name admin.create bearer token auth
        $response = $this->withToken($token)->postJson(route('userAdmin.create'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'address' => '123 Main St, Anytown, AN',
            'phone_number' => '555-1234'
        ]);

        // check if the response is successful and and the user is created
        $response->assertStatus(200)->assertJsonStructure([
            'success',
            'data',
            'error',
            'errors',
            'extra',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'john.doe@example.com', 'is_admin' => 1]);
    }

    public function test_create_user_as_regular_user(): void
    {
        $user = $this->createUser(false);
        $token = $user->createToken('registerToken');

        // get in route name admin.create bearer token auth
        $response = $this->withToken($token)->postJson(route('userAdmin.create'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'address' => '123 Main St, Anytown, AN',
            'phone_number' => '555-1234'
        ]);

        // check if the response is successful and and the user is created
        $response->assertStatus(403)->assertJsonStructure([
            'message'
        ]);

        $this->assertDatabaseMissing('users', ['email' => 'john.doe@example.com', 'is_admin' => 1]);
    }

    public function test_user_listing_as_admin(): void
    {
        $user = $this->createUser(true);
        $token = $user->createToken('registerToken');

        // get in route name admin.userListing bearer token auth
        $response = $this->withToken($token)->getJson(route('userAdmin.userListing'));

        // check if the response is successful and the response has a message
        $response->assertStatus(200)->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'uuid',
                    'first_name',
                    'last_name',
                    'email',
                    'address',
                    'phone_number',
                ]
            ],
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'next_page_url',
        ]);
    }

    public function test_user_listing_as_regular_user(): void
    {
        $user = $this->createUser();
        $token = $user->createToken('registerToken');

        // get in route name admin.userListing bearer token auth
        $response = $this->withToken($token)->getJson(route('userAdmin.userListing'));

        // check if the response is successful and the response has a message
        $response->assertStatus(403)->assertJsonStructure([
            'message',
        ]);
    }

    public function test_user_edit_as_admin(): void
    {
        $user = $this->createUser(true);
        $token = $user->createToken('registerToken');

        $userToEdit = $this->createUser(false, ['john.doe@example.com','password']);

        // get in route name admin.userEdit bearer token auth
        $response = $this->withToken($token)->putJson(route('userAdmin.userEdit', ['user' => $userToEdit->uuid]), [
            'email' => 'john.doe.new@example.com',
            'phone_number' => '666-1234'
        ]);

        // check if the response is successful and the response has a message
        $response->assertStatus(200)->assertJsonStructure([
            'success',
            'data',
            'error',
            'errors',
            'extra',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'john.doe.new@example.com', 'phone_number' => '666-1234']);
        $this->assertDatabaseMissing('users', ['email' => 'john.doe@example.com', 'phone_number' => '555-1234']);
    }

    public function test_user_edit_as_regular_user(): void
    {
        $user = $this->createUser();
        $token = $user->createToken('registerToken');

        $userToEdit = $this->createUser(false, ['john.doe@example.com','password']);

        // get in route name admin.userEdit bearer token auth
        $response = $this->withToken($token)->putJson(route('userAdmin.userEdit', ['user' => $userToEdit->uuid]), [
            'email' => 'john.doe.new@example.com',
            'phone_number' => '666-1234'
        ]);

        // check if the response is successful and the response has a message
        $response->assertStatus(403)->assertJsonStructure([
            'message',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'john.doe@example.com']);
        $this->assertDatabaseMissing('users', ['email' => 'john.doe.new@example.com', 'phone_number' => '666-1234']);
    }

    public function test_user_edit_to_an_existing_email(): void
    {
        $user = $this->createUser(true);
        $token = $user->createToken('registerToken');

        $userToEditFrom = $this->createUser(false, ['john.doe@example.com','password']);
        $userToEditTo = $this->createUser(false, ['jane.doe@example.com','password']);

        // get in route name admin.userEdit bearer token auth
        $response = $this->withToken($token)->putJson(route('userAdmin.userEdit', ['user' => $userToEditFrom->uuid]), [
            'email' => 'jane.doe@example.com',
            'phone_number' => '666-1234'
        ]);

        // check if the response is successful and the response has a message
        $response->assertStatus(422)->assertJsonStructure([
            'message',
            'errors' => [
                'email'
            ],
        ]);

        $this->assertDatabaseHas('users', ['email' => 'john.doe@example.com']);
    }

    public function test_user_edit_with_wrong_uuid_path(): void
    {
        $user = $this->createUser(true);
        $token = $user->createToken('registerToken');

        $userToEdit = $this->createUser(false, ['john.doe@example.com','password']);

        // get in route name admin.userEdit bearer token auth
        $response = $this->withToken($token)->putJson(route('userAdmin.userEdit', ['user' => $userToEdit->uuid.'1']), [
            'email' => 'john.doe.new@example.com',
            'phone_number' => '666-1234'
        ]);

        // check if the response is successful and the response has a message
        $response->assertStatus(404)->assertJsonStructure([
            'success',
            'data',
            'error',
            'errors',
            'trace',
        ]);
    }
}
