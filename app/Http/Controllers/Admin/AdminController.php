<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutContent;
use App\Models\ContactInfo;
use App\Models\Education;
use App\Models\Experience;
use App\Models\HeroContent;
use App\Models\Project;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

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

        return view('admin.hero', compact('hero'));
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
            'image_path' => ['nullable', 'string', 'max:255'],
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
     * Accept a new CV PDF.
     *
     * NOTE: Real storage (moving the file into storage/app/public/cv, deleting
     * the previous file, and storage:link) is intentionally deferred to Step 8.
     * For now we validate the upload and only record the filename in
     * site_settings under cv_path so the rest of the app can resolve it.
     */
    public function uploadCv(Request $request)
    {
        $request->validate([
            'cv' => ['required', 'file', 'mimes:pdf', 'max:10240'], // max 10 MB
        ]);

        $filename = $request->file('cv')->getClientOriginalName();
        SiteSetting::set('cv_path', $filename);

        return back()->with('success', 'CV reference updated. File storage is wired up in Step 8.');
    }
}
