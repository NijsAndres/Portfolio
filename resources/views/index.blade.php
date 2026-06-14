<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ __('site.meta.description') }}">
    <link rel="icon" href="{{ asset('assets/asterisk.svg') }}" type="image/x-icon">
    <title>{{ __('site.meta.title') }}</title>
    <link rel="alternate" hreflang="en" href="{{ route('home') }}">
    <link rel="alternate" hreflang="nl" href="{{ route('home.nl') }}">
    <link rel="alternate" hreflang="x-default" href="{{ route('home') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bai+Jamjuree:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;1,200;1,300;1,400;1,500;1,600;1,700&family=Bebas+Neue&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/backtotop.js') }}" defer></script>
    <script src="{{ asset('js/track.js') }}" defer></script>
</head>

<body>
    @php $localeHome = app()->getLocale() === 'nl' ? route('home.nl') : route('home'); @endphp
    <a href="#" class="c-backtotop" aria-label="{{ __('site.a11y.back_to_top') }}"></a>

    <nav class="c-nav js-nav">
        <div class="container o-container__flex">
            <a class="c-nav__logo" href="{{ $localeHome }}"><img class="c-nav__logoimg" src="{{ asset('assets/star.svg') }}" alt="Andres Nijs Logo" width="40" height="40">Andres Nijs</a>
            <ul class="c-nav__list">
                <li class="c-nav__item"><a class="c-nav__link" href="#about">{{ __('site.nav.about') }}</a></li>
                <li class="c-nav__item"><a class="c-nav__link" href="#projects">{{ __('site.nav.projects') }}</a></li>
                <li class="c-nav__item"><a class="c-nav__link" href="#education">{{ __('site.nav.education') }}</a></li>
                <li class="c-nav__item"><a class="c-nav__link" href="#experience">{{ __('site.nav.experience') }}</a></li>
                <li class="c-nav__item"><a class="c-nav__link" href="#contact">{{ __('site.nav.contact') }}</a></li>
                <li class="c-nav__item c-nav__langswitch">
                    <a class="c-nav__langlink @if (app()->getLocale() === 'en') is-active @endif"
                       href="{{ route('home') }}" hreflang="en"
                       @if (app()->getLocale() === 'en') aria-current="true" @endif>EN</a>
                    <span class="c-nav__langsep" aria-hidden="true">/</span>
                    <a class="c-nav__langlink @if (app()->getLocale() === 'nl') is-active @endif"
                       href="{{ route('home.nl') }}" hreflang="nl"
                       @if (app()->getLocale() === 'nl') aria-current="true" @endif>NL</a>
                </li>
            </ul>
            <button class="c-nav__toggle js-toggle">
                <svg class="c-nav__hamburger c-nav__hamburger--open" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/></svg>
                <svg class="c-nav__hamburger c-nav__hamburger--close" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/></svg>
            </button>
        </div>
    </nav>
    <header class="c-hero">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 u-fade-in-left">
                    <div class="c-hero__titlecontainer">
                        <h1>{{ $hero->headline }}</h1>
                        <div class="o-container__row">
                            <h1>{{ $hero->subheadline }}</h1>
                            <img class="c-hero__title-star" src="{{ asset('assets/star.svg') }}" alt="Star icon" width="40" height="40">
                        </div>
                        <h1>{{ $hero->tagline }}</h1>
                    </div>
                    <div class="c-hero__detailcontainer">
                        @foreach ($hero->disciplines as $index => $discipline)
                            @if ($index > 0)
                                <img src="{{ asset('assets/smallstar.svg') }}" alt="Small star icon" width="20" height="20">
                            @endif
                            <p class="c-hero__detailitem">{{ $discipline }}</p>
                        @endforeach
                    </div>
                    <div class="c-hero__buttoncontainer">
                        <a class="c-btn c-btn--primary" href="{{ $cvUrl }}" download data-track-event="cv_download">{{ __('site.hero.download_cv') }}<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/></svg></a>
                        <a class="c-btn c-btn--social" href="{{ $contact->linkedin_url }}" target="_blank" aria-label="{{ __('site.a11y.linkedin') }}" data-track-event="contact_linkedin"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854zm4.943 12.248V6.169H2.542v7.225zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248S2.4 3.226 2.4 3.934c0 .694.521 1.248 1.327 1.248zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016l.016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225z"/></svg></a>
                        <a class="c-btn c-btn--social" href="{{ $contact->github_url }}" target="_blank" aria-label="{{ __('site.a11y.github') }}" data-track-event="contact_github"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27s1.36.09 2 .27c1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.01 8.01 0 0 0 16 8c0-4.42-3.58-8-8-8"/></svg></a>
                    </div>
                </div>
                <div class="col-xl-4 offset-lg-1 col-lg-5 u-fade-in-right">
                    <div class="c-hero__imgcontainer">
                        <img class="c-hero__img" src="{{ $hero->image_url }}" alt="{{ $hero->image_alt }}" width="1920" height="1920" fetchpriority="high">
                    </div>
                </div>
            </div>
        </div>
    </header>
    <main>
        <section class="c-slider">
            @for ($i = 0; $i < 4; $i++)
            <ul class="c-slider__wrapper">
                @foreach ($hero->skills as $skill)
                <li class="c-slider__item">{{ $skill }}</li>
                <li class="c-slider__separator" aria-hidden="true"><img src="{{ asset('assets/smallstar.svg') }}" alt="" width="20" height="20"></li>
                @endforeach
            </ul>
            @endfor
        </section>
        <article class="c-about" id="about">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4">
                        <p class="u-uppercase u-text-with-deco mb-4">Andres <img src="{{ asset('assets/smallstar.svg') }}" alt="Small star icon" width="20" height="20"> Nijs</p>
                        <h2>{{ __('site.about.heading') }}</h2>
                    </div>
                    <div class="col-lg-8">
                        <x-rich-text :text="$about->bio_text" class="c-about__text" />

                        <div class="c-about__infocontainer">
                            <div class="c-about__infoitem">
                                <p class="c-about__infolabel">{{ __('site.about.born_in') }}</p>
                                <p class="c-about__infotext">{{ $about->born_in }}</p>
                            </div>
                            <div class="c-about__infoitem">
                                <p class="c-about__infolabel">{{ __('site.about.languages') }}</p>
                                <p class="c-about__infotext">{{ $about->languages }}</p>
                            </div>
                            <div class="c-about__infoitem">
                                <p class="c-about__infolabel">{{ __('site.about.date_of_birth') }}</p>
                                <p class="c-about__infotext">{{ $about->date_of_birth }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </article>
        <section class="c-projects" id="projects">
            <div class="container">
                <div class="row">
                    <div class="col-12 o-titlecontainer">
                        <img src="{{ asset('assets/star.svg') }}" alt="star" width="40" height="40">
                        <h2 class="c-projects__title">{{ __('site.projects.heading') }}</h2>
                        <p class="c-projects__subtitle u-uppercase">{{ __('site.projects.subtitle') }}</p>
                        <div class="c-projects__filtercontainer">
                            <button class="c-projects__filter c-projects__filter--active js-filter" data-filter="all">{{ __('site.projects.filter_all') }}</button>
                            @foreach ($filters as $filter)
                            <button class="c-projects__filter js-filter" data-filter="{{ $filter->slug }}">{{ strtoupper($filter->name) }}</button>
                            @endforeach
                        </div>
                    </div>
                    <div class="row js-projects">
                        @foreach ($projects as $project)
                        @php
                            $typeLabel = $project->type
                                ? (trans()->has('site.project_type.'.$project->type) ? __('site.project_type.'.$project->type) : $project->type)
                                : '';
                        @endphp
                        <div class="col-lg-6">
                            <button type="button" class="c-projects__card js-project-card"
                                data-track-event="project_click"
                                data-project-id="{{ $project->id }}"
                                data-filters="{{ $project->filters->pluck('slug')->implode(' ') }}"
                                data-type="{{ $project->type }}"
                                data-type-label="{{ $typeLabel }}"
                                data-title="{{ $project->title }}"
                                data-url="{{ $project->url }}"
                                data-tags="{{ implode(',', $project->tags ?? []) }}"
                                aria-haspopup="dialog" aria-controls="project-modal">
                                <div class="c-projects__cardimgcontainer">
                                    <img class="c-projects__cardimg" src="{{ $project->image_url }}" alt="{{ $project->image_alt }}" width="1920" height="1079" loading="lazy">
                                    @if (!empty($project->tags))
                                    <span class="c-projects__badge">{{ implode(' / ', $project->tags) }}</span>
                                    @endif
                                </div>
                                <div class="c-projects__cardcontent">
                                    <p class="c-projects__cardtitle">{{ $project->title }}</p>
                                    <p class="c-projects__cardtext">{{ $project->description }}</p>
                                </div>
                            </button>
                        </div>
                        @endforeach
                    </div>
                    <div class="col-12 c-projects__loadmorecontainer">
                        <button type="button" class="c-btn c-btn--primary js-load-more" hidden>{{ __('site.projects.load_more') }}</button>
                    </div>
                </div>
            </div>
        </section>
        <section class="c-edu" id="education">
            <div class="container">
                <div class="row">
                    <div class="col-12 o-titlecontainer">
                        <img src="{{ asset('assets/star.svg') }}" alt="star" width="40" height="40">
                        <h2 class="c-edu__title">{{ __('site.education.heading') }}</h2>
                        <p class="c-edu__subtitle u-uppercase">{{ __('site.education.subtitle') }}</p>
                    </div>
                    <div class="col-12">
                        @foreach ($education as $item)
                        <div class="c-edu__card">
                            <div class="c-edu__cardcontent">
                                <p class="c-edu__cardtitle">{{ $item->institution }}</p>
                                <p class="c-edu__cardtext">{{ $item->degree }} ({{ $item->period }})</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
        <section class="c-exp" id="experience">
            <div class="container">
                <div class="row">
                    <div class="col-12 o-titlecontainer">
                        <img src="{{ asset('assets/star.svg') }}" alt="star" width="40" height="40">
                        <h2 class="c-exp__title">{{ __('site.experience.heading') }}</h2>
                        <p class="c-exp__subtitle u-uppercase">{{ __('site.experience.subtitle') }}</p>
                    </div>
                    <div class="col-12">
                        @foreach ($experience as $item)
                        <div class="c-exp__card">
                            <div class="c-exp__cardcontent">
                                <p class="c-exp__cardtitle">{{ $item->company }}</p>
                                <p class="c-exp__cardtext">{{ $item->role }} ({{ $item->period }})</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
        <article class="c-contact" id="contact">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4">
                        <p class="u-uppercase u-text-with-deco mb-4">{{ __('site.contact.eyebrow_a') }} <img src="{{ asset('assets/smallstar.svg') }}" alt="Small star icon" width="20" height="20"> {{ __('site.contact.eyebrow_b') }}</p>
                        <h2>{{ __('site.contact.heading') }}</h2>
                    </div>
                    <div class="col-lg-8">
                        <x-rich-text :text="$contact->intro_text" class="c-contact__text" />
                        <div class="c-contact__infocontainer">
                            <div class="c-contact__infoitem">
                                <p class="c-contact__infolabel">{{ __('site.contact.mail_me') }}</p>
                                <a class="c-contact__link" href="mailto:{{ $contact->email }}" data-track-event="contact_email">
                                    <p class="c-contact__infotext">{{ $contact->email }}</p>
                                </a>
                            </div>
                            <div class="c-contact__infoitem">
                                <p class="c-contact__infolabel">{{ __('site.contact.phone_me') }}</p>
                                <a class="c-contact__link" href="tel:{{ preg_replace('/\s+/', '', $contact->phone) }}">
                                    <p class="c-contact__infotext">{{ $contact->phone }}</p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    </main>
    <footer class="c-footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-12">
                    <a class="c-footer__logo" href="{{ $localeHome }}"><img class="c-footer__logoimg" src="{{ asset('assets/star.svg') }}" alt="Andres Nijs Logo" width="40" height="40">Andres Nijs</a>
                    <ul class="c-footer__nav">
                        <li class="c-footer__navitem"><a class="c-footer__navlink" href="#about">{{ __('site.nav.about') }}</a></li>
                        <li class="c-footer__navitem"><a class="c-footer__navlink" href="#projects">{{ __('site.nav.projects') }}</a></li>
                        <li class="c-footer__navitem"><a class="c-footer__navlink" href="#education">{{ __('site.nav.education') }}</a></li>
                        <li class="c-footer__navitem"><a class="c-footer__navlink" href="#experience">{{ __('site.nav.experience') }}</a></li>
                        <li class="c-footer__navitem"><a class="c-footer__navlink" href="#contact">{{ __('site.nav.contact') }}</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4 offset-lg-4 offset-md-4 col-12">
                    <p class="c-footer__title">{{ __('site.footer.quick_links') }}</p>
                    <ul class="c-footer__nav">
                        <li class="c-footer__navitem"><a class="c-footer__navlink" href="{{ $cvUrl }}" download data-track-event="cv_download">{{ __('site.hero.download_cv') }}</a></li>
                        <li class="c-footer__navitem"><a class="c-footer__navlink" href="{{ $contact->linkedin_url }}" target="_blank" data-track-event="contact_linkedin">LinkedIn</a></li>
                        <li class="c-footer__navitem"><a class="c-footer__navlink" href="{{ $contact->github_url }}" target="_blank" data-track-event="contact_github">GitHub</a></li>
                    </ul>
                </div>
            </div>
            <div class="c-footer__bottom">
                <p class="c-footer__text">{{ __('site.footer.rights', ['year' => date('Y')]) }}</p>
            </div>
    </footer>

    @foreach ($projects as $project)
    <template id="project-{{ $project->id }}">{!! $project->body !!}</template>
    @endforeach

    <dialog id="project-modal" class="c-modal" aria-labelledby="project-modal-title">
        <button type="button" class="c-modal__close js-modal-close" aria-label="{{ __('site.a11y.close_modal') }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/></svg>
        </button>
        <header class="c-modal__header">
            <span class="c-modal__typebadge js-modal-type"></span>
            <h2 id="project-modal-title" class="c-modal__title js-modal-title"></h2>
        </header>
        <div class="c-modal__body js-modal-body"></div>
        <footer class="c-modal__footer">
            <div class="c-modal__tags js-modal-tags"></div>
            <a class="c-btn c-btn--primary js-modal-visit" href="#" target="_blank" rel="noopener">{{ __('site.projects.visit_site') }}<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5"/><path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0z"/></svg></a>
        </footer>
    </dialog>

</body>

</html>
