<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response as ResponseStatusCode;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function generateUserData(): array
    {
        $password = Str::random(8);
        return [
            'username' => Str::random(6),
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

    /**
     * @test
     */
    public function username_is_required()
    {
        $data = array_merge($this->generateUserData(), ['username' => '']);

        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals('failed', $response['status']);
        $this->assertArrayHasKey('username', $response['message']);
    }

    /**
     * @test
     */
    public function username_is_string()
    {
        $data = array_merge($this->generateUserData(), ['username' => $this->faker->randomNumber(8)]);

        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals('failed', $response['status']);
        $this->assertArrayHasKey('username', $response['message']);
    }

    /**
     * @test
     */
    public function username_is_unique()
    {
        $firstUserData = $this->generateUserData();
        $this->post('/api/v1/auth/register', $firstUserData);

        $userWithSameUsername = array_merge($this->generateUserData(), ['username' => $firstUserData['username']]);
        $response = $this->post('/api/v1/auth/register', $userWithSameUsername);

        $response->assertStatus(ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals('failed', $response['status']);
        $this->assertArrayHasKey('username', $response['message']);
    }

    /**
     * @test
     */
    public function username_min_6()
    {
        $data = array_merge($this->generateUserData(), ['username' => Str::random(5)]);
        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals('failed', $response['status']);
        $this->assertArrayHasKey('username', $response['message']);
    }

    /**
     * @test
     */
    public function username_max_50()
    {
        $data = array_merge($this->generateUserData(), ['username' => Str::random(51)]);
        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals('failed', $response['status']);
        $this->assertArrayHasKey('username', $response['message']);
    }

    /**
     * @test
     */
    public function email_required()
    {
        $data = array_merge($this->generateUserData(), ['email' => '']);
        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals('failed', $response['status']);
        $this->assertArrayHasKey('email', $response['message']);
    }

    /**
     * @test
     */
    public function email_is_valid_email_format()
    {
        $data = array_merge($this->generateUserData(), ['email' => Str::random()]);
        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals('failed', $response['status']);
        $this->assertArrayHasKey('email', $response['message']);
    }

    /**
     * @test
     */
    public function email_is_unique()
    {
        $firstUserData = $this->generateUserData();
        $this->post('/api/v1/auth/register', $firstUserData);

        $data = array_merge($this->generateUserData(), ['email' => $firstUserData['email']]);
        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals('failed', $response['status']);
        $this->assertArrayHasKey('email', $response['message']);
    }

    /**
     * @test
     */
    public function email_max_100()
    {
        $firstUserData = $this->generateUserData();
        $this->post('/api/v1/auth/register', $firstUserData);

        $data = array_merge($this->generateUserData(), ['email' => Str::random(101).'@test.test']);
        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals('failed', $response['status']);
        $this->assertArrayHasKey('email', $response['message']);
    }

    /**
     * @test
     */
    public function password_is_required()
    {
        $data = array_merge($this->generateUserData(), ['password' => '', 'password_confirmation' => '']);
        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals('failed', $response['status']);
        $this->assertArrayHasKey('password', $response['message']);
    }

    /**
     * @test
     */
    public function password_is_string()
    {
        $password = $this->faker->randomNumber(8);
        $data = array_merge($this->generateUserData(), ['password' => $password, 'password_confirmation' => $password]);
        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals('failed', $response['status']);
        $this->assertArrayHasKey('password', $response['message']);
    }

    /**
     * @test
     */
    public function password_is_confirmed()
    {
        $data = array_merge($this->generateUserData(), ['password_confirmation' => '']);
        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals('failed', $response['status']);
        $this->assertArrayHasKey('password', $response['message']);
    }

    /**
     * @test
     */
    public function password_min_8()
    {
        $password = Str::random(7);
        $data = array_merge($this->generateUserData(), ['password' => $password, 'password_confirmation' => $password]);
        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals('failed', $response['status']);
        $this->assertArrayHasKey('password', $response['message']);
    }

    /**
     * @test
     */
    public function password_max_50()
    {
        $password = Str::random(51);
        $data = array_merge($this->generateUserData(), ['password' => $password, 'password_confirmation' => $password]);
        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals('failed', $response['status']);
        $this->assertArrayHasKey('password', $response['message']);
    }

    /**
     * @test
     */
    public function firstname_is_required()
    {
        $data = array_merge($this->generateUserData(), ['firstname' => '']);
        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals('failed', $response['status']);
        $this->assertArrayHasKey('firstname', $response['message']);
    }

    /**
     * @test
     */
    public function firstname_is_string()
    {
        $data = array_merge($this->generateUserData(), ['firstname' => $this->faker->randomNumber()]);
        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals('failed', $response['status']);
        $this->assertArrayHasKey('firstname', $response['message']);
    }

    /**
     * @test
     */
    public function firstname_max_50()
    {
        $data = array_merge($this->generateUserData(), ['firstname' => Str::random(51)]);
        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals('failed', $response['status']);
        $this->assertArrayHasKey('firstname', $response['message']);
    }

    /**
     * @test
     */
    public function lastname_is_required()
    {
        $data = array_merge($this->generateUserData(), ['lastname' => '']);
        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals('failed', $response['status']);
        $this->assertArrayHasKey('lastname', $response['message']);
    }

    /**
     * @test
     */
    public function lastname_is_string()
    {
        $data = array_merge($this->generateUserData(), ['lastname' => $this->faker->randomNumber()]);
        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals('failed', $response['status']);
        $this->assertArrayHasKey('lastname', $response['message']);
    }

    /**
     * @test
     */
    public function lastname_max_50()
    {
        $data = array_merge($this->generateUserData(), ['lastname' => Str::random(51)]);
        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals('failed', $response['status']);
        $this->assertArrayHasKey('lastname', $response['message']);
    }

    /**
     * @test
     */
    public function phone_number_is_required()
    {
        $data = array_merge($this->generateUserData(), ['phone_number' => '']);
        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals('failed', $response['status']);
        $this->assertArrayHasKey('phone_number', $response['message']);
    }

    /**
     * @test
     */
    public function phone_number_is_string()
    {
        $data = array_merge($this->generateUserData(), ['phone_number' => $this->faker->randomNumber(4)]);
        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals('failed', $response['status']);
        $this->assertArrayHasKey('phone_number', $response['message']);
    }

    /**
     * @test
     */
    public function phone_number_max_40()
    {
        $data = array_merge($this->generateUserData(), ['phone_number' => Str::random(41)]);
        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals('failed', $response['status']);
        $this->assertArrayHasKey('phone_number', $response['message']);
    }

    /**
     * @test
     */
    public function address_is_required()
    {
        $data = array_merge($this->generateUserData(), ['address' => '']);
        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals('failed', $response['status']);
        $this->assertArrayHasKey('address', $response['message']);
    }

    /**
     * @test
     */
    public function address_is_string()
    {
        $data = array_merge($this->generateUserData(), ['address' => $this->faker->randomNumber(4)]);
        $response = $this->post('/api/v1/auth/register', $data);

        $this->assertEquals('failed', $response['status']);
        $this->assertArrayHasKey('address', $response['message']);
    }

    /**
     * @test
     */
    public function address_max_255()
    {
        $data = array_merge($this->generateUserData(), ['address' => Str::random(256)]);
        $response = $this->post('/api/v1/auth/register', $data);

        $this->assertEquals('failed', $response['status']);
        $this->assertArrayHasKey('address', $response['message']);
    }
}
