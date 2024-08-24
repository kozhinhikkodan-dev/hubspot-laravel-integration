<?php

use App\Http\Controllers\HubspotWebhookController;
use Illuminate\Support\Facades\Route;


// API Route for handling webhook from hubspot
Route::post('/hubspot-webhook',[HubspotWebhookController::class, 'handleWebhook']);