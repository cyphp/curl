<?php

namespace Cyphp\Curl\Test\Resource;

use PHPUnit\Framework\TestCase;
use Cyphp\Curl\Resource\CurlResource;

class CurlResourceTest extends TestCase
{
    public function testCurlConstruct()
    {
        $ch = new CurlResource();

        $this->assertTrue(is_resource($ch->getResource()));
    }

    public function testCurlClose()
    {
        $cr = new CurlResource();

        $cr->close();

        $this->assertFalse($cr->exists());
    }

    public function testCurlFailToSetInexistingOption()
    {
        $ch = new CurlResource();

        $fail = false;

        try {
            $ch->withOption(-10000, 'dummy value');
        } catch (\Exception $e) {
            $fail = true;
        }

        $this->assertEquals(null, $ch->getOption(-10000));
        $this->assertTrue($fail);
    }

    public function testCurlSetCustomRequestMethod()
    {
        $ch = new CurlResource();

        $ch->withMethod('POST');

        $this->assertTrue($ch->getOption(CURLOPT_POST));
        $this->assertEquals(null, $ch->getOption(CURLOPT_CUSTOMREQUEST));

        $ch->withMethod('PUT');

        $this->assertTrue($ch->getOption(CURLOPT_POST));
        $this->assertEquals('PUT', $ch->getOption(CURLOPT_CUSTOMREQUEST));

        $ch->withMethod('PATCH');

        $this->assertTrue($ch->getOption(CURLOPT_POST));
        $this->assertEquals('PATCH', $ch->getOption(CURLOPT_CUSTOMREQUEST));
    }
}
