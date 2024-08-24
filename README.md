# HubSpot Integration with Laravel

This project integrates HubSpot CRM Contacts using the **HubSpot REST API** and **Webhooks**.

## Running the Project Locally

1. **Clone the Project**

   ```bash
   git clone https://github.com/kozhinhikkodan-dev/hubspot-laravel-integration.git
   ```

2. **Navigate to the Project Directory**

   ```bash
   cd hubspot-laravel-integration
   ```

3. **Set Up Environment Variables**

   Copy the example environment file and update it:

   ```bash
   cp .env.example .env
   ```

   Then, ensure that the `.env` file includes your HubSpot access token:

   ```bash
   HUBSPOT_ACCESS_TOKEN={YOUR_HUBSPOT_ACCESS_TOKEN}
   ```

4. **Install Composer Dependencies**

   ```bash
   composer install
   ```

5. **Start the Laravel Development Server**

   ```bash
   php artisan serve
   ```

6. **Expose the Local Server to the Internet Using ngrok**

   To handle webhooks, you need to expose your local server:

   ```bash
   ngrok http 8000
   ```

7. **Configure HubSpot Webhooks**

   Add the ngrok URL to the HubSpot Webhook configuration:

   ```bash
   https://{ID}.ngrok-free.app/api/hubspot-webhook
   ```

## Additional Feature

- **Sync with HubSpot button** :
  This is only needed when any system ( HubSpot API or Webhook or Our Application ) fails real-time 2 way sync, we can manualy by triggering this action, **PLEASE NOTE THIS IS NOT PART OF 2 WAY SYNC - JUST AN ADDITIONAL AND NECESSARY FEATURE WHICH WILL NOT NEED WHEN SYSTEM WORKS PERFECTLY FINE**




---