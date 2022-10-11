<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function generateUserData() : array {
        $password = $this->faker->password;
        return [
            'username'              => $this->faker->userName,
            'email'                 => $this->faker->email,
            'password'              => $password,
            'password_confirmation' => $password,
            'firstname'             => $this->faker->firstName,
            'lastname'              => $this->faker->lastName,
            'phone_number'          => $this->faker->phoneNumber,
            'address'               => $this->faker->address,
        ];
    }

    /**
     * @test
     * A basic feature test example.
     *
     * @return void
     */
    public function user_can_register()
    {
        $data = $this->generateUserData();

        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(201);
        $response->assertSee('token');
        $response->assertSee('id');
        $response->assertJson([
            'user' => [
                'username' => $data['username'],
                'email' => $data['email'],
            ],
        ]);
        $response->assertJsonStructure([
            'user' => [
                'username',
                'email',
                'updated_at',
                'created_at',
                'id'
            ],
            'token'
        ]);
        $this->assertNotEmpty($response['token'], 'Token is empty');
    }
}
