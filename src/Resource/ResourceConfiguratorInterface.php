<?php

namespace Cyphp\Curl\Resource;

interface ResourceConfiguratorInterface
{
    public function __invoke(ResourceInterface $resource);
}
