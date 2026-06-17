<?php

namespace Tests\Feature;

use App\Models\HeroContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Covers the token-protected CMS API's translation contract (Step 12 / MCP):
 * GET returns translatable fields as { en, nl } objects, PUT accepts that shape,
 * and an empty Dutch value is pruned so the site falls back to English.
 */
class ApiTranslationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        config(['services.cms.api_token' => 'test-token']);
    }

    public function test_get_hero_returns_both_languages(): void
    {
        $this->withToken('test-token')
            ->getJson('/api/cms/hero')
            ->assertOk()
            ->assertJsonPath('headline.nl', 'Hallo, ik ben Andres.')
            ->assertJsonPath('subheadline.en', 'A Web Developer');
    }

    public function test_update_hero_accepts_translation_object(): void
    {
        $this->withToken('test-token')
            ->putJson('/api/cms/hero', [
                'headline' => ['en' => 'New EN', 'nl' => 'Nieuw NL'],
            ])
            ->assertOk()
            ->assertJsonPath('headline.en', 'New EN')
            ->assertJsonPath('headline.nl', 'Nieuw NL');

        $this->assertSame('Nieuw NL', HeroContent::first()->getTranslation('headline', 'nl', false));
    }

    public function test_blank_dutch_value_is_pruned_so_it_falls_back(): void
    {
        $this->withToken('test-token')
            ->putJson('/api/cms/hero', [
                'headline' => ['en' => 'Only English', 'nl' => ''],
            ])
            ->assertOk()
            ->assertJsonPath('headline', ['en' => 'Only English']); // no nl key

        $this->assertSame([], HeroContent::first()->getTranslations('headline', ['nl']));
    }

    public function test_api_requires_a_valid_token(): void
    {
        $this->getJson('/api/cms/hero')->assertUnauthorized();
    }
}
