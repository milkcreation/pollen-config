<?php

declare(strict_types=1);

namespace Pollen\Config;

use Pollen\Config\Exception\UnableLoadConfigException;
use SplFileInfo;

class FilesystemConfigLoader implements ConfigLoaderInterface
{
    /**
     * @var string[]
     */
    protected array $extensions = ['json', 'php', 'yml'];

    /**
     * @var string
     */
    protected string $path;

    /**
     * @param string $path
     * @param array|string[] $extensions
     */
    public function __construct(string $path, ?array $extensions = null)
    {
        $this->path = $path;

        if ($extensions !== null) {
            $this->extensions = $extensions;
        }
    }

    public function load(): array
    {
        $config = [];

        if (!file_exists($this->path)) {
            throw new UnableLoadConfigException(sprintf('Unable log config from [%s], path is missing.', $this->path));
        }

        $pattern =  is_dir($this->path) ? $this->path . '/*' : $this->path;

        foreach (glob($pattern) as $filename) {
            $finfo = new SplFileInfo($filename);

            $key = $finfo->getBasename('.'. $finfo->getExtension());

            if (!in_array($finfo->getExtension(), $this->extensions, true)) {
                continue;
            }

            switch($finfo->getExtension()) {
                case 'json':
                    $config = array_merge($config, (new JsonConfigLoader($key, $finfo))->load());
                    break;
                case 'php':
                    $config = array_merge($config, (new PhpConfigLoader($key, $finfo))->load());
                    break;
                case 'yml' :
                    $config = array_merge($config, (new YamlConfigLoader($key, $finfo))->load());
                    break;
            }
        }

        return $config;
    }
}