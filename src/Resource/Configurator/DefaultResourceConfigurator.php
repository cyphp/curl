<?php

namespace Cyphp\Curl\Resource\Configurator;

use Cyphp\Curl\Resource\ResourceConfiguratorInterface;
use Cyphp\Curl\Resource\ResourceInterface;

class DefaultResourceConfigurator implements ResourceConfiguratorInterface
{
    public function __invoke(ResourceInterface $resource)
    {
        $resource
            ->withOption(CURLOPT_VERBOSE, false)
            ->withOption(CURLOPT_RETURNTRANSFER, true)
            ->withOption(CURLOPT_HEADER, true)
            ->withOption(CURLOPT_CONNECTTIMEOUT, 2)
            ->withOption(CURLOPT_TIMEOUT, 12)
            ->withOption(CURLOPT_SSL_VERIFYPEER, false)
            ->withOption(CURLOPT_SSL_VERIFYHOST, false)
            ->withOption(CURLOPT_FOLLOWLOCATION, true);
    }
}
