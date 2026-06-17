<?php

/*
 * Static UI strings for the public frontend (resources/views/index.blade.php).
 * Database-driven content (hero, about, projects, …) is translated per record
 * via spatie/laravel-translatable, not here. Keep this file in sync with
 * lang/nl/site.php — same keys, translated values.
 */

return [
    'meta' => [
        'title' => 'Andres Nijs | Portfolio',
        'description' => 'Portfolio of Andres Nijs, a web developer based in Belgium specializing in HTML, CSS, JavaScript, PHP, and Laravel, with a focus on design, development, and user experience.',
    ],

    'nav' => [
        'about' => 'About',
        'projects' => 'Projects',
        'education' => 'Education',
        'experience' => 'Experience',
        'contact' => 'Contact',
    ],

    'hero' => [
        'download_cv' => 'Download CV',
    ],

    'about' => [
        'heading' => 'Get To Know Me.',
        'born_in' => 'Born In',
        'languages' => 'languages',
        'date_of_birth' => 'Date of birth',
    ],

    'projects' => [
        'heading' => 'Projects',
        'subtitle' => 'Check out some of my previous works!',
        'filter_all' => 'ALL',
        'load_more' => 'Load more',
        'visit_site' => 'Visit site',
    ],

    // Modal type badge — keyed by the project's stable `type` value.
    'project_type' => [
        'school' => 'School',
        'concept' => 'Concept',
        'internship' => 'Internship',
    ],

    'education' => [
        'heading' => 'Education',
        'subtitle' => 'Check out my educational background!',
    ],

    'experience' => [
        'heading' => 'Experience',
        'subtitle' => 'Check out my professional experience!',
    ],

    'contact' => [
        'eyebrow_a' => 'Contact',
        'eyebrow_b' => 'Me',
        'heading' => 'Feel Free to Reach Out!',
        'mail_me' => 'Mail Me',
        'phone_me' => 'Phone Me',
    ],

    'footer' => [
        'quick_links' => 'Quick Links',
        'rights' => '© :year Andres Nijs. All rights reserved.',
    ],

    'a11y' => [
        'back_to_top' => 'Back to top',
        'linkedin' => 'LinkedIn profile',
        'github' => 'GitHub profile',
        'close_modal' => 'Close project details',
    ],
];
