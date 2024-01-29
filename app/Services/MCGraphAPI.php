<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MCGraphAPI
{

    private $auth_client;

    private $graph_api_url;

    public function __construct()
    {
        $this->auth_client = Http::withOptions([
            'base_uri'      => 'https://login.microsoftonline.com',
            'Content-Type'  => 'application/x-www-form-urlencoded'
        ]);

        $this->graph_api_url = Http::withOptions([
            'base_uri'      => 'https://graph.microsoft.com/v1.0/me/',
        ]);
    }

    /**
     *
     * get the code to generate the access_token
     *
     * @return string
     */
    public function getAuthCodeUrl(): string
    {
        return sprintf(
            'https://login.microsoftonline.com/%s/oauth2/v2.0/authorize?%s',
            config('services.microsoft.tenant_id'),
            http_build_query([
                'client_id'     => config('services.microsoft.client_id'),
                'response_type' => 'code',
                'redirect_uri'  => route('microsoft.webhook'),
                'response_mode' => 'query',
                'scope'         => config('services.microsoft.scopes'),
                'state'         => $this->generate_state_code()
        ]));
    }

    /**
     *
     * verify the state code to check request is authentic
     *
     * @param string $state_code
     * @return bool
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function verifyAuthCode(string $state_code): bool
    {
        if(session()->has('state_code')){
            if($state_code === session()->get('state_code')){
                session()->forget('state_code');
                return true;
            }
            return false;
        }
        return false;
    }


    /**
     *
     * get the access token
     *
     * @param string $code
     * @return array
     */
    public function getAccessToken(string $code): array
    {
        $response = $this->auth_client->asForm()->post(sprintf('%s/oauth2/v2.0/token', config('services.microsoft.tenant_id')), [
            'client_id'     => config('services.microsoft.client_id'),
            'grant_type'    => 'authorization_code',
            'redirect_uri'  => route('microsoft.webhook'),
            'scope'         => config('services.microsoft.scopes'),
            'code'          => $code,
            'client_secret' => config('services.microsoft.client_secret')
        ]);
        return $response->json();
    }


    /**
     *
     * generate the code for verification state
     *
     * @return string
     */
    private function generate_state_code(): string
    {
        $code_state = Str::random(
            mt_rand(
                mt_rand(100, 200)
                ,
                mt_rand(500, 600)
            )
        );
        session()->put([
            'state_code' => $code_state
        ]);
        return $code_state;
    }


}
