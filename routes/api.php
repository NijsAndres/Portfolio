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

    // CV — read the current file + upload/replace it.
    Route::get('cv', [CmsController::class, 'showCv'])->name('cv.show');
    Route::post('cv', [CmsController::class, 'uploadCv'])->name('cv.update');

    // Analytics
    Route::get('analytics/summary', [CmsController::class, 'analyticsSummary'])->name('analytics.summary');

    // Media library — list (discover media_id for images), upload, edit metadata, delete.
    Route::get('media', [CmsController::class, 'media'])->name('media.index');
    Route::post('media', [CmsController::class, 'storeMedia'])->name('media.store');
    Route::put('media/{media}', [CmsController::class, 'updateMedia'])->name('media.update');
    Route::delete('media/{media}', [CmsController::class, 'destroyMedia'])->name('media.destroy');

    // CRUD entities. Reorder routes are registered before each apiResource so
    // POST <entity>/reorder isn't shadowed by the resource's routes.
    Route::post('projects/reorder', [ProjectController::class, 'reorder'])->name('projects.reorder');
    Route::apiResource('projects', ProjectController::class);

    Route::post('education/reorder', [EducationController::class, 'reorder'])->name('education.reorder');
    Route::apiResource('education', EducationController::class)->parameters(['education' => 'education']);

    Route::post('experience/reorder', [ExperienceController::class, 'reorder'])->name('experience.reorder');
    Route::apiResource('experience', ExperienceController::class)->parameters(['experience' => 'experience']);

    Route::post('filters/reorder', [FilterController::class, 'reorder'])->name('filters.reorder');
    Route::apiResource('filters', FilterController::class);
});
