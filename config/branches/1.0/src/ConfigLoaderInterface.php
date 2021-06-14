<?php

declare(strict_types=1);

namespace Pollen\Config;

interface ConfigLoaderInterface
{
    /**
     * @return array
     */
    public function load(): array;
}