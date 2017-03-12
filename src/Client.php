<?php

namespace Cyphp\Curl;

use Cyphp\Curl\Resource\ResourceInterface;
use Cyphp\Curl\Resource\ResourceAwareInterface;
use Cyphp\Curl\Resource\ResourceConfiguratorInterface;
use Cyphp\Curl\Http\Request;
use Cyphp\Curl\Http\Response;

class Client implements ResourceAwareInterface
{
    protected $resource;

    public function __construct(ResourceInterface $resource = null)
    {
        $this->withResource($resource);
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function withResource(ResourceInterface $resource = null)
    {
        $this->resource = $resource;

        return $this;
    }

    public function configureResource(callable $configurator)
    {
        $configurator($this->resource);

        return $this;
    }

    public function send(Request $request)
    {
        $this->resource->withUrl($request->getUri());
        $method = $request->getMethod();

        $this->resource
            ->withMethod($method)
            ->withOption(CURLOPT_HTTPHEADER, $request->getHeaderLines());

        if ('GET' !== $method) {
            $this->resource->withOption(CURLOPT_POSTFIELDS, $request->getBody());
        }

        $resp = $this->resource->fetch();

        $statusCode = $this->resource->getInfo(CURLINFO_HTTP_CODE);
        $contentType = $this->resource->getInfo(CURLINFO_CONTENT_TYPE);

        $headerSize = $this->resource->getInfo(CURLINFO_HEADER_SIZE);

        $headers = substr($resp, 0, $headerSize);
        $body = substr($resp, $headerSize);

        $this->resource->close();

        return new Response((int) $statusCode, $this->parseHeaders($headers), 'application/json' == $contentType ? json_decode($body, true) : $body);
    }

    protected function parseHeaders(string $headers)
    {
        $headers = preg_split('/\r\n/', $headers, null, PREG_SPLIT_NO_EMPTY);

        array_shift($headers);

        $headersArr = [];

        foreach ($headers as $line) {
            if (strpos($line, 'HTTP') === 0) {
                continue;
            }

            list($header, $value) = preg_split('/:\s/', $line, null, PREG_SPLIT_NO_EMPTY);

            $headersArr[strtoupper($header)] = trim($value);
        }

        return $headersArr;
    }
}
