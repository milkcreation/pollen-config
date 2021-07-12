<?php

declare(strict_types=1);

namespace Pollen\Config;

use SplFileInfo;

class PhpConfigLoader implements ConfigLoaderInterface
{
    /**
     * @var string
     */
    protected string $key;

    /**
     * @var SplFileInfo
     */
    protected SplFileInfo $finfo;

    /**
     * @param string $key
     * @param SplFileInfo $finfo
     */
    public function __construct(string $key, SplFileInfo $finfo)
    {
        $this->key = $key;
        $this->finfo = $finfo;
    }

    /**
     * @inheritDoc
     */
    public function load(): array
    {
        return [$this->key => include $this->finfo->getRealPath()];
    }
}