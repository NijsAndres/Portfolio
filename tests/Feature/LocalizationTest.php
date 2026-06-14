<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Covers the NL/EN multi-language frontend: locale routing (/ = en, /nl = nl,
 * /en → /), that static chrome and DB content render in the right language, and
 * that an untranslated field falls back to English on the Dutch page.
 */
class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_root_renders_in_english(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('<html lang="en"', false);
        $response->assertSee('Get To Know Me.');        // static chrome (lang file)
        $response->assertSee('Quick Links');
        $response->assertSee('A Web Developer');         // DB content (hero subheadline)
        $response->assertDontSee('Snelkoppelingen');     // no Dutch chrome
    }

    public function test_nl_prefix_renders_in_dutch(): void
    {
        $response = $this->get('/nl');

        $response->assertOk();
        $response->assertSee('<html lang="nl"', false);
        $response->assertSee('Leer mij kennen.');        // static chrome (lang file)
        $response->assertSee('Snelkoppelingen');
        $response->assertSee('Een webontwikkelaar');     // DB content (hero subheadline)
        $response->assertDontSee('Quick Links');         // no English chrome
    }

    public function test_en_prefix_redirects_to_root(): void
    {
        $this->get('/en')->assertRedirect('/');
    }

    public function test_untranslated_field_falls_back_to_english_on_dutch_page(): void
    {
        // The seeded bio_text has no Dutch translation, so /nl should show the
        // English bio rather than an empty block.
        $this->get('/nl')->assertSee('I am a web developer with a strong focus');
    }
}
