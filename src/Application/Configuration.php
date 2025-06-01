<?php

namespace B24\Devtools\Application;

final class Configuration
{
    const DEFAULT_CONFIG = 'config.php';

    private array $registerControllerName = [
        'modern' => true
    ];

    public function __construct(
        private array $configiration = []
    ) {}

    public function setController(string $namespace, string $name): self
    {
        if (isset($this->registerControllerName[$name])) {
            throw new \Exception('Already exist namespace ' . $name . ' for controller');
        }

        $this->registerControllerName[$name] = true;

        $this->configiration['controllers']['namespaces'][$namespace] = $name;
        return $this;
    }

    public function get(): array
    {
        return array_merge_recursive(
            $this->configiration,
            require __DIR__ . '/../' . self::DEFAULT_CONFIG
        );
    }
}
