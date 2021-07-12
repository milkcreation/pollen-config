<?php

declare(strict_types=1);

namespace Pollen\Config;

use Pollen\Config\Exception\UnableLoadConfigException;
use SplFileInfo;
use Symfony\Component\Yaml\Parser;
use Throwable;

class YamlConfigLoader implements ConfigLoaderInterface
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
            $data = (new Parser())->parseFile($this->finfo->getRealPath());
        } catch (Throwable $e) {
            throw new UnableLoadConfigException(
                sprintf('Unable log config from [%s], Yaml parsing fail.', $this->finfo->getRealPath())
            );
        }

        return  [$this->key => $data];
    }
}