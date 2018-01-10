<?php


namespace Conduit;

use GuzzleHttp\Psr7\Request as HttpRequest;

class Endpoint
{
    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @var string
     */
    protected $uri;

    /**
     * @var array
     */
    protected $body = [];

    /**
     * @var Authentication|null
     */
    protected $authentication;

    /**
     * @var Configuration
     */
    protected $configuration;

    public function __construct(string $endpoint, array $params, Configuration $configuration, ?Authentication $authentication)
    {
        $this->configuration = $configuration;
        $this->authentication = $authentication;

        $this->endpoint = $endpoint;

        if(!empty($params)) {
            $this->endpoint = $this->endpoint . '/' . $params[0] . '/';
        }

        return $this;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return Endpoint
     */
    public function __call(string $name, array $arguments) : self
    {
        $this->uri .= $name;

        if(!empty($arguments)) {
            $this->endpoint = $this->endpoint . '/' . $arguments[0] . '/';
        }

        return $this;
    }

    /**
     * @param string $name
     * @return Endpoint
     */
    public function __get(string $name) : self {
        $this->uri .= $name .'/';

        return $this;
    }


    /**
     * @param array $data
     * @return Endpoint
     */
    public function data(array $data) : self
    {
        $this->body = array_merge($this->body, $data);
        return $this;
    }

    /**
     * @return string
     */
    private function getRequestURI() : string
    {
        return rtrim($this->endpoint . $this->uri, '/') . '/';
    }

    /**
     * @return string
     */
    public function getBody()
    {
        // if there are no body elements we need an empty string or ESI will throw an error
        if(empty($this->body))
            return '';

        return json_encode($this->body);
    }

    /**
     * @return Response
     */
    public function get()
    {
        $request = new HttpRequest('GET', $this->getRequestURI(), $this->configuration->getDefaultHeaders(), $this->getBody());

        return $this->makeRequest($request);
    }

    /**
     * @return Response
     */
    public function post()
    {
        $request = new HttpRequest('POST', $this->getRequestURI(), $this->configuration->getDefaultHeaders(), $this->getBody());

        return $this->makeRequest($request);
    }

    /**
     * @return Response
     */
    public function put()
    {
        $request = new HttpRequest('PUT', $this->getRequestURI(), $this->configuration->getDefaultHeaders(), $this->getBody());

        return $this->makeRequest($request);
    }

    /**
     * @return Response
     */
    public function delete()
    {
        $request = new HttpRequest('DELETE', $this->getRequestURI(), $this->configuration->getDefaultHeaders(), $this->getBody());

        return $this->makeRequest($request);
    }

    /**
     * @param HttpRequest $request
     * @return Response
     */
    private function makeRequest(HttpRequest $request)
    {
        return (new Request($this->configuration, $this->authentication))->send($request);
    }

}