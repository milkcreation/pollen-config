<?php

declare(strict_types=1);

namespace Pollen\Config;

use Pollen\Support\Concerns\BootableTraitInterface;
use Pollen\Support\Concerns\ParamsBagDelegateTraitInterface;
use Pollen\Support\Proxy\ContainerProxyInterface;

interface ConfigInterface extends BootableTraitInterface, ContainerProxyInterface, ParamsBagDelegateTraitInterface
{
    /**
     * Chargement.
     *
     * @return void
     */
    public function boot(): void;
}