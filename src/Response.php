<?php


namespace Conduit;


use Conduit\Exceptions\PropertyNotFoundException;
use PHPUnit\Runner\Exception;

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
     * @throws PropertyNotFoundException
     */
    public function __get(string $name)
    {
        try {
            return $this->data->$name;
        } catch (\Exception $exception)
        {
            throw new PropertyNotFoundException("$name was not returned by the API");
        }
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

    /**
     * @param string $name
     * @param null $default
     * @return null|mixed
     */
    public function get(string $name, $default = null) {
        if(isset($this->data->$name)) {
            return $this->data->$name;
        } else {
          return $default;
        }
    }
}