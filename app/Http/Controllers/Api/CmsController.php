<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AboutContent;
use App\Models\Analytics;
use App\Models\ContactInfo;
use App\Models\HeroContent;
use App\Models\Media;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Internal JSON API for the CMS singletons (hero, about, contact), the CV
 * upload and the analytics summary (Step 12). Consumed by the MCP server.
 *
 * Guarded by the cms.token middleware (Bearer token) in routes/api.php. These
 * methods reuse the existing models and mirror the admin controllers' rules;
 * the only job here is to return/accept JSON.
 */
class CmsController extends Controller
{
    /* ---------------------------------------------------------------------
     | Hero
     * ------------------------------------------------------------------- */

    public function showHero(): JsonResponse
    {
        return response()->json(HeroContent::first() ?? new HeroContent());
    }

    public function updateHero(Request $request): JsonResponse
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

        $hero = HeroContent::first() ?? new HeroContent();
        $hero->fill($validated)->save();

        return response()->json($hero);
    }

    /* ---------------------------------------------------------------------
     | About
     * ------------------------------------------------------------------- */

    public function showAbout(): JsonResponse
    {
        return response()->json(AboutContent::first() ?? new AboutContent());
    }

    public function updateAbout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bio_text' => ['nullable', 'string'],
            'born_in' => ['nullable', 'string', 'max:255'],
            'languages' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'string', 'max:255'],
        ]);

        $about = AboutContent::first() ?? new AboutContent();
        $about->fill($validated)->save();

        return response()->json($about);
    }

    /* ---------------------------------------------------------------------
     | Contact
     * ------------------------------------------------------------------- */

    public function showContact(): JsonResponse
    {
        return response()->json(ContactInfo::first() ?? new ContactInfo());
    }

    public function updateContact(Request $request): JsonResponse
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

        return response()->json($contact);
    }

    /* ---------------------------------------------------------------------
     | Media library (read-only)
     * ------------------------------------------------------------------- */

    /**
     * List the media library so callers can discover the media_id to attach to
     * a hero or project image. Read-only — uploading new files isn't supported
     * over the API/MCP. Includes the resolved public url.
     */
    public function media(): JsonResponse
    {
        $media = Media::latest()->get()->map(fn (Media $m) => [
            'id' => $m->id,
            'url' => $m->url,
            'original_name' => $m->original_name,
            'alt' => $m->alt,
            'title' => $m->title,
            'width' => $m->width,
            'height' => $m->height,
        ]);

        return response()->json($media);
    }

    /* ---------------------------------------------------------------------
     | CV upload
     * ------------------------------------------------------------------- */

    /**
     * Accept a new CV PDF and store it on the public disk, mirroring
     * AdminController::uploadCv. Note the MCP server does not expose this (file
     * upload over stdio isn't practical); it exists for completeness/future use.
     */
    public function uploadCv(Request $request): JsonResponse
    {
        $request->validate([
            'cv' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $previous = SiteSetting::get('cv_path');
        if ($previous && Storage::disk('public')->exists($previous)) {
            Storage::disk('public')->delete($previous);
        }

        $path = $request->file('cv')->store('cv', 'public');
        SiteSetting::set('cv_path', $path);

        return response()->json(['cv_path' => $path, 'url' => SiteSetting::cvUrl()]);
    }

    /* ---------------------------------------------------------------------
     | Analytics summary
     * ------------------------------------------------------------------- */

    /**
     * Lean JSON version of the dashboard analytics aggregate (no chart series).
     * Mirrors the query shapes in AdminController::analyticsSummary().
     */
    public function analyticsSummary(): JsonResponse
    {
        $monthStart = Carbon::now()->startOfMonth();

        $thisMonth = fn (string $event) => Analytics::where('event', $event)
            ->where('created_at', '>=', $monthStart)
            ->count();

        $topProjects = Analytics::where('event', 'project_click')
            ->whereNotNull('meta')
            ->selectRaw('meta, COUNT(*) AS total')
            ->groupBy('meta')
            ->orderByDesc('total')
            ->limit(3)
            ->get();

        return response()->json([
            'page_views' => [
                'total' => Analytics::where('event', 'page_view')->count(),
                'this_month' => $thisMonth('page_view'),
            ],
            'cv_downloads' => [
                'total' => Analytics::where('event', 'cv_download')->count(),
                'this_month' => $thisMonth('cv_download'),
            ],
            'contact_clicks' => [
                'email' => Analytics::where('event', 'contact_email')->count(),
                'linkedin' => Analytics::where('event', 'contact_linkedin')->count(),
                'github' => Analytics::where('event', 'contact_github')->count(),
            ],
            'top_projects' => $topProjects,
        ]);
    }
}
