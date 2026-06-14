<?php

/*
 * Nederlandse UI-teksten voor de publieke frontend. Houd de sleutels gelijk aan
 * lang/en/site.php; alleen de waarden zijn vertaald. Inhoud uit de database
 * (hero, about, projecten, …) wordt per record vertaald via
 * spatie/laravel-translatable, niet hier.
 */

return [
    'meta' => [
        'title' => 'Andres Nijs | Portfolio',
        'description' => 'Portfolio van Andres Nijs, een webontwikkelaar uit België gespecialiseerd in HTML, CSS, JavaScript, PHP en Laravel, met een focus op design, development en gebruikerservaring.',
    ],

    'nav' => [
        'about' => 'Over mij',
        'projects' => 'Projecten',
        'education' => 'Opleiding',
        'experience' => 'Ervaring',
        'contact' => 'Contact',
    ],

    'hero' => [
        'download_cv' => 'Download cv',
    ],

    'about' => [
        'heading' => 'Leer mij kennen.',
        'born_in' => 'Geboren in',
        'languages' => 'talen',
        'date_of_birth' => 'Geboortedatum',
    ],

    'projects' => [
        'heading' => 'Projecten',
        'subtitle' => 'Bekijk enkele van mijn eerdere werken!',
        'filter_all' => 'ALLE',
        'load_more' => 'Meer laden',
        'visit_site' => 'Bekijk site',
    ],

    'project_type' => [
        'school' => 'School',
        'concept' => 'Concept',
        'internship' => 'Stage',
    ],

    'education' => [
        'heading' => 'Opleiding',
        'subtitle' => 'Bekijk mijn opleidingsachtergrond!',
    ],

    'experience' => [
        'heading' => 'Ervaring',
        'subtitle' => 'Bekijk mijn professionele ervaring!',
    ],

    'contact' => [
        'eyebrow_a' => 'Contact',
        'eyebrow_b' => 'Mij',
        'heading' => 'Neem gerust contact op!',
        'mail_me' => 'Mail mij',
        'phone_me' => 'Bel mij',
    ],

    'footer' => [
        'quick_links' => 'Snelkoppelingen',
        'rights' => '© :year Andres Nijs. Alle rechten voorbehouden.',
    ],

    'a11y' => [
        'back_to_top' => 'Terug naar boven',
        'linkedin' => 'LinkedIn-profiel',
        'github' => 'GitHub-profiel',
        'close_modal' => 'Projectdetails sluiten',
    ],
];
