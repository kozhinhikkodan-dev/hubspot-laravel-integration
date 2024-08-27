<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Services\HubSpotService;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class HubSpotController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function authorize()
    {
        $query = http_build_query([
            'client_id' => env('HUBSPOT_CLIENT_ID'),
            'scope' => 'crm.objects.contacts.read crm.objects.contacts.write',
            'redirect_uri' => route('hubspot.callback'),
        ]);

        return redirect('https://app.hubspot.com/oauth/authorize?' . $query);
    }

    public function callback(Request $request)
    {
        $response = $this->client->post('https://api.hubapi.com/oauth/v1/token', [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => env('HUBSPOT_CLIENT_ID'),
                'client_secret' => env('HUBSPOT_CLIENT_SECRET'),
                'redirect_uri' => route('hubspot.callback'),
                'code' => $request->code,
            ],
        ]);

        $accessToken = json_decode((string) $response->getBody(), true)['access_token'];

        // Store the access token in the session, since user login is not set
        session(['hubspot_access_token' => $accessToken]);

        Contact::truncate();
        app(HubSpotService::class)->syncContacts();

        // redirect to home route
        return redirect('/');
    }


    public function logout()
    {
        Contact::truncate();

        // forget session key 
        session()->forget('hubspot_access_token');

        // redirect to home route
        return redirect('/');
    }
}
