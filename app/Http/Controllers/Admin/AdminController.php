<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutContent;
use App\Models\ContactInfo;
use App\Models\Education;
use App\Models\Experience;
use App\Models\HeroContent;
use App\Models\Media;
use App\Models\Project;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Handles the "singleton" admin sections (hero, about, contact), the dashboard
 * overview and the CV upload. The CRUD entities (projects, education,
 * experience) live in their own resource controllers in this namespace.
 *
 * Routes and the auth middleware are wired up in Step 5; every method here
 * assumes it is reached through the authenticated /admin route group.
 */
class AdminController extends Controller
{
    /**
     * Overview page with simple counts and the singletons' last-updated info.
     * (The analytics widget is added later in Step 11.)
     */
    public function dashboard()
    {
        $stats = [
            'projects' => Project::count(),
            'education' => Education::count(),
            'experience' => Experience::count(),
        ];

        $hero = HeroContent::first();
        $about = AboutContent::first();
        $contact = ContactInfo::first();

        return view('admin.dashboard', compact('stats', 'hero', 'about', 'contact'));
    }

    /* ---------------------------------------------------------------------
     | Hero (single row)
     * ------------------------------------------------------------------- */

    public function editHero()
    {
        $hero = HeroContent::first() ?? new HeroContent();
        $media = Media::latest()->get();

        return view('admin.hero', compact('hero', 'media'));
    }

    public function updateHero(Request $request)
    {
        $validated = $request->validate([
            'headline' => ['required', 'string', 'max:255'],
            'subheadline' => ['nullable', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['string', 'max:255'],
            'disciplines' => ['nullable', 'array'],
            'disciplines.*' => ['string', 'max:255'],
            'media_id' => ['nullable', 'integer', 'exists:media,id'],
        ]);

        // first() ?? new — the row is seeded, but this also handles a fresh DB.
        $hero = HeroContent::first() ?? new HeroContent();
        $hero->fill($validated)->save();

        return back()->with('success', 'Hero section updated.');
    }

    /* ---------------------------------------------------------------------
     | About (single row)
     * ------------------------------------------------------------------- */

    public function editAbout()
    {
        $about = AboutContent::first() ?? new AboutContent();

        return view('admin.about', compact('about'));
    }

    public function updateAbout(Request $request)
    {
        $validated = $request->validate([
            'bio_text' => ['nullable', 'string'],
            'born_in' => ['nullable', 'string', 'max:255'],
            'languages' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'string', 'max:255'],
        ]);

        $about = AboutContent::first() ?? new AboutContent();
        $about->fill($validated)->save();

        return back()->with('success', 'About section updated.');
    }

    /* ---------------------------------------------------------------------
     | Contact (single row)
     * ------------------------------------------------------------------- */

    public function editContact()
    {
        $contact = ContactInfo::first() ?? new ContactInfo();

        return view('admin.contact', compact('contact'));
    }

    public function updateContact(Request $request)
    {
        $validated = $request->validate([
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'intro_text' => ['nullable', 'string'],
        ]);

        $contact = ContactInfo::first() ?? new ContactInfo();
        $contact->fill($validated)->save();

        return back()->with('success', 'Contact details updated.');
    }

    /* ---------------------------------------------------------------------
     | CV upload
     * ------------------------------------------------------------------- */

    /**
     * Accept a new CV PDF, store it on the public disk and record its relative
     * path in site_settings under cv_path. The previous file is removed when it
     * lives in storage; legacy public/assets references are left untouched.
     */
    public function uploadCv(Request $request)
    {
        $request->validate([
            'cv' => ['required', 'file', 'mimes:pdf', 'max:10240'], // max 10 MB
        ]);

        // Delete the previous CV only when it was a stored upload.
        $previous = SiteSetting::get('cv_path');
        if ($previous && Storage::disk('public')->exists($previous)) {
            Storage::disk('public')->delete($previous);
        }

        $path = $request->file('cv')->store('cv', 'public');
        SiteSetting::set('cv_path', $path);

        return back()->with('success', 'CV updated.');
    }
}
