<?php

namespace Cyphp\Curl\Test;

use Cyphp\Curl\Client;
use Cyphp\Curl\Http\Request;
use Cyphp\Curl\Resource\CurlResource;
use Cyphp\Curl\Resource\ResourceInterface;
use Cyphp\Curl\Resource\Configurator\DefaultResourceConfigurator;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testCreateClient()
    {
        $client = new Client();

        $this->assertNull($client->getResource());

        $client->withResource(new CurlResource());

        $this->assertNotNull($client->getResource());

        $this->assertTrue($client->getResource()->exists());
    }

    public function testClientConfigureResource()
    {
        $client = new Client(new CurlResource());

        // the callback is basicly default resource configurator
        $client->configureResource(function (ResourceInterface $resource) {
            $resource
            ->withOption(CURLOPT_VERBOSE, false)
            ->withOption(CURLOPT_RETURNTRANSFER, true)
            ->withOption(CURLOPT_HEADER, true)
            ->withOption(CURLOPT_CONNECTTIMEOUT, 2)
            ->withOption(CURLOPT_TIMEOUT, 12)
            ->withOption(CURLOPT_SSL_VERIFYPEER, false)
            ->withOption(CURLOPT_SSL_VERIFYHOST, false)
            ->withOption(CURLOPT_FOLLOWLOCATION, true);
        });

        $this->assertEquals(12, $client->getResource()->getOption(CURLOPT_TIMEOUT));
        $this->assertTrue($client->getResource()->getOption(CURLOPT_FOLLOWLOCATION));
    }

    public function testClientSendGetRequest()
    {
        $client = new Client(new CurlResource());

        $client->configureResource(new DefaultResourceConfigurator());

        $request = new Request('GET', 'https://status.github.com/api/status.json', [
            'User-Agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:52.0) Gecko/20100101 Firefox/52.0',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        ]);

        $response = $client->send($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testClientWithBaseUrl()
    {
        $client = new Client(new CurlResource(), [
            'base_url' => 'https://status.github.com/',
        ]);

        $client->configureResource(new DefaultResourceConfigurator());

        $request = new Request('GET', '/api/status.json', [
            'User-Agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:52.0) Gecko/20100101 Firefox/52.0',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        ]);

        $response = $client->send($request);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
