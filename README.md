# Библиотека инструментов для Bitrix24 PHP 8.1+

* [Установка](https://github.com/kim1ne/B24-Devtools?tab=readme-ov-file#Установка)
* [Подключение](https://github.com/kim1ne/B24-Devtools?tab=readme-ov-file#Подключение)
* [Смарт-процессы](https://github.com/kim1ne/B24-Devtools?tab=readme-ov-file#Смарт-процессы)
   * [Создание смарт-процесса с чистого листа](https://github.com/kim1ne/B24-Devtools?tab=readme-ov-file#Создание-смарт-процесса-с-чистого-листа)
   * [Удаление смарт-процесса](https://github.com/kim1ne/B24-Devtools?tab=readme-ov-file#Удаление-смарт-процесса)
   * [Подмена сервис контейнера](https://github.com/kim1ne/B24-Devtools?tab=readme-ov-file#Подмена-сервис-контейнера)
   * [Пример фабрики](https://github.com/kim1ne/B24-Devtools?tab=readme-ov-file#Пример-фабрики)
   * [Обработчик события на Добавление элемента](https://github.com/kim1ne/B24-Devtools?tab=readme-ov-file#Обработчик-события-на-Добавление-элемента)
   * [Работа с сущностью смарт-процесса](https://github.com/kim1ne/B24-Devtools?tab=readme-ov-file#Работа-с-сущностью-смарт-процесса)
* [CRUD над таблицей b_crm_entity_relation](https://github.com/kim1ne/B24-Devtools?tab=readme-ov-file#CRUD-над-таблицей-b_crm_entity_relation)
* [Работа с денежными полями](https://github.com/kim1ne/B24-Devtools?tab=readme-ov-file#Работа-с-денежными-полями)
* [Highload-блоки](https://github.com/kim1ne/B24-Devtools?tab=readme-ov-file#Highload-блоки)
   * [Создание и Удаление хайлод блока](https://github.com/kim1ne/B24-Devtools?tab=readme-ov-file#Создание-и-Удаление-хайлод-блока)
   * [Миграции полей хайлод-блоков](https://github.com/kim1ne/B24-Devtools?tab=readme-ov-file#Миграции-полей-хайлод-блоков)
   * [События для хайлод блока](https://github.com/kim1ne/B24-Devtools?tab=readme-ov-file#События-для-хайлод-блока)
   * [Удобная вставка/обновление записи в highload-блоке](https://github.com/kim1ne/B24-Devtools?tab=readme-ov-file#Удобная-вставкаобновление-записи-в-highload-блоке)
* [StepProcessing (Пошаговая обработка)](https://github.com/kim1ne/B24-Devtools?tab=readme-ov-file#stepprocessing-%D0%BF%D0%BE%D1%88%D0%B0%D0%B3%D0%BE%D0%B2%D0%B0%D1%8F-%D0%BE%D0%B1%D1%80%D0%B0%D0%B1%D0%BE%D1%82%D0%BA%D0%B0)
  * [Генерация объекта](https://github.com/kim1ne/B24-Devtools?tab=readme-ov-file#%D0%B3%D0%B5%D0%BD%D0%B5%D1%80%D0%B0%D1%86%D0%B8%D1%8F-%D0%BE%D0%B1%D1%8A%D0%B5%D0%BA%D1%82%D0%B0)
  * [Динамическая подгрузка очереди](https://github.com/kim1ne/B24-Devtools?tab=readme-ov-file#%D0%B4%D0%B8%D0%BD%D0%B0%D0%BC%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%B0%D1%8F-%D0%BF%D0%BE%D0%B4%D0%B3%D1%80%D1%83%D0%B7%D0%BA%D0%B0-%D0%BE%D1%87%D0%B5%D1%80%D0%B5%D0%B4%D0%B8)
  * [Экшен Контроллера для Пошаговой обработки](https://github.com/kim1ne/B24-Devtools?tab=readme-ov-file#%D1%8D%D0%BA%D1%88%D0%B5%D0%BD-%D0%BA%D0%BE%D0%BD%D1%82%D1%80%D0%BE%D0%BB%D0%BB%D0%B5%D1%80%D0%B0-%D0%B4%D0%BB%D1%8F-%D0%BF%D0%BE%D1%88%D0%B0%D0%B3%D0%BE%D0%B2%D0%BE%D0%B9-%D0%BE%D0%B1%D1%80%D0%B0%D0%B1%D0%BE%D1%82%D0%BA%D0%B8)
  * [Пошаговая запись в Excel-файл](https://github.com/kim1ne/B24-Devtools?tab=readme-ov-file#%D0%BF%D0%BE%D1%88%D0%B0%D0%B3%D0%BE%D0%B2%D0%B0%D1%8F-%D0%B7%D0%B0%D0%BF%D0%B8%D1%81%D1%8C-%D0%B2-excel-%D1%84%D0%B0%D0%B9%D0%BB)

# Установка
```php
composer require b24/devtools
```
# Подключение

Для использования инструментов необходимо подключить автозагрузчик Composer. Пример подключения:

```php
local/php_interface/init.php
```
```php
require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
```

# Смарт-процессы

### Создание смарт-процесса с чистого листа

```php
$mapper = \B24\Devtools\Crm\Smart\Mapper::create(title: 'TEST', code: 'TEST', name: 'TEST'|null);
```
При успешном создании отдаст объект класса \B24\Devtools\Crm\Smart\Mapper
```php
class Mapper
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

### Удаление смарт-процесса
По символьному коду, либо по ENTITY_ID сущности

```php
\B24\Devtools\Crm\Smart\Mapper::deleteByCodeOrEntityId($code|$entityTypeId)
// Либо
\B24\Devtools\Crm\Smart\Mapper::deleteByCodeOrEntityIdIfExists($code|$entityTypeId)
```
По ID из таблицы b_crm_dynamic_type

```php
\B24\Devtools\Crm\Smart\Mapper::deleteById($id);
// Либо
\B24\Devtools\Crm\Smart\Mapper::deleteByIdIfExists($id)
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

# StepProcessing (Пошаговая обработка)
Документация Bitrix: https://dev.1c-bitrix.ru/api_d7/bitrix/ui/stepprocessing/examples.php

### Генерация объекта
```php
use B24\Devtools\Process\UI\Fillers\Buttons;
use B24\Devtools\Process\UI\Fillers\Handler;
use B24\Devtools\Process\UI\Fillers\Messages;
use B24\Devtools\Process\UI\Fillers\Queue;
use B24\Devtools\Process\UI\Step\Result;
use B24\Devtools\Process\UI\StepProcessing;

$process = new StepProcessing(
    id: 'uniq_id',
    controller: 'module:name.controllers.Process', // только контроллеры модуля
    messages: new Messages(
        DialogTitle: "Title",
        DialogSummary: "Description",
        DialogStartButton: "StartButton",
        DialogStopButton: "StopButton",
        DialogCloseButton: "CloseButton",
        RequestCanceling: "Canceling...",
        RequestCanceled: "Canceled",
        RequestCompleted: "Completed",
        DialogExportDownloadButton: "ExportDownloadButton",
        DialogExportClearButton: "ExportClearButton",
    )
);

$process->setButtons(new Buttons(start: true, close: true, stop: true));

$process->setQueue(
    new Queue(action: 'run', title: 'Page generation 1', params: ['page' => 1]),
    new Queue(action: 'run', title: 'Page generation 2', params: ['page' => 2]),
    new Queue(action: 'run', title: 'Page generation 3', params: ['page' => 3]),
    new Queue(action: 'run', title: 'Page generation 4', params: ['page' => 4]),
);

$process->setHandlers(
    new Handler(
        callbackType: Handler::StateChanged,
        body: '
        function (state, result) {
            if (state !== "' . Result::COMPLETED . '") {
                return;
            }
            
            console.log(result);
        }
        '
    )
);

$process->initJS();
```
```html
<div
    id="processing"
    class="ui-btn ui-btn-light-border"
    onclick="<?=$process->showDialog() ?>"
>
    Process
</div>
```
![img4](https://github.com/user-attachments/assets/c4450134-83b4-4d61-a525-3734c86c9461)

### Динамическая подгрузка очереди
```js
<script>
    let process = <?=$process->toJsObject() ?>.setQueue([...queue])
</script>
```

### Экшен Контроллера для Пошаговой обработки
```php
use B24\Devtools\Process\UI\Step\Result;

public function runAction(): array
{
    $page = $this->request->get('page') ?? 1;
    return (new Result(
        status: true,
        processedItems: $page,
        totalItems: $totalPages
    ))->toArray();
}
```
![img](https://github.com/user-attachments/assets/ed936434-24e5-486b-a9c9-78be428830ad)


### Пошаговая запись в Excel-файл
```php
use B24\Devtools\Excel\IteratorManager;

$header = ['ID', 'Name', 'Last Name'];
$limit = 2;
$filePath = '/upload/file.xlsx';

$rows = [
    ['1', 'John', 'Hovewer'],
    ['2', 'Freken', 'Bock'],
];

// Первая итерация
$manager = new IteratorManager(
    filePath: $filePath,
    page: 1,
    limit: $limit
);

$manager->setHeaderRow($header)
    ->setRows($rows)
    ->saveFile();

// Вторая итерация
$manager = new \B24\Devtools\Excel\IteratorManager(
    filePath: $filePath,
    page: 2,
    limit: $limit
);

$manager->setRows($rows)
    ->saveFile();
```
![img5](https://github.com/user-attachments/assets/8c29e0c2-5596-4112-8bc1-72cb305c3171)
