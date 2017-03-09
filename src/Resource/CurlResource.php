<?php

namespace Cyphp\Curl\Resource;

class CurlResource implements ResourceInterface
{
    protected $handle = null;
    protected $options = [];

    public function __construct()
    {
        $this->handle = curl_init();

        if (false === $this->handle) {
            throw new \ErrorException('Fail to initialize cURL session');
        }
    }

    public function exists()
    {
        return is_resource($this->handle);
    }

    public function getResource()
    {
        return $this->handle;
    }

    public function close()
    {
        if ($this->exists()) {
            curl_close($this->handle);
        }
    }

    public function fetch()
    {
        $return = curl_exec($this->handle);

        if (false === $return) {
            throw new \Exception(curl_error($this->handle), curl_errno($this->handle));
        }

        return $return;
    }

    /**
     * Get information regarding a specific transfer.
     *
     * @see http://php.net/manual/en/function.curl-getinfo.php
     *
     * @param int|null $option curl option code
     *
     * @return mixed
     */
    public function getInfo(int $option = null)
    {
        if (null === $option) {
            return curl_getinfo($this->handle);
        }

        return curl_getinfo($this->handle, $option);
    }
    /**
     * set curl option.
     *
     * @param int    $option
     * @param [type] $value
     *
     * @return CurlResource
     */
    public function withOption(int $option, $value)
    {
        if (!curl_setopt($this->handle, $option, $value)) {
            throw new \ErrorException('Fail to set option');
        }

        $this->options[$option] = $value;

        return $this;
    }

    public function getOption(int $option)
    {
        if (!isset($this->options[$option])) {
            return;
        }

        return $this->options[$option];
    }

    /**
     * A custom request method to use instead of "GET" or "HEAD" when doing a HTTP request. This is
     * useful for doing "DELETE" or other, more obscure HTTP requests. Valid values are things like
     * "GET", "POST", "CONNECT" and so on;.
     *
     * @param string $method
     *
     * @return CurlResource
     */
    public function withMethod(string $method)
    {
        if ('GET' === $method) {
            return $this;
        }

        $this->withOption(CURLOPT_POST, true);

        if ('POST' !== $method) {
            $this->withOption(CURLOPT_CUSTOMREQUEST, $method);
        }

        return $this;
    }

    /**
     * The URL to fetch.
     *
     * @param string $url
     *
     * @return CurlResource
     */
    public function withUrl(string $url)
    {
        return $this->withOption(CURLOPT_URL, $url);
    }
}
