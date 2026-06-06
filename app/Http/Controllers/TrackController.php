<?php

namespace App\Http\Controllers;

use App\Models\Analytics;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Public, unauthenticated endpoint that records a single analytics event
 * (DIY analytics, Step 11). Fired from the frontend via fetch/sendBeacon.
 * Rate limited in routes/web.php and CSRF-exempt in bootstrap/app.php, since
 * sendBeacon cannot attach the CSRF header.
 */
class TrackController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event' => ['required', Rule::in(Analytics::EVENTS)],
            'meta' => ['nullable', 'string', 'max:255'],
        ]);

        Analytics::create($validated);

        return response()->json(['ok' => true]);
    }
}
