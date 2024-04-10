# Библиотека инструментов для Bitrix24

Этот пакет предоставляет инструменты для быстрой разработки в среде Bitrix24.

## Установка

Вы можете установить этот пакет с помощью Composer:
```php
composer require b24/devtools
```
## Подключение

Для использования инструментов необходимо подключить автозагрузчик Composer. Пример подключения:

local/php_interface/init.php
```php
require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
```

## Смарт-процессы

### Подмена сервис контейнера

Пример использования:
```php
use Module\Helpers\Crm\Replacement\Container;

new Container([
    'TEST' => FactoryTest::class
]);
```
Аргументом в конструктор передаётся массив, где ключом CODE смарт-процесса, значением неймспейс его фабрики. Позволяет вызывать события ДО и ПОСЛЕ на элементе смарт-процесса. 

### Фабрика

Пример фабрики:
```php
use Bitrix\Crm\Item;
use Bitrix\Crm\Service;
use Bitrix\Crm\Service\Context;
use Bitrix\Crm\Service\Operation;

class FactoryTest extends Service\Factory\Dynamic
{
    public function getAddOperation(Item $item, Context $context = null): Operation\Add
    {
        $operation = parent::getAddOperation($item, $context);

        $operation->addAction(
            Operation::ACTION_BEFORE_SAVE,
            new AddHandler()
        );

        return $operation;
    }
}
```
### Обработчик события на Добавление элемента:
```php
use Bitrix\Crm\Service\Operation;
use Bitrix\Main\Result;
use B24\Devtools\Crm\ResultOperationTrait;

class AddHandler extends Operation\Action
{
    use ResultOperationTrait;

    public function process(\Bitrix\Crm\Item $item): Result
    {
        return $this
            ->error('Ошибка 1')
            ->error('Ошибка 2')
            ->result();
    }
}
```
![image](https://github.com/kim1ne/B24-Devtools/assets/111231185/68cf35e1-6cd1-457e-b561-f2f7a90aa96f)

