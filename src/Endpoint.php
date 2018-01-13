<?php


namespace Conduit;

use Conduit\Exceptions\InvalidArgumentException;
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

    public function __construct(
        string $endpoint,
        array $params,
        Configuration $configuration,
        ?Authentication $authentication
    ) {
        $this->configuration = $configuration;
        $this->authentication = $authentication;

        $this->endpoint = $endpoint . '/';

        if (!empty($params)) {
            $this->endpoint .= $params[0] . '/';
        }

        return $this;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return Endpoint
     * @throws InvalidArgumentException
     */
    public function __call(string $name, array $arguments): self
    {
        $this->uri .= $name . '/';

        if (!empty($arguments)) {
            $this->uri .= $arguments[0] . '/';

            if (array_key_exists(1, $arguments)) {
                if (!is_array($arguments[1])) {
                    throw new InvalidArgumentException('Body data needs to be an array');
                }
            }
        }

        return $this;
    }


    /**
     * @param array $data
     * @return Endpoint
     */
    public function data(array $data): self
    {
        $this->body = array_merge($this->body, $data);
        return $this;
    }

    /**
     * @return string
     */
    private function getRequestURI(): string
    {
        return rtrim($this->endpoint . $this->uri, '/') . '/';
    }

    /**
     * @return string
     */
    public function getBody()
    {
        // if there are no body elements we need an empty string or ESI will throw an error
        if (empty($this->body)) {
            return '';
        }

        return json_encode($this->body);
    }

    /**
     * @return Response
     * @throws Exceptions\ErrorLimitException
     * @throws \HttpRequestException
     */
    public function get()
    {
        $request = new HttpRequest('GET', $this->getRequestURI(), $this->configuration->getDefaultHeaders(),
            $this->getBody());

        return $this->makeRequest($request);
    }

    /**
     * @return Response
     * @throws Exceptions\ErrorLimitException
     * @throws \HttpRequestException
     */
    public function post()
    {
        $request = new HttpRequest('POST', $this->getRequestURI(), $this->configuration->getDefaultHeaders(),
            $this->getBody());

        return $this->makeRequest($request);
    }

    /**
     * @return Response
     * @throws Exceptions\ErrorLimitException
     * @throws \HttpRequestException
     */
    public function put()
    {
        $request = new HttpRequest('PUT', $this->getRequestURI(), $this->configuration->getDefaultHeaders(),
            $this->getBody());

        return $this->makeRequest($request);
    }

    /**
     * @return Response
     * @throws Exceptions\ErrorLimitException
     * @throws \HttpRequestException
     */
    public function delete()
    {
        $request = new HttpRequest('DELETE', $this->getRequestURI(), $this->configuration->getDefaultHeaders(),
            $this->getBody());

        return $this->makeRequest($request);
    }

    /**
     * @param HttpRequest $request
     * @return Response
     * @throws Exceptions\ErrorLimitException
     * @throws \HttpRequestException
     */
    private function makeRequest(HttpRequest $request)
    {
        return (new Request($this->configuration, $this->authentication))->send($request);
    }

}