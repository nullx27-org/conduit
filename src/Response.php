<?php


namespace Conduit;


class Response
{
    /**
     * @var array Response headers
     */
    protected $headers = [];

    /**
     * @var mixed requested data
     */
    public $data;

    /**
     * Response constructor.
     * @param \GuzzleHttp\Psr7\Response $response
     */
    public function __construct(\GuzzleHttp\Psr7\Response $response)
    {
        $this->headers = $response->getHeaders();
        $this->data = json_decode($response->getBody()->getContents());
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->data->$name;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param string $name
     * @return null|string
     */
    public function getHeader(string $name) : ?string
    {
        if(!array_key_exists($name, $this->headers))
            return null;

        return $this->headers[$name][0];
    }

}