<?php

namespace B24\Devtools\Application;

use Bitrix\Main\Config;
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

final class Application
{
    const MODULE_NAME = 'b24.devtools';

    private ?Config\Configuration $configurationModule = null;

    public function __construct(
        private Configuration $configuration = new Configuration()
    ) {}

    public function run(): void
    {
        $this->includeModule();
        $this->registerConfig();
    }

    public static function isInclude(): bool
    {
        return Loader::includeModule(self::MODULE_NAME);
    }

    private function getConfiguration(): Config\Configuration
    {
        if ($this->configurationModule === null) {
            $this->configurationModule = Config\Configuration::getInstance(self::MODULE_NAME);
        }

        return $this->configurationModule;
    }

    private function includeModule(): void
    {
        $this->addInPropertyClass(Loader::class, 'loadedModules', self::MODULE_NAME, true);

        $this->addInPropertyClass(ModuleManager::class, 'installedModules', self::MODULE_NAME, [
            'ID' => self::MODULE_NAME
        ]);
    }

    private function addInPropertyClass(string $className, string $property, string $key, mixed $data): void
    {
        $reflectionProperty = new \ReflectionProperty($className, $property);

        $propData = $reflectionProperty->getValue();

        $propData[$key] = $data;
        $reflectionProperty->setValue($propData);
    }

    private function registerConfig(): void
    {
        $config = $this->getConfiguration();

        foreach ($this->configuration->get() as $k => $v) {
            $config[$k] = $v;
        }
    }
}
