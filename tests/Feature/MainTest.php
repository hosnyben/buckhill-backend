<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MainTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_listing(): void
    {
        $this->seed();
        $response = $this->getJson(route('blog.index'));

        // check if the response is successful and the response has a message
        $response->assertStatus(200)->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'uuid',
                    'title',
                    'slug',
                    'content',
                    'author',
                    'cover',
                ]
            ],
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'next_page_url',
        ]);

        $response->assertStatus(200);
    }

    public function test_post_show(): void
    {
        $this->seed();
        // get a random post from the database
        $post = \App\Models\Post::inRandomOrder()->first();

        $response = $this->getJson(route('blog.show', ['post' => $post->slug]));

        // check if the response is successful and the response has a message
        $response->assertStatus(200)->assertJsonStructure([
            'success',
            'data' => [
                'uuid',
                'title',
                'slug',
                'content',
                'author',
                'cover',
            ],
            'error',
            'errors',
            'extra'
        ])->assertJson([
            'success' => 1,
            'data' => [
                'uuid' => $post->uuid,
                'title' => $post->title,
                'slug' => $post->slug,
                'content' => $post->content,
                'author' => $post->author,
                'cover' => $post->cover,
            ],
            'error' => null,
            'errors' => [],
            'extra' => []
        ]);
    }

    public function test_promotions_listing(): void
    {
        $this->seed();
        $response = $this->getJson(route('promotions.index'));

        // check if the response is successful and the response has a message
        $response->assertStatus(200)->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'uuid',
                    'title',
                    'content',
                    'cover',
                    'metadata' => [
                        'image',
                        'valid_from',
                        'valid_to',
                    ]
                ]
            ],
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'next_page_url',
        ]);
    }
}
