<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;

class OutlookMailService extends MCGraphAPI
{

    private string $graph_api_url;
    private $graph_api_client;

    public function __construct()
    {
        parent::__construct();
        $this->graph_api_url = 'https://graph.microsoft.com/v1.0/me/';
        $this->graph_api_client = Http::withOptions([
            'base_uri'      => $this->graph_api_url,
            'Content-type'  => 'application/json'
        ]);
    }

    public function send_mail(User $user, string $subject, string $content, array $address, string $content_type = 'Text')
    {
        $response = $this->graph_api_client
            ->withToken($user->mc_access_token)
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
        if($response->status() === 401){
            $data = $this->getRefreshAccessToken($user->mc_refresh_token);
            $refresh_token = $data['refresh_token'];
            $token = $data['access_token'];
            $user  = tap($user)->update([
                'mc_access_token'  => $token,
                'mc_refresh_token' => $refresh_token
            ]);
            return $this->send_mail($user, $subject, $content, $address, $content_type);
        }
        return $response->json();
    }



}
