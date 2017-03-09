<?php

namespace Cyphp\Curl\Resource;

interface ResourceInterface
{
    public function exists();

    public function getResource();

    public function close();
}
