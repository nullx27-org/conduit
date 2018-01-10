<?php


namespace Conduit;


use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
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
     */
    public function __construct(Configuration $configuration, ?Authentication $authentication)
    {
        $this->configuration = $configuration;
        $query = ['datasource' => $this->configuration->getDatasource()];

        if(!is_null($authentication)) {
            $query['token'] = $authentication->getAccessToken();
        }

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
            'base_uri' => 'https://esi.tech.ccp.is/latest/',
            'timeout' => $this->configuration->getRequestTimeout(),
            'query' => $query,
            'handler' => $handlerStack
        ]);
    }

    /**
     * @param \GuzzleHttp\Psr7\Request $request
     * @return Response
     */
    public function send(\GuzzleHttp\Psr7\Request $request)
    {

        try{
            $response = $this->httpClient->send($request);
        } catch (\Exception $e) {
            print_r($e->getMessage());
        }

        return new Response($response);
    }
}