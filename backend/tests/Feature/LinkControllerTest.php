<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LinkControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_list_links(): void
    {
        Link::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/links');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_user_can_only_see_own_links(): void
    {
        Link::factory()->count(2)->create(['user_id' => $this->user->id]);
        Link::factory()->count(3)->create(); // Other user's links

        $response = $this->actingAs($this->user)
            ->getJson('/api/links');

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_user_can_create_link_with_auto_slug(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/links', [
                'original_url' => 'https://example.com/test',
                'title' => 'Test Link',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'original_url',
                'slug',
                'short_url',
                'title',
                'access_count',
            ]);

        $this->assertDatabaseHas('links', [
            'original_url' => 'https://example.com/test',
            'title' => 'Test Link',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_user_can_create_link_with_custom_slug(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/links', [
                'original_url' => 'https://example.com/test',
                'slug' => 'myslug1',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('slug', 'myslug1');
    }

    public function test_user_cannot_create_link_with_duplicate_slug(): void
    {
        Link::factory()->create(['slug' => 'taken01']);

        $response = $this->actingAs($this->user)
            ->postJson('/api/links', [
                'original_url' => 'https://example.com/test',
                'slug' => 'taken01',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['slug']);
    }

    public function test_user_can_update_link(): void
    {
        $link = Link::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/links/{$link->id}", [
                'title' => 'Updated Title',
            ]);

        $response->assertOk()
            ->assertJsonPath('title', 'Updated Title');
    }

    public function test_user_can_soft_delete_link(): void
    {
        $link = Link::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/links/{$link->id}");

        $response->assertOk()
            ->assertJson(['message' => 'Link moved to trash']);

        $this->assertSoftDeleted('links', ['id' => $link->id]);
    }

    public function test_user_can_list_trashed_links(): void
    {
        Link::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'deleted_at' => now(),
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/links/trash');

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_user_can_restore_link(): void
    {
        $link = Link::factory()->create([
            'user_id' => $this->user->id,
            'deleted_at' => now(),
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/links/{$link->id}/restore");

        $response->assertOk();

        $this->assertDatabaseHas('links', [
            'id' => $link->id,
            'deleted_at' => null,
        ]);
    }

    public function test_user_can_force_delete_link(): void
    {
        $link = Link::factory()->create([
            'user_id' => $this->user->id,
            'deleted_at' => now(),
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/links/{$link->id}/force");

        $response->assertOk()
            ->assertJson(['message' => 'Link permanently deleted']);

        $this->assertDatabaseMissing('links', ['id' => $link->id]);
    }

    public function test_user_can_search_links(): void
    {
        Link::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Google Link',
        ]);
        Link::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Facebook Link',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/links?search=Google');

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_user_can_sort_links(): void
    {
        Link::factory()->create([
            'user_id' => $this->user->id,
            'access_count' => 10,
        ]);
        Link::factory()->create([
            'user_id' => $this->user->id,
            'access_count' => 50,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/links?sort_by=access_count&sort_order=desc');

        $response->assertOk();
        $data = $response->json('data');
        $this->assertEquals(50, $data[0]['access_count']);
    }
}
