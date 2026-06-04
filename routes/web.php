<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\EducationController;
use App\Http\Controllers\Admin\ExperienceController;
use App\Http\Controllers\Admin\FilterController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin (CMS)
|--------------------------------------------------------------------------
| All routes here are protected by the auth middleware and share the
| admin. name prefix. Login/redirect behaviour is unchanged for now and is
| revisited in Step 10.
*/
Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {
    // Convenience redirect: /admin -> /admin/dashboard
    Route::get('/', fn () => redirect()->route('admin.dashboard'));

    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Hero (single row)
    Route::get('hero', [AdminController::class, 'editHero'])->name('hero.edit');
    Route::put('hero', [AdminController::class, 'updateHero'])->name('hero.update');

    // About (single row)
    Route::get('about', [AdminController::class, 'editAbout'])->name('about.edit');
    Route::put('about', [AdminController::class, 'updateAbout'])->name('about.update');

    // Contact (single row)
    Route::get('contact', [AdminController::class, 'editContact'])->name('contact.edit');
    Route::put('contact', [AdminController::class, 'updateContact'])->name('contact.update');

    // CV upload
    Route::post('cv', [AdminController::class, 'uploadCv'])->name('cv.update');

    // Drag-and-drop reordering — registered before the resource routes so the
    // fixed sub-path wins. Both lists live on the projects index page.
    Route::post('projects/reorder', [ProjectController::class, 'reorder'])->name('projects.reorder');
    Route::post('filters/reorder', [FilterController::class, 'reorder'])->name('filters.reorder');

    // Clone an existing project (fields, linked filters, and a fresh copy of the
    // uploaded image). Registered before the resource routes so it isn't shadowed.
    Route::post('projects/{project}/duplicate', [ProjectController::class, 'duplicate'])->name('projects.duplicate');

    // CRUD entities. No detail pages in the admin, and the resource
    // controllers define no show() method, so exclude the show route.
    Route::resource('projects', ProjectController::class)->except('show');
    // Filters are listed on the projects page; only their CRUD forms live here.
    Route::resource('filters', FilterController::class)->except(['show', 'index']);
    Route::resource('education', EducationController::class)->except('show');
    Route::resource('experience', ExperienceController::class)->except('show');
});

require __DIR__.'/auth.php';
