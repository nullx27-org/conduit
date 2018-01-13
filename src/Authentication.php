<?php


namespace Conduit;


use GuzzleHttp\Client;

class Authentication
{
    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var string
     */
    protected $refreshToken;

    /**
     * @var null|string
     */
    protected $accessToken = null;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * Authentication constructor.
     * @param string $clientId
     * @param string $clientSecret
     * @param string $refreshToken
     */
    public function __construct(string $clientId, string $clientSecret, string $refreshToken)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->refreshToken = $refreshToken;

        $this->configuration = Configuration::getInstance();
    }

    /**
     * @return string
     */
    private function getBaseLoginUrl()
    {
        if ($this->configuration->getDatasource() == 'singularity') {
            return 'https://sisilogin.testeveonline.com';
        }

        return 'https://login.eveonline.com';
    }

    /**
     * @return mixed
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getAccessToken()
    {
        $cache = $this->configuration->getCache();

        // create a hash of the refresh token so we don't expose it in case the cache is leaking
        $cacheKey = hash('sha256', $this->refreshToken);
        $item = $cache->getItem($cacheKey);

        if (is_null($this->accessToken) || !$item->isHit()) {
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
            $item->set($data->access_token);
            $item->expiresAfter(1140); // 19 min
            $cache->save($item);
        }

        return $item->get();
    }


}