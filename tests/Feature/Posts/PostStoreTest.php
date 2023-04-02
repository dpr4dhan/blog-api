<?php

declare(strict_types=1);

use App\Enums\Version;
use App\Models\Post;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

it('returns a not found error if it tries to call an invalid version', function () {
    $endpoint = routeVersioned('posts', [], 'v0.1');

    actingAs()
        ->postJson($endpoint)
        ->assertStatus(Response::HTTP_NOT_FOUND);
});

it('raises an error if title is not provided', function (): void {
    $endpoint = routeVersioned('posts', [], Version::v1_0);

    actingAs()
        ->postJson($endpoint, [
            'content' => 'the post content',
        ])
        ->assertJsonValidationErrorFor('title')
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});

it('raises an error if body is not provided', function (): void {
    $endpoint = routeVersioned('posts', [], Version::v1_0);

    actingAs()
        ->postJson($endpoint, [
            'title' => 'the post title',
        ])
        ->assertJsonValidationErrorFor('body')
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});

it('raises an error if status is not provided', function (): void {
    $endpoint = routeVersioned('posts', [], Version::v1_0);

    actingAs()
        ->postJson($endpoint, [
            'title' => 'the post title',
        ])
        ->assertJsonValidationErrorFor('status')
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});

it('raises an error if status is not valid', function (): void {
    $endpoint = routeVersioned('posts', [], Version::v1_0);

    actingAs()
        ->postJson($endpoint, [
            'title' => 'the post title',
            'status' => 'open'
        ])
        ->assertJsonValidationErrorFor('status')
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});

it('raises an error if publish date is not valid', function (): void {
    $endpoint = routeVersioned('posts', [], Version::v1_0);

    actingAs()
        ->postJson($endpoint, [
            'title' => 'the post title',
            'publish_date' => '2023-05-06'
        ])
        ->assertJsonValidationErrorFor('publish_date')
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});

it('creates a new post', function (): void {
    $endpoint = routeVersioned('posts', [], Version::v1_0);

    $payload = Post::factory()->raw();
    $payload['body'] = $payload['content'];
    unset($payload['content']);
    actingAs()
        ->postJson($endpoint, $payload)
        ->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                'uuid'      ,
                'title'     ,
                'body'     ,
                'status'    ,
                'publish_date' ,
                'is_featured',
                'created_at' ,
                'author'  => [] ,
                'total_likes' ,
                'comments' =>[]
            ],
        ]);

});
