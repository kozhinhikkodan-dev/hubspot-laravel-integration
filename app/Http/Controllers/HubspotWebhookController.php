<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;

class HubspotWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // Get the incoming payload
        $payload = $request->all();

        // If the change detected from API integration and the webhook trigggered, 
        // we can ignore that webhook trigger, since that change is already handled from the system
        if($payload[0]['changeSource'] == 'INTEGRATION'){
            return true;
        }
       
        // Sort payload by subscriptionType, ensuring 'contact.creation' is first in order to handle new contacts
        usort($payload, function ($a, $b) {
            if ($a['subscriptionType'] === 'contact.creation') {
                return -1;
            }
            if ($b['subscriptionType'] === 'contact.creation') {
                return 1;
            }
            return 0;
        });

        foreach ($payload as $event) {
            $subscriptionType = $event['subscriptionType'];
            $contactObjectId = $event['objectId'];
            switch ($subscriptionType) {
                case 'contact.creation':
                    $this->handleContactCreation($contactObjectId, $event);
                    break;

                case 'contact.propertyChange':
                    $this->handleContactUpdate($contactObjectId, $event);
                    break;

                case 'contact.deletion':
                    $this->handleContactDeletion($contactObjectId);
                    break;

            }
        }

        return response()->json(['status' => 'success'], 200);
    }

    private function handleContactCreation($contactObjectId, $event)
    {
        // Extract the necessary event
        $contactData = $this->extractContactData($event);

        // Create a new contact in the local database
        Contact::updateOrCreate(['hubspot_object_id' => $contactObjectId], $contactData);
    }

    private function handleContactUpdate($contactObjectId, $event)
    {
        // Extract the necessary event
        $contactData = $this->extractContactData($event);

        // Update the existing contact in the local database
        Contact::updateOrCreate(['hubspot_object_id' => $contactObjectId], $contactData);
    }

    private function handleContactDeletion($contactObjectId)
    {
        // Find the contact by HubSpot ID via model scope and delete it from the local database
        Contact::findByObjectId($contactObjectId)->delete();
    }

    private function extractContactData($event)
    {
        // Extract only properties from event
        $contactData = [];
        $contactData['hubspot_object_id'] = $event['objectId'];
        if (array_key_exists('propertyName', $event)) {
            $contactData[$event['propertyName']] = $event['propertyValue'] ?? null;
        }

        return $contactData;
    }
}
