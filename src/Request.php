<?php

namespace Conduit;

use Conduit\Exceptions\ErrorLimitException;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Conduit\Exceptions\HttpStatusException;
use GuzzleHttp\TransferStats;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\Psr6CacheStorage;
use Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;

class Request
{
    /**
     * @var Client guzzle http client
     */
    protected $httpClient;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * Request constructor.
     * @param Configuration $configuration
     * @param Authentication|null $authentication
     * @param array $request_query
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function __construct(Configuration $configuration, Array $request_query = [], ?Authentication $authentication)
    {
        $this->configuration = $configuration;
        $query = ['datasource' => $this->configuration->getDatasource()];

        if (!is_null($authentication)) {
            $query['token'] = $authentication->getAccessToken();
        }

        $query = array_merge($query, $request_query);

        //caching stuff
        $handlerStack = HandlerStack::create();
        $handlerStack->push(
            new CacheMiddleware(
                new PrivateCacheStrategy(
                    new Psr6CacheStorage(
                        $this->configuration->getCache()
                    )
                )
            ),
            'cache'
        );

        $this->httpClient = new Client([
            'base_uri' => $this->configuration->getESIBaseUri(),
            'timeout' => $this->configuration->getRequestTimeout(),
            'handler' => $handlerStack,
            'query' => $query
        ]);
    }

    /**
     * @param \GuzzleHttp\Psr7\Request $request
     * @return Response
     * @throws ErrorLimitException
     * @throws HttpStatusException
     */
    public function send(\GuzzleHttp\Psr7\Request $request)
    {
        try {
            $response = $this->httpClient->send($request);

        } catch (\Exception $e) {
            throw new HttpStatusException($e->getMessage(), $e->getResponse()->getStatusCode());
        }

        if($response->getHeader('X-ESI-Error-Limit')) {
            throw new ErrorLimitException('ESI error limit reached');
        }

        return new Response($response);
    }
}