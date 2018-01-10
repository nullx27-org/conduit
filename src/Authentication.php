<?php


namespace Conduit;


use GuzzleHttp\Client;

class Authentication
{
    protected $clientId;
    protected $clientSecret;
    protected $refreshToken;
    protected $accessToken = null;

    protected $configuration;

    public function __construct(string $clientId, string $clientSecret, string $refreshToken)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->refreshToken = $refreshToken;

        $this->configuration = Configuration::getInstance();
    }

    private function getBaseLoginUrl()
    {
        if ($this->configuration->getDatasource() == 'singularity')
        {
            return 'https://sisilogin.testeveonline.com';
        }

        return 'https://login.eveonline.com';
    }


    public function getAccessToken()
    {
        if(is_null($this->accessToken)) { //todo: check access token expiry and renew automatically etc
            $httpClient = new Client();
            $payload = [
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->refreshToken
            ];
            $response = $httpClient->request('POST', $this->getBaseLoginUrl() . '/oauth/token',
                [
                    'auth' => [$this->clientId, $this->clientSecret],
                    'form_params' => $payload
                ]
            );

            $data = \GuzzleHttp\json_decode($response->getBody()->getContents());

            $this->accessToken = $data->access_token;
        }

        return $this->accessToken;
    }


}