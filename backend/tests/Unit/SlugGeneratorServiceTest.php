<?php

namespace Tests\Unit;

use App\Models\Link;
use App\Services\SlugGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlugGeneratorServiceTest extends TestCase
{
    use RefreshDatabase;

    private SlugGeneratorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SlugGeneratorService();
    }

    public function test_generates_slug_with_correct_length(): void
    {
        $slug = $this->service->generate();

        $this->assertGreaterThanOrEqual(6, strlen($slug));
        $this->assertLessThanOrEqual(8, strlen($slug));
    }

    public function test_generates_slug_with_specific_length(): void
    {
        $slug = $this->service->generate(7);

        $this->assertEquals(7, strlen($slug));
    }

    public function test_generates_alphanumeric_slug(): void
    {
        $slug = $this->service->generate();

        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]+$/', $slug);
    }

    public function test_generates_unique_slugs(): void
    {
        $slugs = [];
        for ($i = 0; $i < 100; $i++) {
            $slugs[] = $this->service->generate();
        }

        $this->assertCount(100, array_unique($slugs));
    }

    public function test_is_valid_returns_true_for_valid_slug(): void
    {
        $this->assertTrue($this->service->isValid('abc123'));
        $this->assertTrue($this->service->isValid('Test123'));
        $this->assertTrue($this->service->isValid('ABCDEFGH'));
    }

    public function test_is_valid_returns_false_for_invalid_slug(): void
    {
        // Too short
        $this->assertFalse($this->service->isValid('abc'));

        // Too long
        $this->assertFalse($this->service->isValid('abcdefghi'));

        // Contains special characters
        $this->assertFalse($this->service->isValid('abc-123'));
        $this->assertFalse($this->service->isValid('abc_123'));
    }

    public function test_is_valid_returns_false_for_existing_slug(): void
    {
        Link::factory()->create(['slug' => 'exists']);

        $this->assertFalse($this->service->isValid('exists'));
    }

    public function test_slug_exists_returns_true_for_existing_slug(): void
    {
        Link::factory()->create(['slug' => 'taken01']);

        $this->assertTrue($this->service->slugExists('taken01'));
    }

    public function test_slug_exists_returns_false_for_non_existing_slug(): void
    {
        $this->assertFalse($this->service->slugExists('notaken'));
    }

    public function test_slug_exists_excludes_id_when_provided(): void
    {
        $link = Link::factory()->create(['slug' => 'myslug1']);

        $this->assertFalse($this->service->slugExists('myslug1', $link->id));
    }
}
