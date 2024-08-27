<?php

use App\Http\Controllers\ContactsController;
use App\Http\Controllers\HubSpotController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// primary index map to same as contact list page
Route::get('/',[ContactsController::class, 'index'])->name('index');

// All the CRUD routes for contacts
Route::resource('/contacts', ContactsController::class)->names('contacts')->except(['create','edit','show']);

// Route for generating paginated data for contacts datatable
Route::get('/get-contacts', [ContactsController::class, 'getContacts'])->name('contacts.list');

// Sync with hubspot
Route::get('contacts-sync', [ContactsController::class, 'sync'])->name('contacts.sync');

Route::get('/hubspot/authorize', [HubSpotController::class, 'authorize'])->name('hubspot.authorize');
Route::get('/hubspot/callback', [HubSpotController::class, 'callback'])->name('hubspot.callback');
Route::get('/hubspot/logout', [HubSpotController::class, 'logout'])->name('hubspot.logout');
