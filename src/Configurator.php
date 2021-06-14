<?php

declare(strict_types=1);

namespace Pollen\Config;

use Dflydev\DotAccessData\Data;
use League\Config\Configuration;
use Nette\Schema\Schema;
use Pollen\Support\Exception\ManagerRuntimeException;

class Configurator implements ConfiguratorInterface
{
    /**
     * @var static|null
     */
    private static $instance;

    /**
     * @var Data
     */
    protected $config;

    /**
     * @var Configuration
     */
    protected $strictConfig;

    /**
     * @var string[]
     */
    protected $strictConfigKeys = [];

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = new Data();
        $this->config->import($config, Data::REPLACE);

        $this->strictConfig = new Configuration();

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * @inheritDoc
     */
    public static function fetchFromLoader(ConfigLoaderInterface $loader): array
    {
        return $loader->load();
    }

    /**
     * Récupération de l'instance principale.
     *
     * @return static
     */
    public static function getInstance(): ConfiguratorInterface
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new ManagerRuntimeException(sprintf('Unavailable [%s] instance', __CLASS__));
    }

    /**
     * @param string $key
     * @param Schema $schema
     *
     * @return ConfiguratorInterface
     */
    public function addSchema(string $key, Schema $schema): ConfiguratorInterface
    {
        if (!in_array($key, $this->strictConfigKeys, true)) {
            $this->strictConfigKeys[] = $key;
        }

        $this->strictConfig->addSchema($key, $schema);

        if ($this->config->has($key)) {
            $this->strictConfig->set($key, $this->config->get($key));
            $this->config->remove($key);
        }

        return $this;
    }

    public function set($key, $value = null): ConfiguratorInterface
    {
        if (is_string($key)) {
            $key = [$key => $value];
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                if (in_array($k, $this->strictConfigKeys, true)) {
                    $this->strictConfig->set($k, $v);
                } else {
                    $this->config->set($k, $v);
                }
            }
        }

        return $this;
    }

    public function get(string $key, $default = null)
    {
        if ($this->strictConfig->exists($key)) {
            return $this->strictConfig->get($key);
        }

        return $this->config->get($key, $default);
    }

    public function has(string $key): bool
    {
        // @todo extraire la partie avant le .
        if (in_array($key, $this->strictConfigKeys, true)) {
            return $this->strictConfig->exists($key);
        }
        return $this->config->has($key);
    }
}