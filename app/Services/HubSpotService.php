<?php

namespace App\Services;

use App\Models\Contact;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;

class HubSpotService
{
    protected $client;
    protected $baseUri = 'https://api.hubapi.com';
    protected $accessToken;

    public function __construct()
    {
        // Common Setup HTTP Client
        $this->accessToken = env('HUBSPOT_ACCESS_TOKEN');
        $this->client = new Client([
            'base_uri' => $this->baseUri,
            'timeout'  => 30.0,
            'headers'  => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . session('hubspot_access_token'),
            ],
        ]);
    }



    public function getContacts()
    {
        try {
            $response = $this->client->get('/crm/v3/objects/contacts?properties=firstname,lastname,email,phone,website,lifecyclestage,jobtitle');
            return json_decode($response->getBody()->getContents())->results;
        } catch (RequestException $e) {
            return $this->handleException($e);
        }
    }

    public function updateOrCreateContact($id, Request $request)
    {
        if ($id) {
            return $this->updateContact($id, $request);
        } else {
            $this->createContact($request->all());

            $contact = Contact::findByObjectId($id);
            $contact->hubspot_object_id = $id;
            $contact->save();
        }
    }


    public function updateContact($id, Request $request)
    {
        // Filter out empty fields
        $requestData = array_filter($request->all(), function ($value) {
            return !empty($value);
        });

        // Remove _token field
        unset($requestData['_token']);

        $payload = [
            'json' => [
                'properties' => $requestData
            ]
        ];
        try {
            $response = $this->client->patch('/crm/v3/objects/contacts/' . $id, $payload);
            return json_decode($response->getBody()->getContents());
        } catch (RequestException $e) {
            return $this->handleException($e);
        }
    }

    public function createContact(array $requestData)
    {
        $requestData = array_filter($requestData, function ($value) {
            return !empty($value);
        });

        unset($requestData['_token']);

        $payload = [
            'json' => [
                'properties' => $requestData
            ]
        ];

        try {
            $response = $this->client->post('/crm/v3/objects/contacts/', $payload);
            return json_decode($response->getBody()->getContents());
        } catch (RequestException $e) {
            return $this->handleException($e);
        }
    }

    public function deleteContact($id)
    {
        try {
            $response = $this->client->delete('/crm/v3/objects/contacts/' . $id);
            return true;
        } catch (RequestException $e) {
            return $this->handleException($e);
        }
    }


    public function syncContacts()
    {
        try {

            // Syncing Contacts from HubSpot with System
            $contactsFromHubspot = $this->getContacts();
            $validKeys = ['firstname', 'lastname', 'email', 'phone', 'website', 'lifecyclestage', 'jobtitle'];

            foreach ($contactsFromHubspot as $key => $contactFromHubspot) {
                $data['hubspot_object_id'] = $contactFromHubspot->id;
                foreach (
                    array_filter((array)$contactFromHubspot->properties, function ($key) use ($validKeys) {
                        return in_array($key, $validKeys);
                    }, ARRAY_FILTER_USE_KEY) as $propertykey => $propertyValue
                ) {
                    $data[$propertykey] = $propertyValue;
                }

                Contact::updateOrCreate(['hubspot_object_id' => $contactFromHubspot->id],$data);
            }

            // Syncing New Contacts from System with HubSpot
            $newContactsFromSystem = Contact::whereNull('hubspot_object_id')->get();
            foreach ($newContactsFromSystem as $key => $contact) {
                $newContactData = $contact->toArray();
                unset($newContactData['id']);
                unset($newContactData['created_at']);
                unset($newContactData['updated_at']);
                unset($newContactData['hubspot_object_id']);
                $response = $this->createContact($newContactData);

                $contact->hubspot_object_id = $response->id;
                $contact->save();
            }
        } catch (RequestException $e) {
            return $this->handleException($e);
        }
    }



    private function handleException(RequestException $e)
    {
        if ($e->hasResponse()) {
            return $e->getResponse()->getBody()->getContents();
        }

        return 'Error: ' . $e->getMessage();
    }
}
