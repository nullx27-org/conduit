<?php


namespace Conduit;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class Conduit
{
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var Authentication|null
     */
    protected $authentication;


    /**
     * Conduit constructor.
     * @param Authentication|null $authentication
     */
    public function __construct(Authentication $authentication = null)
    {
        $this->configuration = Configuration::getInstance();
        $this->authentication = $authentication;
    }

    /**
     * @return Configuration
     */
    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    /**
     * @param string $name
     * @param array $params
     * @return Endpoint
     */
    public function __call(string $name, array $params)
    {
        return new Endpoint($name, $params, $this->configuration, $this->authentication);
    }

    /**
     * @param Authentication $authentication
     */
    public function setAuthentication(Authentication $authentication): void
    {
        $this->authentication = $authentication;
    }


}