<?php

namespace Cyphp\Curl;

use Cyphp\Curl\Resource\ResourceInterface;
use Cyphp\Curl\Http\Request;
use Cyphp\Curl\Http\Response;

class Client
{
    protected $cr;

    public function __construct(ResourceInterface $cr = null)
    {
        $this->withCurlResource($cr);
    }

    public function withCurlResource(ResourceInterface $cr = null)
    {
        $this->cr = $cr;

        return $this;
    }

    public function send(Request $request)
    {
        $this->cr->withUrl($request->getUri());
        $method = $request->getMethod();

        $this->cr
            ->withMethod($method)
            ->withOption(CURLOPT_VERBOSE, false)
            ->withOption(CURLOPT_RETURNTRANSFER, true)
            ->withOption(CURLOPT_HEADER, true)
            ->withOption(CURLOPT_HTTPHEADER, $request->getHeaderLines())
            ->withOption(CURLOPT_CONNECTTIMEOUT, 2)
            ->withOption(CURLOPT_TIMEOUT, 12)
            ->withOption(CURLOPT_SSL_VERIFYPEER, false)
            ->withOption(CURLOPT_SSL_VERIFYHOST, false)
            ->withOption(CURLOPT_FOLLOWLOCATION, true);

        if ('GET' !== $method) {
            $this->cr->withOption(CURLOPT_POSTFIELDS, $request->getBody());
        }

        $resp = $this->cr->fetch();

        $statusCode = $this->cr->getInfo(CURLINFO_HTTP_CODE);
        $contentType = $this->cr->getInfo(CURLINFO_CONTENT_TYPE);

        $headerSize = $this->cr->getInfo(CURLINFO_HEADER_SIZE);

        $headers = substr($resp, 0, $headerSize);
        $body = substr($resp, $headerSize);

        $this->cr->close();

        return new Response((int) $statusCode, $this->parseHeaders($headers), 'application/json' == $contentType ? json_decode($body, true) : $body);
    }

    protected function parseHeaders(string $headers)
    {
        $headers = preg_split('/\r\n/', $headers, null, PREG_SPLIT_NO_EMPTY);

        array_shift($headers);

        $headersArr = [];

        foreach ($headers as $line) {
            list($header, $value) = preg_split('/:\s/', $line, null, PREG_SPLIT_NO_EMPTY);

            $headersArr[strtoupper($header)] = trim($value);
        }

        return $headersArr;
    }
}
