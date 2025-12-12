<?php

use App\Http\Controllers\TodolistController;
use App\Models\Todolist;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', fn () => Inertia::render('Welcome'))->name('home');
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard') // logged-in users go to dashboard
        : redirect()->route('login');   // guests go to login
})->name('home');

Route::middleware(['auth', 'verified'])->group(function (): void {

    Route::get('dashboard', fn () => Inertia::render('Dashboard', [
        'todolist' => Todolist::where('task_date', today())->orderByRaw("
            CASE priority
                WHEN 'High' THEN 1
                WHEN 'Medium' THEN 2
                WHEN 'Low' THEN 3
            END
        ")->get(),
        'todayFormatDate' => Carbon::now()->format('l, F d, Y'),
        'dateToday' => Carbon::now()->toDateString()
    ]))->name('dashboard');
    Route::get('inbox', fn () => Inertia::render('Inbox'))->name('inbox');
    Route::get('customers', fn () => Inertia::render('Customers'))->name('customers');


    Route::get('todolist', [TodolistController::class, 'index'])->name('todolist');
    Route::get('todolist/get_todos', [TodolistController::class, 'get_todos']);
    Route::get('todolist/show/{id}', [TodolistController::class, 'show']);
    Route::get('todolist/show_table_data', [TodolistController::class, 'show_table_data']);
    Route::post('todolist/store', [TodolistController::class, 'store'])->name('todolist.store');
    Route::put('todolist/update/{id}', [TodolistController::class, 'update'])->name('todolist.update');
    Route::delete('todolist/destroy/{id}', [TodolistController::class, 'destroy'])->name('todolist.destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
