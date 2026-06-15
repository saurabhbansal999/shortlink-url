<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShortUrlController;
use App\Models\ShortUrl;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Welcome', [
    'canLogin'    => Route::has('login'),
    'canRegister' => Route::has('register'),
]))->name('welcome');

Route::middleware(['auth', 'verified'])->get('/dashboard', fn () => Inertia::render('Dashboard'))->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('companies')->group(function () {
        Route::get('/', [CompanyController::class, 'index'])->name('companies.index');
        Route::get('/create', [CompanyController::class, 'create'])->name('companies.create');
        Route::post('/store', [CompanyController::class, 'store'])->name('companies.store');
    });

    Route::prefix('members')->group(function () {
        Route::get('/', [MemberController::class, 'index'])->name('members.index');
        Route::get('/create', [MemberController::class, 'create'])->name('members.create');
        Route::post('/store', [MemberController::class, 'store'])->name('members.store');
    });

    Route::prefix('urls')->group(function () {
        Route::get('/', [ShortUrlController::class, 'index'])->name('urls.index');
        Route::post('/', [ShortUrlController::class, 'store'])->name('urls.store');
        Route::post('/generate-all', [ShortUrlController::class, 'generateAll'])->name('urls.generate-all');
        Route::post('/{ulid}/shorten', [ShortUrlController::class, 'shorten'])->name('urls.shorten');
    });
});

Route::get('/s/{code}', function (string $code) {
    $url = ShortUrl::where('short_code', $code)->firstOrFail();
    return redirect($url->original_url);
})->name('short.redirect');

require __DIR__.'/auth.php';
