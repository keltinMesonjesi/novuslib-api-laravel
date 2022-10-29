<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response as ResponseStatusCode;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function generateUserData(): array
    {
        $password = $this->faker->password;
        return [
            'username' => $this->faker->userName,
            'email' => $this->faker->email,
            'password' => $password,
            'password_confirmation' => $password,
            'firstname' => $this->faker->firstName,
            'lastname' => $this->faker->lastName,
            'phone_number' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
        ];
    }

    /**
     * @test
     * Testing successful user registration.
     * @return void
     */
    public function user_can_register()
    {
        $data = $this->generateUserData();

        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(ResponseStatusCode::HTTP_CREATED);
        $response->assertSee('token');
        $response->assertSee('id');
        $response->assertSee('uid');
        $response->assertJson([
            'status' => 'success',
            'data' => [
                'resource' => [
                    'type' => 'user',
                    'attributes' => [
                        'username' => $data['username'],
                        'email' => $data['email'],
                        'detail' => [
                            'firstname' => $data['firstname'],
                            'lastname' => $data['lastname'],
                            'phone_number' => $data['phone_number'],
                            'address' => $data['address'],
                        ]
                    ]
                ],
            ],
        ]);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'resource' => [
                    'type',
                    'id',
                    'uid',
                    'attributes' => [
                        'username',
                        'email',
                        'detail' => [
                            'firstname',
                            'lastname',
                            'phone_number',
                            'address',
                        ]
                    ]
                ],
                'options' => [
                    'token'
                ]
            ]
        ]);
        $this->assertNotEmpty($response['data']['options']['token'], 'Token is empty');
    }
}
