<?php

use Illuminate\Support\Facades\Route;
use App\Http\Requests\StoreTicketRequest;
use App\Actions\CreateTicketAction;

Route::post('/tickets', function (StoreTicketRequest $request, CreateTicketAction $action) {
    $action->execute($request->validated());

    return back()->with('success', '¡Tu ticket ha sido recibido y clasificado exitosamente!');
});


Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
