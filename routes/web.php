<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\AuditLogController;
use App\Http\Middleware\CheckIsLogged;
use App\Http\Middleware\CheckIsNotLogged;
use Illuminate\Support\Facades\Route;

// Rotas de autenticação
Route::middleware([CheckIsNotLogged::class])->group(function () {
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/loginSubmit', [AuthController::class, 'loginSubmit'])->name('loginSubmit');
});

Route::middleware([CheckIsLogged::class])->group(function () {
    Route::get('/', [MainController::class, 'index'])->name('home');
    // NOVA NOTA
    Route::get('/newNote', [MainController::class, 'newNote'])->name('newNote');
    Route::post('/newNoteSubmit', [MainController::class, 'newNoteSubmit'])->name('newNoteSubmit');

    // EDITAR NOTA
    Route::get('/editNote/{id}', [MainController::class, 'editNote'])->name('edit');
    Route::post('/editNoteSubmit', [MainController::class, 'editNoteSubmit'])->name('editNoteSubmit');


    //  DELETAR NOTA
    Route::get('/deleteNote/{id}', [MainController::class, 'deleteNote'])->name('delete');
    Route::get('/deleteNoteConfirm/{id}', [MainController::class, 'deleteNoteConfirm'])->name('deleteNoteConfirm');


// logout
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/log', [AuditLogController::class, 'index'])->name('audit_log');
    Route::get('/logshow/{id}', [AuditLogController::class, 'show'])->name('audit_log_show');

});

// Route::any('/loginSubmit', function () {
//     return redirect()->route('login')
//          ->with('error', 'Você deve enviar o formulário de login');
// });
