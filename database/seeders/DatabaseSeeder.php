<?php

namespace Database\Seeders;

use App\Models\Filter;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the portfolio CMS with the current live content from index.html.
     */
    public function run(): void
    {
        $now = now();

        // Translatable columns store JSON keyed by locale. This seeder writes
        // rows with the query builder (not Eloquent), so wrap values by hand:
        // English is always set; Dutch is optional and falls back to English.
        $tr = fn (string $en, ?string $nl = null) => json_encode(
            $nl === null ? ['en' => $en] : ['en' => $en, 'nl' => $nl],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
        );

        // Clear existing rows so the seeder is safe to re-run.
        // Pivot before its parents to respect foreign keys.
        DB::table('filter_project')->delete();
        DB::table('filters')->delete();
        DB::table('hero_content')->delete();
        DB::table('about_content')->delete();
        DB::table('projects')->delete();
        DB::table('education')->delete();
        DB::table('experience')->delete();
        DB::table('contact_info')->delete();

        // --- Hero --------------------------------------------------------
        DB::table('hero_content')->insert([
            'headline' => $tr('Hi, I’m Andres.', 'Hallo, ik ben Andres.'),
            'subheadline' => $tr('A Web Developer', 'Een webontwikkelaar'),
            'tagline' => $tr('Based in belgium', 'Gevestigd in België'),
            'skills' => json_encode(['HTML', 'SCSS', 'JavaScript', 'Laravel', 'PHP']),
            'disciplines' => json_encode(['Design', 'Development', 'UX']),
            'image_path' => 'Portfolio-image.webp',
            'updated_at' => $now,
        ]);

        // --- About -------------------------------------------------------
        $bio = <<<'TEXT'
            I am a web developer with a strong focus on creating websites that are both functional and visually engaging. My core skills include HTML, CSS, JavaScript, and PHP, with experience in building reliable web applications using Laravel. I also place great importance on design, using Figma to plan wireframes and prototypes that ensure every project has a solid structure and smooth user experience.

            To support my development work, I create high-quality visuals and assets with Adobe Photoshop and Illustrator. These tools allow me to design custom graphics and refine details that give each project a professional and unique look.

            My goal is to deliver websites that combine clean design with strong performance, accessibility, and ease of use. I stay up to date with the latest web technologies and design practices to continually improve the quality of my work.
            TEXT;

        DB::table('about_content')->insert([
            'bio_text' => $tr($bio),
            'born_in' => 'Waregem',
            'languages' => 'NL/EN',
            'date_of_birth' => '21/04/2003',
            'updated_at' => $now,
        ]);

        // --- Projects ----------------------------------------------------
        $sinksenBody = <<<'HTML'
            <p class="c-modal__paragraph">Sinksen Kortrijk 2026 is a multi-page guide built around the Amazigh cultural theme of next year's edition. The site helps visitors plan their route across the weekend's activities.</p>

            <h3 class="c-modal__heading">The brief</h3>
            <p class="c-modal__paragraph">Created during my Atelier 2 module at Howest. The deliverable combined front-end work, a PHP-backed event list, and a coherent visual identity that nodded to the Amazigh theme without leaning on cliché.</p>

            <figure class="c-modal__media">
                <img src="assets/projects/sinksen-amazigh.webp" alt="Mockup of the Sinksen Kortrijk 2026 site" loading="lazy">
                <figcaption class="c-modal__caption">Hero of the site, set against the Amazigh visual identity.</figcaption>
            </figure>

            <h3 class="c-modal__heading">My role</h3>
            <p class="c-modal__paragraph">I owned the design system, built the front-end in HTML, SCSS and JavaScript, and wrote the PHP layer that reads events from a flat data file.</p>
            HTML;

        $banksyBody = <<<'HTML'
            <p class="c-modal__paragraph">A tribute site to Banksy that walks visitors through some of his most recognisable works and the ideas behind them.</p>

            <figure class="c-modal__media">
                <img src="assets/projects/banksy.webp" alt="Mockup of the Banksy tribute site" loading="lazy">
            </figure>

            <h3 class="c-modal__heading">The idea</h3>
            <p class="c-modal__paragraph">A concept project focused on bold typography and visual contrast, leaning into the stencil-style aesthetic of the source material.</p>
            HTML;

        $tuiBody = <<<'HTML'
            <p class="c-modal__paragraph">A redesign of the TUI Travel website, focused on a cleaner booking flow and a calmer visual hierarchy.</p>

            <figure class="c-modal__media">
                <img src="assets/projects/TUI-redesign.webp" alt="Mockup of the TUI Travel redesign" loading="lazy">
            </figure>

            <h3 class="c-modal__heading">The brief</h3>
            <p class="c-modal__paragraph">Atelier 1 at Howest. The aim was to rethink an existing travel site with modern design principles while keeping the brand recognisable.</p>
            HTML;

        $urbangearBody = <<<'HTML'
            <p class="c-modal__paragraph">A product page concept for UrbanGear, a fictional outdoor gear brand. The page was designed in Figma with a focus on bold imagery and a tactile feel.</p>

            <figure class="c-modal__media">
                <img src="assets/projects/urbangear-product.webp" alt="Mockup of the UrbanGear product page" loading="lazy">
            </figure>

            <h3 class="c-modal__heading">The brief</h3>
            <p class="c-modal__paragraph">Lab 08 at Howest. Build a complete product page in Figma that could plausibly ship as a real e-commerce template.</p>
            HTML;

        DB::table('projects')->insert([
            [
                'title' => $tr('Sinksen Kortrijk 2026'),
                'description' => $tr(
                    'A website that guides people through the Sinksen activities with an Amazigh theme.',
                    'Een website die mensen door de Sinksen-activiteiten loodst met een Amazigh-thema.',
                ),
                'tags' => json_encode(['HTML', 'JS', 'PHP']),
                'url' => 'https://www.andresnijs.be/atelier2/',
                'image_path' => 'projects/sinksen-amazigh.webp',
                'type' => 'school',
                'body' => $tr($sinksenBody),
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => $tr('Banksy Tribute', 'Banksy-eerbetoon'),
                'description' => $tr(
                    'A tribute site to Banksy that walks visitors through some of his most recognisable works and the ideas behind them.',
                    'Een eerbetoonsite voor Banksy die bezoekers meeneemt langs enkele van zijn bekendste werken en de ideeën erachter.',
                ),
                'tags' => json_encode(['HTML']),
                'url' => null,
                'image_path' => 'projects/banksy.webp',
                'type' => 'concept',
                'body' => $tr($banksyBody),
                'sort_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => $tr('TUI Travel Redesign', 'TUI Travel-herontwerp'),
                'description' => $tr(
                    'A redesign of the TUI Travel website, focused on a cleaner booking flow and a calmer visual hierarchy.',
                    'Een herontwerp van de TUI Travel-website, gericht op een vlottere boekingsflow en een rustigere visuele hiërarchie.',
                ),
                'tags' => json_encode(['Figma']),
                'url' => null,
                'image_path' => 'projects/TUI-redesign.webp',
                'type' => 'school',
                'body' => $tr($tuiBody),
                'sort_order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => $tr('UrbanGear Product Page', 'UrbanGear-productpagina'),
                'description' => $tr(
                    'A product page concept for UrbanGear, a fictional outdoor gear brand, designed in Figma with a focus on bold imagery and a tactile feel.',
                    'Een conceptproductpagina voor UrbanGear, een fictief outdoormerk, ontworpen in Figma met focus op krachtige beelden en een tactiel gevoel.',
                ),
                'tags' => json_encode(['Figma']),
                'url' => null,
                'image_path' => 'projects/urbangear-product.webp',
                'type' => 'school',
                'body' => $tr($urbangearBody),
                'sort_order' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // --- Filters -----------------------------------------------------
        // Dashboard-managed filters, linked to projects via the filter_project
        // pivot. Separate from the projects' free-text `tags` (the card badge).
        // Filter names are the same in both languages, so only English is set
        // (Dutch falls back to it). The slug stays a single, shared value.
        DB::table('filters')->insert([
            ['name' => $tr('HTML'),  'slug' => 'html',  'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => $tr('JS'),    'slug' => 'js',    'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['name' => $tr('PHP'),   'slug' => 'php',   'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['name' => $tr('Figma'), 'slug' => 'figma', 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Link projects to filters (mirrors the current tag-based grouping).
        $filterIds = Filter::pluck('id', 'slug');
        $projectFilters = [
            'Sinksen Kortrijk 2026' => ['html', 'js', 'php'],
            'Banksy Tribute' => ['html'],
            'TUI Travel Redesign' => ['figma'],
            'UrbanGear Product Page' => ['figma'],
        ];
        foreach ($projectFilters as $title => $slugs) {
            // title is now a translatable JSON column; match on the English value.
            $project = Project::where('title->en', $title)->first();
            $project?->filters()->sync($filterIds->only($slugs)->values()->all());
        }

        // --- Education ---------------------------------------------------
        DB::table('education')->insert([
            [
                'institution' => 'Howest Kortrijk',
                'degree' => $tr('Associate Degree in Web Development & Design + Front-End Web', 'Graduaat Webdevelopment & -design + Front-End Web'),
                'period' => $tr('2024 - present', '2024 - heden'),
                'sort_order' => 1,
            ],
            [
                'institution' => 'Vives Kortrijk - University of Applied Sciences',
                'degree' => $tr('Studied Bachelor of Applied Computer Science (not completed)', 'Studeerde Bachelor Toegepaste Informatica (niet afgerond)'),
                'period' => $tr('2022 - 2024'),
                'sort_order' => 2,
            ],
            [
                'institution' => 'Sint-Paulusschool Campus College - Waregem',
                'degree' => $tr('Diploma of Technical Secondary Education - IT Management', 'Diploma technisch secundair onderwijs - Informaticabeheer'),
                'period' => $tr('2015 - 2022'),
                'sort_order' => 3,
            ],
        ]);

        // --- Experience --------------------------------------------------
        DB::table('experience')->insert([
            'company' => 'Buro86 - Webdesign Agency',
            'role' => $tr('Internship', 'Stage'),
            'period' => $tr('March 2026 - June 2026', 'maart 2026 - juni 2026'),
            'sort_order' => 1,
        ]);

        // --- Contact -----------------------------------------------------
        $contactIntro = <<<'TEXT'
            I am always open to new professional opportunities and collaborations. If you are looking to hire a reliable web developer to bring your digital projects to life, enhance your existing platform, or support your team, feel free to get in touch.

            I welcome inquiries from clients, agencies, and recruiters alike, and will respond promptly to discuss how I can add value to your project or organization.
            TEXT;

        DB::table('contact_info')->insert([
            'email' => 'andres.nijs@icloud.com',
            'phone' => '+32 498 90 55 77',
            'linkedin_url' => 'https://www.linkedin.com/in/andres-nijs-378499252/',
            'github_url' => 'https://github.com/NijsAndres',
            'intro_text' => $tr($contactIntro),
            'updated_at' => $now,
        ]);

        // --- Site settings -----------------------------------------------
        // English CV ships seeded; the Dutch CV is left unset so it falls back
        // to English until one is uploaded (see SiteSetting::cvUrl).
        DB::table('site_settings')->updateOrInsert(
            ['key' => 'cv_path_en'],
            ['value' => 'AndresNijs-CV-2025.pdf'],
        );

        // --- Media library -----------------------------------------------
        // Register the seeded images (and any earlier uploads) into the media
        // library and link the projects/hero just inserted above.
        DB::table('media')->delete();
        Artisan::call('media:import');
    }
}
