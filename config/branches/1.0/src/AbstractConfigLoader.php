<?php

declare(strict_types=1);

namespace Pollen\Config;

abstract class AbstractConfigLoader implements ConfigLoaderInterface
{
    /**
     * List of configuration parameters.
     * @var array
     */
    protected array $config = [];
}