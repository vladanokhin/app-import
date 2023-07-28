<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StackDeckApi
{
    const URL = 'https://api.stackdeck.com/api/v1/apps';
    protected $client;

    public function __construct()
    {
        $this->client = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ])->withToken(config('app.stack_deck_token'));
    }

    /**
     * Send request with data for creating app
     * @param array $data
     * @return void
     */
    public function createApp(array $data)
    {
        $response = $this->client->post(self::URL, $data);

        if($response->failed())
            Log::error('StackDeckApi cannot create app:', ['app' => $data['crunchbase_id'], 'response' => $response->json()]);
        else
            Log::info('Successful created app', ['app' => $data['crunchbase_id']]);
    }
}

