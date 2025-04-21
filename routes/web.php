<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\RegisterController;
use App\Http\Middleware\CheckIsLogged;
use App\Http\Middleware\CheckIsNotLogged;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;

// Rotas públicas (não logado)
Route::middleware([CheckIsNotLogged::class])->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/loginSubmit', [AuthController::class, 'loginSubmit'])->name('loginSubmit');
    // Register routes
    Route::get('/users/register', [RegisterController::class, 'registerForm'])->name('register');
    Route::post('/users/register', [RegisterController::class, 'registerSubmit'])->name('register.submit');
});

// Rotas protegidas (usuário logado)
Route::middleware([CheckIsLogged::class])->group(function () {

    // Página inicial
    Route::get('/', [MainController::class, 'index'])->name('home');

    // Notas
    Route::get('/newNote', [MainController::class, 'newNote'])->name('newNote');
    Route::post('/newNoteSubmit', [MainController::class, 'newNoteSubmit'])->name('newNoteSubmit');

    Route::get('/editNote/{id}', [MainController::class, 'editNote'])->name('edit');
    Route::post('/editNoteSubmit', [MainController::class, 'editNoteSubmit'])->name('editNoteSubmit');

    Route::get('/deleteNote/{id}', [MainController::class, 'deleteNote'])->name('delete');
    Route::get('/deleteNoteConfirm/{id}', [MainController::class, 'deleteNoteConfirm'])->name('deleteNoteConfirm');

    // Logout
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    // 🔒 Somente ADMIN pode ver os logs
    Route::middleware([CheckRole::class . ':admin'])->group(function () {
        Route::get('/log', [AuditLogController::class, 'index'])->name('audit_log');
        Route::get('/logshow/{id}', [AuditLogController::class, 'show'])->name('audit_log_show');

        // Edição de usuário
        Route::get('/users/user_list', [RegisterController::class, 'user_list'])->name('user_list');
        Route::get('/users/edit/{id}', [RegisterController::class, 'editUser'])->name('users.edit');
        Route::post('/users/update', [RegisterController::class, 'editUserSubmit'])->name('editUserSubmit');
    });
});
