<?php

declare(strict_types=1);

use App\Enums\Version;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

it('returns a not found error if it tries to call an invalid version', function () {
    $endpoint = routeVersioned('users', [], 'v0.1');

    actingAs()
        ->postJson($endpoint)
        ->assertNotFound();
});
it('raises an error if name is not provided', function (): void {
    $endpoint = routeVersioned('users', [], Version::v1_0);
    actingAs()
        ->postJson($endpoint, [
            'email' => 'johnwick@mail.com',
            'password' => 'P@ssw0rd'
        ])
        ->assertJsonValidationErrorFor('name')
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});

it('raises an error if email is not provided', function (): void {
    $endpoint = routeVersioned('users', [], Version::v1_0);
    $faker = new \Faker\Generator();
    actingAs()
        ->postJson($endpoint, [
            'name' => 'John Wick',
            'password' => 'P@ssw0rd'
        ])
        ->assertJsonValidationErrorFor('email')
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});

it('raises an error if email is not valid', function (): void {
    $endpoint = routeVersioned('users', [], Version::v1_0);
    $faker = new \Faker\Generator();
    actingAs()
        ->postJson($endpoint, [
            'name' => 'John Wick',
            'email' => 'johnwick.com',
            'password' => 'P@ssw0rd'
        ])
        ->assertJsonValidationErrorFor('email')
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});

it('raises an error if password is too short', function (): void {
    $endpoint = routeVersioned('users', [], Version::v1_0);
    $faker = new \Faker\Generator();
    actingAs()
        ->postJson($endpoint, [
            'name' => 'John Wick',
            'email' => 'johnwick@mail.com',
            'password' => 'P@ssw0'
        ])
        ->assertJsonValidationErrorFor('password')
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});
it('raises an error if password is not valid', function (): void {
    $endpoint = routeVersioned('users', [], Version::v1_0);
    $faker = new \Faker\Generator();
    actingAs()
        ->postJson($endpoint, [
            'name' => 'John Wick',
            'email' => 'johnwick@mail.com',
            'password' => 'Password'
        ])
        ->assertJsonValidationErrorFor('password')
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});

it('creates a new user', function (): void {
    $endpoint = routeVersioned('users', [], Version::v1_0);
    $payload = [
        'name' => 'John Wick',
        'email' => 'johnwick@mail.com',
        'password' => 'P@ssw0rd'
    ];
    actingAs()
        ->postJson($endpoint, $payload)
        ->assertSuccessful()
        ->assertJsonStructure([
            'data'
        ]);
    unset($payload['password']);
    $this->assertDatabaseHas(User::class, $payload);
});

