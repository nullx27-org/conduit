<?php


namespace Conduit;


use Cache\Adapter\PHPArray\ArrayCachePool;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Configuration
{

    /**
     * @var null|Configuration
     */
    protected static $instance = null;

    /**
     * @var CacheItemPoolInterface
     */
    protected $cache;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var string ESI data source
     */
    protected $datasource = 'tranquility';

    /**
     * @var array default http handlers
     */
    protected $defaultHeaders = [];

    /**
     * @var int guzzle request timeout
     */
    protected $requestTimeout = 20;

    /**
     * @var string
     */
    protected $userAgent = 'nullx27/Conduit';


    /**
     * Configuration constructor.
     */
    private final function __construct()
    {
        $this->setCache(new ArrayCachePool());
        $this->setLogger(new NullLogger());

        $this->defaultHeaders = [
            'User-Agent' => $this->getUserAgent(),
            'Accept' => 'application/json'
        ];
    }

    /**
     * @return Configuration
     */
    public static function getInstance() : self
    {
        if(is_null(static::$instance)) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    /**
     * @return mixed
     */
    public function getCache(): CacheItemPoolInterface
    {
        return $this->cache;
    }

    /**
     * @param mixed $cache
     */
    public function setCache(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return mixed
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param mixed $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public function getDefaultHeaders(): array
    {
        return $this->defaultHeaders;
    }

    /**
     * @return mixed
     */
    public function getRequestTimeout(): int
    {
        return $this->requestTimeout;
    }

    /**
     * @param mixed $requestTimeout
     */
    public function setRequestTimeout(int $requestTimeout)
    {
        $this->requestTimeout = $requestTimeout;
    }

    /**
     * @return mixed
     */
    public function getUserAgent() : string
    {
        return $this->userAgent;
    }

    /**
     * @param mixed $userAgent
     */
    public function setUserAgent(string $userAgent)
    {
        $this->userAgent = $userAgent;
    }

    /**
     * @return mixed
     */
    public function getDatasource() : string
    {
        return $this->datasource;
    }

    /**
     * @param mixed $datasource
     */
    public function setDatasource(string $datasource)
    {
        $this->datasource = $datasource;
    }


}