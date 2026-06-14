<?php

namespace Tests\Feature;

use App\Models\HeroContent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Covers the admin editing UI for translatable content: the shared form
 * component renders a per-locale (EN + NL) input pair, and saving persists both
 * languages onto the spatie/laravel-translatable model.
 */
class AdminTranslationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_hero_form_shows_both_language_inputs(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin/hero')
            ->assertOk()
            ->assertSee('name="headline[en]"', false)
            ->assertSee('name="headline[nl]"', false);
    }

    public function test_hero_update_saves_both_languages(): void
    {
        $this->actingAs(User::factory()->create())
            ->put('/admin/hero', [
                'headline' => ['en' => 'EN head', 'nl' => 'NL head'],
            ])
            ->assertRedirect();

        $hero = HeroContent::first();
        $this->assertSame('EN head', $hero->getTranslation('headline', 'en'));
        $this->assertSame('NL head', $hero->getTranslation('headline', 'nl'));
    }
}
