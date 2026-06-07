<?php

use App\Http\Controllers\Api\CmsController;
use App\Http\Controllers\Api\EducationController;
use App\Http\Controllers\Api\ExperienceController;
use App\Http\Controllers\Api\FilterController;
use App\Http\Controllers\Api\ProjectController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Internal CMS API (Step 12)
|--------------------------------------------------------------------------
| Token-protected JSON endpoints over the existing models, consumed by the
| MCP server in /mcp-server. Guarded by the cms.token middleware (Bearer
| token = CMS_API_TOKEN). All paths are prefixed with /api/cms.
*/
Route::prefix('cms')->middleware('cms.token')->name('cms.')->group(function () {
    // Singletons
    Route::get('hero', [CmsController::class, 'showHero'])->name('hero.show');
    Route::put('hero', [CmsController::class, 'updateHero'])->name('hero.update');

    Route::get('about', [CmsController::class, 'showAbout'])->name('about.show');
    Route::put('about', [CmsController::class, 'updateAbout'])->name('about.update');

    Route::get('contact', [CmsController::class, 'showContact'])->name('contact.show');
    Route::put('contact', [CmsController::class, 'updateContact'])->name('contact.update');

    // CV upload (not exposed by the MCP server; kept for completeness).
    Route::post('cv', [CmsController::class, 'uploadCv'])->name('cv.update');

    // Analytics
    Route::get('analytics/summary', [CmsController::class, 'analyticsSummary'])->name('analytics.summary');

    // Media library (read-only) — discover the media_id for hero/project images.
    Route::get('media', [CmsController::class, 'media'])->name('media.index');

    // CRUD entities
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('education', EducationController::class)->parameters(['education' => 'education']);
    Route::apiResource('experience', ExperienceController::class)->parameters(['experience' => 'experience']);
    Route::apiResource('filters', FilterController::class);
});
