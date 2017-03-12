<?php

namespace Cyphp\Curl\Resource;

interface ResourceAwareInterface
{
    public function getResource();

    public function withResource(ResourceInterface $cr = null);

    public function configureResource(callable $configurator);
}
