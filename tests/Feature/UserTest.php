<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\User;
use App\Models\Order;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

use App\Notifications\PasswordResetCreated;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected $credentials = ['guest@buckhill.co.uk','password'];

    protected function createUser($credentials = null): User
    {
        return User::factory()->create([
            'email' => $credentials ? $credentials[0] : $this->credentials[0],
            'password' => Hash::make($credentials ? $credentials[0] : $this->credentials[1])
        ]);
    }

    public function test_login(): void
    {
        $user = $this->createUser(); // Admin or not admin won't be an issue here

        $response = $this->postJson(route('user.login'), [
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

        $response = $this->postJson(route('user.login'), [
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
        $response = $this->withToken($token)->getJson(route('user.logout'));

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

    public function test_create_user(): void
    {
        $response = $this->postJson(route('user.create'), [
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

        $this->assertDatabaseHas('users', ['email' => 'john.doe@example.com', 'is_admin' => 0]);
    }

    public function test_user_edit(): void
    {
        $token = $this->createUser()->createToken('registerToken');

        // get in route name admin.userEdit bearer token auth
        $response = $this->withToken($token)->putJson(route('user.update'), [
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

        $this->assertDatabaseHas('users', ['email' => 'john.doe.new@example.com']);
        $this->assertDatabaseMissing('users', ['email' => $this->credentials[0]]);
    }

    public function test_user_edit_to_an_existing_email(): void
    {
        $user = $this->createUser();
        $token = $user->createToken('registerToken');

        $userToEditFrom = $this->createUser(['john.doe@example.com','password']);

        // get in route name admin.userEdit bearer token auth
        $response = $this->withToken($token)->putJson(route('user.update'), [
            'email' => 'john.doe@example.com',
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

    public function test_user_edit_with_wrong_token(): void
    {
        $user = $this->createUser();
        $token = $user->createToken('registerToken');

        // get in route name admin.userEdit bearer token auth
        $response = $this->withToken('wrongtoken')->putJson(route('user.update'), [
            'email' => 'jane.doe@example.com',
            'phone_number' => '666-1234'
        ]);

        // check if the response is successful and the response has a message
        $response->assertStatus(401)->assertJsonStructure([
            'message'
        ]);

        $this->assertDatabaseHas('users', ['email' => $user->email]);
    }

    public function test_user_show(): void
    {
        $user = $this->createUser();
        $token = $user->createToken('registerToken');

        // get in route name admin.userEdit bearer token auth
        $response = $this->withToken($token)->getJson(route('user.show'));

        // check if the response is successful and the response has a message
        $response->assertStatus(200)->assertJsonStructure([
            'success',
            'data' => [
                'uuid',
                'first_name',
                'last_name',
                'email',
                'address',
                'phone_number',
            ],
            'error',
            'errors',
            'extra',
        ])
        ->assertJson([
            'data' => [
                'email' => $this->credentials[0],
            ],
        ]);
    }

    public function test_list_user_orders(): void
    {
        $this->seed();
        $user = Order::first()->user;

        $token = $user->createToken('registerToken');

        // list orders
        $response = $this->withToken($token)->getJson(route('user.orders'));

        // check if the response is successful and the response has a message
        $response->assertStatus(200)->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'uuid',
                    'user' => [
                        'uuid',
                        'first_name',
                        'last_name',
                        'email',
                        'address',
                        'phone_number',
                        'is_marketing',
                        'avatar'
                    ],
                    'order_status' => [
                        'uuid',
                        'title',
                    ],
                    'payment' => [
                        'uuid',
                        'type',
                        'details'
                    ],
                    'products' => [
                        '*' => [
                            'uuid',
                            'quantity',
                            'price',
                        ]
                    ],
                    'address' => [
                        'billing',
                        'shipping',
                    ],
                    'delivery_fee',
                    'amount',
                    'shipping_at',
                ]
            ],
            'error',
            'errors',
            'extra'
        ]);
    }

    public function test_forgot_password(): void
    {
        Notification::fake();

        $user = $this->createUser();
        $token = $user->createToken('registerToken');

        // get in route name admin.userEdit bearer token auth
        $response = $this->postJson(route('user.forgotPassword'), [
            'email' => $this->credentials[0],
        ]);

        // Check if password reset token is created
        $this->assertDatabaseHas('password_resets', ['email' => $this->credentials[0]]);

        // Check if notification job dispatched
        Notification::assertSentTo([$user], PasswordResetCreated::class);

        // check if the response is successful and the response has a message
        $response->assertStatus(200)->assertJsonStructure([
            'success',
            'data',
            'error',
            'errors',
            'extra',
        ]);
    }

    public function test_reset_password(): void
    {
        Notification::fake();

        $user = $this->createUser();
        $token = $user->createToken('registerToken');

        $response = $this->postJson(route('user.forgotPassword'), [
            'email' => $this->credentials[0],
        ]);

        // Get password reset token from the database
        $passwordReset = \DB::table('password_resets')->where('email', $this->credentials[0])->first();

        // Reset password with the token
        $response = $this->postJson(route('user.resetPassword', ['token' => $passwordReset->token]), [
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        // check if the response is successful and the response has a message
        $response->assertStatus(200)->assertJsonStructure([
            'success',
            'data',
            'error',
            'errors',
            'extra',
        ]);
    }
}
