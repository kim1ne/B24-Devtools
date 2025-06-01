# Библиотека инструментов для Bitrix24 PHP 8.1+

* [Установка](https://github.com/kim1ne/B24-Devtools?tab=readme-ov-file#Установка)
* [Подключение](https://github.com/kim1ne/B24-Devtools?tab=readme-ov-file#Подключение)
* [Регистрация библиотеки как Модуль в системе Bitrix](https://github.com/kim1ne/B24-Devtools/tree/main?tab=readme-ov-file#регистрация-библиотеки-как-модуль-в-системе-bitrix)
* [Смарт-процессы](https://github.com/kim1ne/B24-Devtools?tab=readme-ov-file#Смарт-процессы)
   * [Подмена сервис контейнера](https://github.com/kim1ne/B24-Devtools?tab=readme-ov-file#Подмена-сервис-контейнера)
   * [Пример фабрики](https://github.com/kim1ne/B24-Devtools?tab=readme-ov-file#Пример-фабрики)
   * [Обработчик события на Добавление элемента](https://github.com/kim1ne/B24-Devtools?tab=readme-ov-file#Обработчик-события-на-Добавление-элемента)
* [Работа с денежными полями](https://github.com/kim1ne/B24-Devtools?tab=readme-ov-file#Работа-с-денежными-полями)

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

# Регистрация библиотеки как Модуль в системе Bitrix
С помощью этого можно регистрировать свои контроллеры не создавая модуль. 
В init.php добавить:
```php
new \B24\Devtools\Application\Application(
    new \B24\Devtools\Application\Configuration([
        'controllers' => [
            'namespaces' => [
                '\\Some\\Namespace' => 'custom'
            ]
        ]
    ])
);

// ИЛИ
new \B24\Devtools\Application\Application(
    (new \B24\Devtools\Application\Configuration())
        ->setController('\\Some\\Namespace', 'custom')
        ->setController(...)
);

\CModule::IncludeModule('b24.devtools'); // Вернёт true
```
Из js будет доступна отправка запроса в контроллер:
```js
BX.ajax.runAction('b24:devtools.custom.ControllerName.actionName')
```
[Контроллеры в битрикс](https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=6436&LESSON_PATH=3913.3516.5062.3750.6436&ysclid=m3bybd65q2512401672)

# Смарт-процессы

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

class AddHandler extends Operation\Action
{
    public function process(\Bitrix\Crm\Item $item): Result
    {
        $result = new Result();
        $result->addError(new \Bitrix\Main\Error('Ошибка 1'))
        $result->addError(new \Bitrix\Main\Error('Ошибка 2'))
        return $result;
    }
}
```
![image](https://github.com/kim1ne/B24-Devtools/assets/111231185/ab98b075-780c-40d9-89f1-bb310c08b61e)

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

# Пользовательские поля

### UserFieldService
```php
$service = \B24\Devtools\UserField\UserFieldService::getInstance();

 /**
 * @var \B24\Devtools\UserField\UserField $field 
 */
$field = $service->getField($entityId, $fieldName);
$field = $service->getFieldByHlBlockId($hlBlockId, $fieldName);
$field = $service->getFieldByEntityTypeId($entityTypeId, $fieldName);
$field = $service->getFieldByHlBlockName($hlBlockName, $fieldName);
$field = $service->getFieldBySmartProcessCode($smartProcessCode, $fieldName);
$field = $service->getFieldBySmartProcessName($smartProcessName, $fieldName);
```

### UserField

```php
use B24\Devtools\UserField\UserFieldService;

$service = UserFieldService::getInstance();
$field = $service->getField($entityId, $fieldName);

$field->entityId;
$field->fieldCode;
$field->id;
$field->isMandatory;
$field->isMultiple;
$field->settings;
$field->userTypeId;
$field->xmlId;
$field->getEnums();
$field->getLang();
$field->isBooleanType();
$field->isEnumType();
$field->isFileType();
// .....
```

### EnumCollection

```php
use B24\Devtools\UserField\UserFieldService;

$service = UserFieldService::getInstance();
$field = $service->getField($entityId, $fieldName);
$enumsCollection = $field->getEnums();
$enums = $enumsCollection->get();
$enum = $enumsCollection->findByValue($value);
$enum = $enumsCollection->findByXmlId($xmlId);
$enumDefault = $enumsCollection->findDefault();
```

### Enum
```php
use B24\Devtools\UserField\Enum;
use B24\Devtools\UserField\UserFieldService;

$service = UserFieldService::getInstance();
$field = $service->getField($entityId, $fieldName);
$enum = $field->getEnums()->findDefault();
$enum->xmlId;
$enum->id;
$enum->isDefault;
$enum->userFieldId;
$enum->value;

$enum = Enum::get($entityId, $fieldName, $xmlId);
$enum = Enum::getByEntityTypeId($entityTypeId, $fieldName, $xmlId);
$enum = Enum::getByHlBlockId($hlBlockId, $fieldName, $xmlId);
$enum = Enum::getByHlBlockName($hlBlockName, $fieldName, $xmlId);
$enum = Enum::getBySmartProcessCode($smartProcessCode, $fieldName, $xmlId);
$enum = Enum::getBySmartProcessName($smartProcessName, $fieldName, $xmlId);
```
