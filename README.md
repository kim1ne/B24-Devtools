# Библиотека инструментов для Bitrix24 PHP 8.1+

Этот пакет предоставляет инструменты для быстрой разработки в среде Bitrix24.

# Установка

Вы можете установить этот пакет с помощью Composer:
```php
composer require b24/devtools
```
# Подключение

Для использования инструментов необходимо подключить автозагрузчик Composer. Пример подключения:

local/php_interface/init.php
```php
require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
```

# Смарт-процессы

### Создание Смарт-процесса с чистого листа

```php
$dto = \B24\Devtools\Crm\Smart\SmartMapper::create(title: 'TEST', code: 'TEST', name: 'TEST'|null);
```
При успешном создании отдаст объект класса \B24\Devtools\Crm\Smart\SmartDto
```php
class SmartDto
{
    public readonly \B24\Devtools\Crm\Smart\SmartDynamic $smart;
    public readonly string $entityName;

    public function __construct(
        public readonly int $id,
        public readonly int $entityTypeId,
        public readonly string $code,
    ) {}
}
```

### Удаление Смарт-процесса
По символьному коду, либо по ENTITY_ID сущности
```php
\B24\Devtools\Crm\Smart\SmartMapper::deleteByCodeOrEntityId($code|$entityTypeId)
// Либо
\B24\Devtools\Crm\Smart\SmartMapper::deleteByCodeOrEntityIdIfExists($code|$entityTypeId)
```
По ID из таблицы b_crm_dynamic_type
```php
\B24\Devtools\Crm\Smart\SmartMapper::deleteById($id);
// Либо
\B24\Devtools\Crm\Smart\SmartMapper::deleteByIdIfExists($id)
```

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
\B24\Devtools\Crm\Smart\SmartProcess насследует \B24\Devtools\Crm\Smart\SmartDynamic
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

# CRUD над таблицей b_crm_entity_relation
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

# Работа с денежными полями

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

# Highload-блоки
Упрощённая работа с хайлод блокоми. Теперь хайлод блоки описываются как модели:

```php
use B24\Devtools\HighloadBlock\ActiveRecord;
use B24\Devtools\HighloadBlock\Fields\Enumeration;
use B24\Devtools\HighloadBlock\Fields\EnumValue;
use B24\Devtools\HighloadBlock\Fields\Field;

class ElementsCatalogHighload extends ActiveRecord
{
    public function getName(): string
    {
        return 'ElementsCatalog';
    }

    public function  getTableName(): string
    {
        return 'elements_catalog';
    }

    public function ruName(): string
    {
        return 'Элементы каталога';
    }

    public function enName(): string
    {
        return 'Elements catalog';
    }
    
    protected function getFields(string $entityId): array
    {
        return [
            new Field(
                entityId:  $entityId,
                fieldName: 'ENUMERATION',
                userTypeId: UserTypeEnum::ENUMERATION,
                multiple: true,
                enum: new Enumeration(
                    [
                        new EnumValue(
                            'Школа43',
                            'SCHOOL43'
                        ),
                    ],
                )
            ),
        ];
    }
}
```
Метод getFields - возвращает список полей Highload блока. По умолчанию битрикс создаёт столбец ID, его указывать не нужно.

в конструктор класса Field передаётся 3 обязательных параметра, остальные можно не заполнять
```php
class Field
{
    public function __construct(
        private string $entityId,
        private string $fieldName,
        private string|UserTypeEnum $userTypeId,
        private bool $multiple = false,
        private bool $mandatory = true,
        private array $editFormLabel = [],
        private array $listColumnLabel = [],
        private array $listFilterLabel = [],
        private array $errorMessage = [],
        private array $helpMessage = [],
        private ?array $settings = null,
        private ?Enumeration $enum = null
    ) {}
}
```
Под капотом этот класс превращается в массив:
```php
[
    'ENTITY_ID' => $entityId, // Название сущности
    'FIELD_NAME' => $fieldName, // Название поля. Можно задать без UF_ - он проставится автоматически
    'USER_TYPE_ID' => $userTypeId, // Тип пользовательского поля, можно найти в UserTypeEnum описание всех полей для хайлод блока
    'MULTIPLE' => $multiple, // Множественное поле
    'MANDATORY' => $mandatory, // Обязательность заполнения
    'EDIT_FORM_LABEL' => $editFormLabel ?? $fieldName, // массив языковых сообщений вида array("ru"=>"привет", "en"=>"hello")
    'LIST_COLUMN_LABEL' => $listColumnLabel ?? $fieldName,
    'LIST_FILTER_LABEL' => $listFilterLabel ?? $fieldName,
    'ERROR_MESSAGE' => $errorMessage ?? $fieldName,
    'SETTINGS' => $settings, // массив с настройками свойства зависимыми от типа свойства. Проходят "очистку" через обработчик типа PrepareSettings.
    'ENUM' => $enum,
]
```
### Создание и Удаление хайлод блока

```php
$hl = ElementsCatalogHighload();
$hl->createHL(); // Создание
$hl->dropHL(); // Удаление
```

### События для хайлод блока

в init.php:
```php
ElementsCatalogHighload::events()
    ->onAdd(ElementsCatalogEvent::class)
    ->onAfterAdd(ElementsCatalogEvent::class)
    ->onBeforeAdd(ElementsCatalogEvent::class)
    ->onDelete(ElementsCatalogEvent::class)
    ->onAfterDelete(ElementsCatalogEvent::class)
    ->onBeforeDelete(ElementsCatalogEvent::class)
    ->onUpdate(ElementsCatalogEvent::class)
    ->onAfterUpdate(ElementsCatalogEvent::class)
    ->onBeforeUpdate(ElementsCatalogEvent::class)
```
Обработчик события:
```php
use B24\Devtools\HighloadBlock\Operation\EventTrait;
use Bitrix\Main\ORM\Event;

class ElementsCatalogEvent
{
    use EventTrait;

    public static function onBeforeAdd(Event $event)
    {
        self::setError($event, 'Ошибка_1');
    }
}
```
для событий был создан трейт B24\Devtools\HighloadBlock\Operation\EventTrait для удобной записи ошибки.

Если событие называется onBeforeAdd, то и метод будет статический и называться onBeforeAdd.

### Миграции полей хайлод-блоков

У класса, который насследует B24\Devtools\HighloadBlock\ActiveRecord, есть обязательный метод getFields, который описывает поля хайлод блока, метод migrate будет отслеживать этот список

```php
$hl = ElementsCatalogHighload();
$hl->migrate();
```

Если удалилось какое то поле из метода getFields(), он будет отслежен и удалён из базы.
Так же метод отслеживает изменения списка в enum:

```php
    protected function getFields(string $entityId): array
    {
        return [
            new Field(
                entityId:  $entityId,
                fieldName: 'ENUMERATION',
                userTypeId: UserTypeEnum::ENUMERATION,
                multiple: true,
                enum: new Enumeration(
                    [
                        new EnumValue(
                            value: 'Школа43',
                            xmlId: 'SCHOOL43',
                            def: false, // поле поумолчанию?
                            sort: 500
                        ),
                    ],
                )
            ),
        ];
    }
```

Если в enum будет добавлен ещё один EnumValue, то он добавится так же в список БД, уникальность определяется по xmlId.
Если удалить из списка какой нибудь EnumValue, он так же удалится из базы
Если у EnumValue изменится либо value, либо def - значение списка обновится

### Удобная вставка/обновление записи в highload-блоке

Запись:
```php
$hl = new ElementsCatalogHighload();
$transfer = $hl->getTransfer();
$result = $transfer
    ->set('NAME', 'Имя')
    ->setEnumByXmlId('ENUMERATION', 'SCHOOL43')
    ->saveExistingFile('FILE', 'log.txt') // Все пути будут складываться от $_SERVER['DOCUMENT_ROOT'] . '/'
    ->setDateTime('DATE', new \Bitrix\Main\Type\DateTime()) // второй аргумент такой уже поумолчанию
    ->setBoolean('BOOLEAN', true)
    ->save();
```
Обновить запись:
```php
$hl = new ElementsCatalogHighload();
$transfer = $hl->getTransfer(id: 1);
$result = $transfer
    ->set('NAME', 'Имя')
    ->save();
```

во всех методах set можно вначале не указывать у названия поля UF_ - он проставится автоматически если его нет.
Так же нет необходимости запоминать какое-то множественное поле чтобы вместо строки задать массив строк, это уже умеет делать Хелпер, он смотрит описанные поля в методе getFields и если $multiple = true, он считает это поле множественным:
```php
$hl = new ElementsCatalogHighload();
$transfer = $hl->getTransfer();
$transfer
    ->setEnumByXmlId('ENUMERATION', 'SCHOOL43')
    ->setEnumByXmlId('ENUMERATION', 'SCHOOL44')
```
Под капотом в UF_ENUMERATION будет записан массив из двух значений.