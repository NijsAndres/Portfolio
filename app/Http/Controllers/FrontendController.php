<?php

namespace App\Http\Controllers;

use App\Models\AboutContent;
use App\Models\ContactInfo;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Filter;
use App\Models\HeroContent;
use App\Models\Project;
use App\Models\SiteSetting;

/**
 * Renders the public-facing portfolio (the former static index.html) from the
 * CMS data layer. Read-only: every admin edit flows straight through to the
 * single view below.
 */
class FrontendController extends Controller
{
    public function index()
    {
        $hero       = HeroContent::first();
        $about      = AboutContent::first();
        $projects   = Project::with('filters')->orderBy('sort_order')->orderBy('id')->get();
        $filters    = Filter::orderBy('sort_order')->orderBy('name')->get();
        $education  = Education::orderBy('sort_order')->orderBy('id')->get();
        $experience = Experience::orderBy('sort_order')->orderBy('id')->get();
        $contact    = ContactInfo::first();
        $cvUrl      = SiteSetting::cvUrl();

        return view('index', compact(
            'hero',
            'about',
            'projects',
            'filters',
            'education',
            'experience',
            'contact',
            'cvUrl',
        ));
    }
}
