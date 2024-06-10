<?php

use App\Http\Controllers\AddressSuggestionController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UsersController;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

#rotas não autenticadas criação de user e login

Route::post('/createUser',[UsersController::class, 'store'])->name('store.user');
Route::post('/login',[LoginController::class, 'login'])->name('do.login');

#####forgot password####

Route::post('/forgotPassword', [ForgotPasswordController::class, 'reset'])->name('password.email');

#demais rotas autenticadas
Route::group(['middleware' => 'auth:api'], function(){
    ####contacts####

    Route::post('/createContacts',[ContactController::class, 'store'])->name('contacts.store');
    Route::get('/contacts/{page}', [ContactController::class, 'index'])->name('contacts.index');
    Route::delete('/contacts/{id}', [ContactController::class, 'delete'])->name('contacts.delete');
    Route::put('/contacts/{id}', [ContactController::class, 'update'])->name('contacts.update');

    ####users####

    Route::delete('/deleteUser',[UsersController::class, 'destroyWithPassword'])->name('user.delete');

    ####address suggestion####

    Route::post('/address/suggest', [AddressSuggestionController::class, 'suggest']);

});
