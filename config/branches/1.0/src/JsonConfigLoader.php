<?php

declare(strict_types=1);

namespace Pollen\Config;

use Pollen\Config\Exception\UnableLoadConfigException;
use SplFileInfo;
use Throwable;

class JsonConfigLoader implements ConfigLoaderInterface
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
        try {
            $data = json_decode(file_get_contents($this->finfo->getRealPath()), true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $e) {
            throw new UnableLoadConfigException(
                sprintf('Unable log config from [%s], json decoding fail.', $this->finfo->getRealPath())
            );
        }

        return  [$this->key => $data];
    }
}