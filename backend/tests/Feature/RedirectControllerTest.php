<?php

namespace Tests\Feature;

use App\Models\Link;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedirectControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_redirected(): void
    {
        $link = Link::factory()->create([
            'slug' => 'test123',
            'original_url' => 'https://example.com/target',
        ]);

        $response = $this->get('/test123');

        $response->assertRedirect('https://example.com/target');
    }

    public function test_access_count_is_incremented(): void
    {
        $link = Link::factory()->create([
            'slug' => 'count01',
            'access_count' => 0,
        ]);

        $this->get('/count01');

        $this->assertDatabaseHas('links', [
            'id' => $link->id,
            'access_count' => 1,
        ]);
    }

    public function test_access_log_is_created(): void
    {
        $link = Link::factory()->create(['slug' => 'logtest']);

        $this->get('/logtest');

        $this->assertDatabaseHas('access_logs', [
            'link_id' => $link->id,
        ]);
    }

    public function test_returns_404_for_invalid_slug(): void
    {
        $response = $this->get('/invalid1');

        $response->assertStatus(404);
    }

    public function test_deleted_link_is_not_accessible(): void
    {
        Link::factory()->create([
            'slug' => 'deleted',
            'deleted_at' => now(),
        ]);

        $response = $this->get('/deleted');

        $response->assertStatus(404);
    }
}
