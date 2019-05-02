<?php

namespace hunomina\Validator\Json\Test;

use hunomina\Validator\Json\Data\JsonData;
use hunomina\Validator\Json\Exception\InvalidDataException;
use hunomina\Validator\Json\Exception\InvalidDataTypeException;
use hunomina\Validator\Json\Exception\InvalidSchemaException;
use hunomina\Validator\Json\Schema\DataSchema;
use hunomina\Validator\Json\Schema\JsonSchema;
use PHPUnit\Framework\TestCase;

class SecondLevelJsonValidatorWithListTest extends TestCase
{
    /**
     * @dataProvider getSamples
     * @param string $data
     * @param JsonSchema $schema
     * @param bool $shouldWork
     * @throws InvalidDataException
     * @throws InvalidDataTypeException
     */
    public function testValidation(string $data, JsonSchema $schema, bool $shouldWork): void
    {
        $jsonData = (new JsonData())->setData($data);

        if ($shouldWork) {
            $this->assertTrue($schema->validate($jsonData));
        } else {
            $this->assertFalse($schema->validate($jsonData));
        }
    }

    /**
     * @return array
     * @throws InvalidSchemaException
     */
    public function getSamples(): array
    {
        return [
            self::checkWithPerfectData(),
            self::checkWithoutAge(),
            self::checkWithEmptyUserList(),
            self::checkThatShouldWorkWithoutUserList(),
            self::checkThatShouldNotWorkWithoutUserList()
        ];
    }

    /**
     * @return array
     * @throws InvalidSchemaException
     */
    private static function checkWithPerfectData(): array
    {
        $data = json_encode([
            'success' => true,
            'error' => null,
            'users' => [
                ['name' => 'test', 'age' => 10, 'gender' => 'male'],
                ['name' => 'test2', 'age' => 12]
            ]
        ]);

        return [$data, self::getSecondLevelSchema(), true];
    }

    /**
     * @return array
     * @throws InvalidSchemaException
     */
    private static function checkWithoutAge(): array
    {
        $data = json_encode([
            'success' => true,
            'error' => null,
            'users' => [
                ['name' => 'test'],
                ['name' => 'test2']
            ]
        ]);

        return [$data, self::getSecondLevelSchema(), false];
    }

    /**
     * @return array
     * @throws InvalidSchemaException
     */
    private static function checkWithEmptyUserList(): array
    {
        $data = json_encode([
            'success' => true,
            'error' => null,
            'users' => []
        ]);

        return [$data, self::getSecondLevelSchema(), true];
    }

    /**
     * @return array
     * @throws InvalidSchemaException
     */
    private static function checkThatShouldNotWorkWithoutUserList(): array
    {
        $data = json_encode([
            'success' => true,
            'error' => null,
            'users' => null
        ]);

        return [$data, self::getSecondLevelSchema(), false];
    }

    /**
     * @return array
     * @throws InvalidSchemaException
     */
    private static function checkThatShouldWorkWithoutUserList(): array
    {
        $data = json_encode([
            'success' => true,
            'error' => null,
            'users' => null
        ]);

        return [$data, self::getSecondLevelSchemaWithNullableList(), true];
    }

    /**
     * @return DataSchema
     * @throws InvalidSchemaException
     */
    private static function getSecondLevelSchema(): DataSchema
    {
        $schema = [
            'success' => ['type' => 'bool'],
            'error' => ['type' => 'string', 'null' => true],
            'users' => ['type' => 'list', 'optional' => true, 'schema' => [
                'name' => ['type' => 'string'],
                'age' => ['type' => 'int'],
                'gender' => ['type' => 'string', 'optional' => true]
            ]]
        ];

        return (new JsonSchema())->setSchema($schema);
    }

    /**
     * @return DataSchema
     * @throws InvalidSchemaException
     */
    private static function getSecondLevelSchemaWithNullableList(): DataSchema
    {
        $schema = [
            'success' => ['type' => 'bool'],
            'error' => ['type' => 'string', 'null' => true],
            'users' => ['type' => 'list', 'optional' => true, 'null' => true, 'schema' => [
                'name' => ['type' => 'string'],
                'age' => ['type' => 'int'],
                'gender' => ['type' => 'string', 'optional' => true]
            ]]
        ];

        return (new JsonSchema())->setSchema($schema);
    }
}