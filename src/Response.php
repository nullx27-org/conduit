<?php


namespace Conduit;


class Response
{
    /**
     * @var array Response headers
     */
    public $headers = [];

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

}