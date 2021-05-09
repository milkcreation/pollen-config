<?php

declare(strict_types=1);

namespace Pollen\Config;

use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Concerns\ParamsBagDelegateTrait;
use Pollen\Support\Exception\ManagerRuntimeException;
use Pollen\Support\Proxy\ContainerProxy;
use Psr\Container\ContainerInterface as Container;
use Pollen\Support\ClassLoader;
use Pollen\Support\Env;
use Pollen\Support\Filesystem as fs;

class Config implements ConfigInterface
{
    use BootableTrait;
    use ContainerProxy;
    use ParamsBagDelegateTrait;

    /**
     * Instance principale.
     * @var static|null
     */
    private static $instance;

    /**
     * Chemin absolu vers le repertoire de stockage des fichiers de configuration.
     * @var string
     */
    protected $dir;

    /**
     * @param string $dir
     * @param Container|null $container
     */
    public function __construct(string $dir, ?Container $container= null)
    {
        $this->dir = fs::normalizePath($dir);
        if ($container !== null) {
            $this->setContainer($container);
        }

        $this->boot();

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * Récupération de l'instance principale.
     *
     * @return static
     */
    public static function getInstance(): ConfigInterface
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new ManagerRuntimeException(sprintf('Unavailable [%s] instance', __CLASS__));
    }

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        if ($this->isBooted()) {
            if (is_dir($this->dir)) {
                $autoload =  $this->dir . fs::DS . 'autoload.php';
                if (file_exists($autoload)) {
                    $loads = file_get_contents($autoload);
                    $classLoader = new ClassLoader();

                    foreach ($loads as $type => $namespaces) {
                        foreach ($namespaces as $namespace => $path) {
                            $classLoader->load($namespace, $path, $type);
                        }
                    }
                }

                $params = [];
                foreach (glob($this->dir . fs::DS . '*.php') as $filename) {
                    $key = basename($filename, ".php");
                    if ($key === 'autoload') {
                        continue;
                    }
                    $params[$key] = file_get_contents($filename);
                }
                $this->set($params);
                $this->parse();
            }

            $this->setBooted();
        }
    }

    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return [
            'app_url' => Env::get('APP_URL')
        ];
    }
}