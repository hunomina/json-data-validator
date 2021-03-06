# Json Data Validator

[![Build Status](https://travis-ci.com/hunomina/data-validator.svg?branch=master)](https://travis-ci.com/hunomina/data-validator)
[![codecov](https://codecov.io/gh/hunomina/data-validator/branch/master/graph/badge.svg)](https://codecov.io/gh/hunomina/data-validator)

__Description :__ Library for data validation based on data schemas

This project is licensed under the terms of the MIT license.

## Interfaces and classes

### [DataType](https://github.com/hunomina/json-data-validator/blob/master/src/Data/DataType.php)

Allows to encapsulate the data into an object and format it in order to be validated using a [DataSchema](https://github.com/hunomina/json-data-validator/blob/master/src/Schema/DataSchema.php).

### [JsonData](https://github.com/hunomina/json-data-validator/blob/master/src/Data/JsonData.php)

[JsonData](https://github.com/hunomina/json-data-validator/blob/master/src/Data/JsonData.php) implements [DataType](https://github.com/hunomina/json-data-validator/blob/master/src/Data/DataType.php).

`JsonData::format()` uses `json_decode()` to format json string into php array.

---

### [DataRule](https://github.com/hunomina/json-data-validator/blob/master/src/Rule/Rule.php)

Allows to validate [DataType]() based on rule specifications (is the data optional ?, is the data allowed to be...).

### [JsonRule](https://github.com/hunomina/json-data-validator/blob/master/src/Rule/JsonRule.php)

[JsonRule](https://github.com/hunomina/json-data-validator/blob/master/src/Rule/JsonRule.php) implement [DataRule](https://github.com/hunomina/json-data-validator/blob/master/src/Rule/Rule.php).

[JsonRule](https://github.com/hunomina/json-data-validator/blob/master/src/Rule/JsonRule.php) can validate :

 Type/Check | Null | Length | Pattern | Min/Max | Enum | Date format | Empty
:---------: | :----: | :-----: | :-----: | :--: | :---------: | :---: | :---: 
  String    | :white_check_mark: | :white_check_mark: | :white_check_mark: |         | :white_check_mark: | :white_check_mark: |:white_check_mark:
 Character  | :white_check_mark: |  | :white_check_mark: |         | :white_check_mark: |             |
  Number    | :white_check_mark: |        |         | :white_check_mark: | :white_check_mark: |  |
  Integer   | :white_check_mark: |        |         | :white_check_mark: | :white_check_mark: |             |
   Float    | :white_check_mark: |        |         | :white_check_mark: | :white_check_mark: |             |
  Boolean   |     |        |         |         |      |             |
 Typed List | :white_check_mark: | :white_check_mark: |  | :white_check_mark: |  |  |:white_check_mark:

---

### [DataSchema](https://github.com/hunomina/json-data-validator/blob/master/src/Schema/DataSchema.php)

[DataSchema](https://github.com/hunomina/json-data-validator/blob/master/src/Schema/DataSchema.php) is the library main class. It allows to validate [DataType](https://github.com/hunomina/json-data-validator/blob/master/src/Data/DataType.php) based on sub-schemas and [DataRule](https://github.com/hunomina/json-data-validator/blob/master/src/Rule/Rule.php).

`DataSchema::validate()` method allows this validation. If  `DataSchema::validate()` does not validate the [DataType](https://github.com/hunomina/json-data-validator/blob/master/src/Data/DataType.php) it throws an [InvalidDataException]().

### [JsonSchema](https://github.com/hunomina/json-data-validator/blob/master/src/Schema/JsonSchema.php)

[JsonSchema](https://github.com/hunomina/json-data-validator/blob/master/src/Schema/JsonSchema.php) implements [DataSchema](https://github.com/hunomina/json-data-validator/blob/master/src/Schema/DataSchema.php) and validates [JsonData](https://github.com/hunomina/json-data-validator/blob/master/src/Data/JsonData.php) using [JsonRule](https://github.com/hunomina/json-data-validator/blob/master/src/Rule/JsonRule.php).

## How it works

See [tests](https://github.com/hunomina/json-data-validator/tree/master/tests) for examples

A [JsonSchema](https://github.com/hunomina/json-data-validator/blob/master/src/Schema/JsonSchema.php) has a type : `object` or `list`.

Objects are composed of rules and "child" schemas if needed.

This is a schema definition :

```php
use hunomina\Validator\Json\Schema\Json\JsonSchema;

$schema = new JsonSchema([
    'success' => ['type' => 'bool'],
    'error' => ['type' => 'string', 'null' => true],
    'user' => ['type' => 'object', 'null' => true, 'optional' => true, 'schema' => [
        'name' => ['type' => 'string'],
        'age' => ['type' => 'int']
    ]]
]);
```

Schemas are just php arrays passe to `JsonSchema::setSchema()` method.

This schema is composed of 3 elements :
- a rule `success` which :
    - is a boolean
    - can not be null
    - is not optional
   
- a rule `error` which :
    - is a string
    - can be null
    - is not optional
    
- a "child" schema `user` which :
    - is an object and therefor is represented by a schema which contains 2 elements : a `name` (string) and an `age` (integer)
    - can be null
    - is optional
    

When a data unit is being validated using this schema by calling the `JsonSchema::validate()` method, the schema will check recursively if the data respects the rules and the "child" schemas.

If the data has :
- a boolean element `success`
- a null or string element `error`
- an optionally, null or object element `user` which must have :
    - a string element `name`
    - an integer element `age`
    

This data is valid :

```php
use hunomina\Validator\Json\Data\Json\JsonData;

$data = new JsonData([
    'success' => true,
    'error' => null,
    'user' => [
        'name' => 'test',
        'age' => 10
    ]
]);
```

This one is not :

```php
use hunomina\Validator\Json\Data\Json\JsonData;

$data = new JsonData([
    'success' => true,
    'error' => null,
    'user' => 'test'
]);
```

As said earlier, rules can be used to validate length or data pattern.

This schema uses the pattern validation on the `name` element and the length validation on the `geolocation` element :

```php
use hunomina\Validator\Json\Schema\Json\JsonSchema;

$schema = new JsonSchema([
    'name' => ['type' => 'string', 'pattern' => '/^[a-z]+$/'],
    'geolocation' => ['type' => 'integer-list', 'length' => 2]
]);
```

When calling the `JsonSchema::validate()` method, the schema will recursively check all the rule set and "child" schemas. If one rule or one "child" schema is invalid, `JsonSchema::validate()` returns `false`.

The "first level" schema is an `object` typed schema. It could be changed but is not meant to.

Finally, if a "child" schema is typed as an `object`, the schema will validate it as described above. If it's typed as a `list`, the schema will simply check each element of the data as an `object` type using the given "child" schema.