<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OutlookMailService
{

    private string $graph_api_url;
    private $graph_api_client;

    public function __construct()
    {
        $this->graph_api_url = 'https://graph.microsoft.com/v1.0/me/';
        $this->graph_api_client = Http::withOptions([
            'base_uri'      => $this->graph_api_url,
            'Content-type'  => 'application/json'
        ]);
    }

    public function send_mail(string $token, string $subject, string $content, array $address, string $content_type = 'Text')
    {
        $response = $this->graph_api_client->dd()
            ->withToken($token)
            ->withBody(json_encode([
                'message' => [
                    'subject' => $subject,
                    'body' => [
                        'contentType' => $content_type,
                        'content'     => $content
                    ],
                    'toRecipients' => $address
                ]
            ]), 'application/json')
            ->post('sendMail');

        dd($response);
    }

}
