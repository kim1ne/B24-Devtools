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

### Пример фабрики
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
### Обработчик события на Добавление элемента
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
![image](https://github.com/kim1ne/B24-Devtools/assets/111231185/ab98b075-780c-40d9-89f1-bb310c08b61e)

### Работа с сущностью смарт-процесса
```php
$entityTypeId = \B24\Devtools\Crm\Smart\SmartProcess::getIdByCode('TEST');

$smart = new \B24\Devtools\Crm\Smart\SmartProcess($entityTypeId);
// Либо так: $smart = new \B24\Devtools\Crm\Smart\SmartProcess('TEST');

$smart->getFactory(); // Вернёт фабрику смарт-процесса
$smart->getFactoryId(); // ID смарт-процесса
$smart->getEntityName(); // Название объекта смарт-процесса, CRM_2 (например)
$smart->compileClass(); // Отдаст неймспейс класса ORM смарт-процесса
$smart->getContainer(); // Отсюда же можно вытащить сервис контейнер
$smart->getRelationManager(); // RelationManager
```

### CRUD над таблицей b_crm_entity_relation
```php
// Чтение связей //

$children = \B24\Devtools\Crm\Relation\Manager::searchChildren(\CCrmOwnerType::Quote, 1)
    ->getAll(); // Массив из ItemIdentifier всех привязанных детей к Предложению

$children = \B24\Devtools\Crm\Relation\Manager::searchChildren(\CCrmOwnerType::Quote, 1)
    ->withEntityTypeId(152); // Массив ID всех привязанных детей-элементов смартпроцесса с ID 152

$children = \B24\Devtools\Crm\Relation\Manager::searchChildren(\CCrmOwnerType::Quote, 1)
    ->withOne(function (\Bitrix\Crm\ItemIdentifier $identifier) {
        return $identifier; // Вернёт массив из ItemIdentifier
        return $identifier->getEntityId(); // Вернёт массив из ID ItemIdentifier
    });

// Если заменить метод searchChildren на searchParents, то будут искаться родители //
```
```php
//  Обновление связей //
\B24\Devtools\Crm\Relation\Manager::update(\CCrmOwnerType::Quote, 1, 152, 1)
    ->isParent() // Например если в связи надо отвязать родителя (Предложения) и привязать к другому Предложению
    ->on(\CCrmOwnerType::Quote, 2) // Привязываем к Предложению с ID = 2
    ->replace(); // Замена
```
```php
// Удаление какой-то одной связи //
\B24\Devtools\Crm\Relation\Manager::deleteOne(\CCrmOwnerType::Quote, 1, 152, 1);

// У Предложения с ID = 1 удаляем все связи со смарт-процессом, у которого ID = 152
\B24\Devtools\Crm\Relation\Manager::deleteWithType(\CCrmOwnerType::Quote, 1, 152);
```
```php
// Создание связей

// Создаст у Предложения с ID = 1 связь (ребёнка) со смарт-процессом с ID = 152 // 
\B24\Devtools\Crm\Relation\Manager::create(\CCrmOwnerType::Quote, 1, 152, 1);
```

## Работа с денежными полями

```php
$moneyField = '155|USD';
$rateUsdToRub = 93.22;

// либо $money = new \B24\Devtools\Data\MoneyField(155, 'USD');
$money = \B24\Devtools\Data\MoneyField::parse($moneyField)
    ->math(function (&$price) use ($rateUsdToRub) {
        $price = $price * $rateUsdToRub;
    })
    ->setCurrency('RUB')
    ->round(2);

echo (string) $money; // 14449.1|RUB
```

## Кэширование битрикс в стиле ООП
```
$cache = new \B24\Devtools\Cache\CacheFile();
$cache->setTtl(3600);
$cache->write('some_kind_of_key', [
    'name' => 'cache'
]);

$cache->getData('some_kind_of_key');
```
### Кэш маппер
```php
// Когда истечёт ttl кеша, он вызовет function, достанет оттуда значение и запишет заново в кеш
$mapper = new \B24\Devtools\Cache\CacheMapper();
$mapper->ttl = 3600;
$mapper->get('some_kind_of_key', function () {
    return [
        'name' => 'cache'
    ];
});
```
