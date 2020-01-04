<?php

namespace hunomina\Validator\Json\Test\Rule\Json;

use hunomina\Validator\Json\Data\Json\JsonData;
use hunomina\Validator\Json\Exception\Json\InvalidDataException;
use hunomina\Validator\Json\Exception\InvalidDataTypeException;
use hunomina\Validator\Json\Exception\Json\InvalidSchemaException;
use hunomina\Validator\Json\Rule\Json\JsonRule;
use hunomina\Validator\Json\Schema\Json\JsonSchema;
use PHPUnit\Framework\TestCase;
use Throwable;

class TypedListRuleTest extends TestCase
{
    /**
     * @dataProvider getTestableData
     * @param JsonData $data
     * @param JsonSchema $schema
     * @param bool $success
     * @throws InvalidDataException
     * @throws InvalidDataTypeException
     */
    public function testTypedListType(JsonData $data, JsonSchema $schema, bool $success): void
    {
        if (!$success) {
            try {
                $schema->validate($data);
            } catch (Throwable $t) {
                // exception thrown by the schema
                $this->assertInstanceOf(InvalidDataException::class, $t);
                $this->assertEquals(InvalidDataException::INVALID_TYPED_LIST_ELEMENT, $t->getCode());

                // exception thrown by the typed list rule
                $t = $t->getPrevious();
                $this->assertInstanceOf(InvalidDataException::class, $t);
                $this->assertEquals(InvalidDataException::INVALID_TYPED_LIST_ELEMENT, $t->getCode());

                // exception thrown by the invalid list element (scalar type)
                $t = $t->getPrevious();
                $this->assertInstanceOf(InvalidDataException::class, $t);
                $this->assertEquals(InvalidDataException::INVALID_DATA_TYPE, $t->getCode());
            }
        } else {
            $this->assertTrue($schema->validate($data));
        }
    }

    /**
     * @return array
     * @throws InvalidDataException
     * @throws InvalidSchemaException
     */
    public function getTestableData(): array
    {
        return [
            self::ValidIntegerList(),
            self::InvalidIntegerList(),
            self::ValidStringList(),
            self::InvalidStringList(),
            self::ValidCharacterList(),
            self::InvalidCharacterList(),
            self::ValidBooleanList(),
            self::InvalidBooleanList(),
            self::ValidFloatList(),
            self::InvalidFloatList(),
            self::ValidNumericList(),
            self::InvalidNumericList()
        ];
    }

    /**
     * @throws InvalidDataException
     * @throws InvalidSchemaException
     */
    private static function ValidIntegerList(): array
    {
        return [
            new JsonData([
                'integers' => [1, 2, 3, 4]
            ]),
            new JsonSchema([
                'integers' => ['type' => JsonRule::INTEGER_LIST_TYPE]
            ]),
            true
        ];
    }

    /**
     * @throws InvalidDataException
     * @throws InvalidSchemaException
     */
    private static function InvalidIntegerList(): array
    {
        return [
            new JsonData([
                'integers' => [1, 2.89, 3.14158, 4.0]
            ]),
            new JsonSchema([
                'integers' => ['type' => JsonRule::INTEGER_LIST_TYPE]
            ]),
            false
        ];
    }

    /**
     * @throws InvalidDataException
     * @throws InvalidSchemaException
     */
    private static function ValidStringList(): array
    {
        return [
            new JsonData([
                'strings' => ['I', 'am', 'testing']
            ]),
            new JsonSchema([
                'strings' => ['type' => JsonRule::STRING_LIST_TYPE]
            ]),
            true
        ];
    }

    /**
     * @throws InvalidDataException
     * @throws InvalidSchemaException
     */
    private static function InvalidStringList(): array
    {
        return [
            new JsonData([
                'strings' => ['I', 'am', 'testing', 'for', 'the', 2, 'nd', 'time']
            ]),
            new JsonSchema([
                'strings' => ['type' => JsonRule::STRING_LIST_TYPE]
            ]),
            false
        ];
    }

    /**
     * @throws InvalidDataException
     * @throws InvalidSchemaException
     */
    private static function ValidCharacterList(): array
    {
        return [
            new JsonData([
                'characters' => ['a', 'b', 'c', 'd']
            ]),
            new JsonSchema([
                'characters' => ['type' => JsonRule::CHAR_LIST_TYPE]
            ]),
            true
        ];
    }

    /**
     * @throws InvalidDataException
     * @throws InvalidSchemaException
     */
    private static function InvalidCharacterList(): array
    {
        return [
            new JsonData([
                'characters' => ['a', 'bc', 'd', 'e']
            ]),
            new JsonSchema([
                'characters' => ['type' => JsonRule::CHAR_LIST_TYPE]
            ]),
            false
        ];
    }

    /**
     * @throws InvalidDataException
     * @throws InvalidSchemaException
     */
    private static function ValidBooleanList(): array
    {
        return [
            new JsonData([
                'booleans' => [true, false, false, true]
            ]),
            new JsonSchema([
                'booleans' => ['type' => JsonRule::BOOLEAN_LIST_TYPE]
            ]),
            true
        ];
    }

    /**
     * @throws InvalidDataException
     * @throws InvalidSchemaException
     */
    private static function InvalidBooleanList(): array
    {
        return [
            new JsonData([
                'booleans' => [true, false, 0, true]
            ]),
            new JsonSchema([
                'booleans' => ['type' => JsonRule::BOOLEAN_LIST_TYPE]
            ]),
            false
        ];
    }

    /**
     * @throws InvalidDataException
     * @throws InvalidSchemaException
     */
    private static function ValidFloatList(): array
    {
        return [
            new JsonData([
                'users' => [1.1, 2.2, 3.3, 4.4]
            ]),
            new JsonSchema([
                'users' => ['type' => JsonRule::FLOAT_LIST_TYPE]
            ]),
            true
        ];
    }

    /**
     * @throws InvalidDataException
     * @throws InvalidSchemaException
     */
    private static function InvalidFloatList(): array
    {
        return [
            new JsonData([
                'users' => [1, 2.2, 3.3, 4.4]
            ]),
            new JsonSchema([
                'users' => ['type' => JsonRule::FLOAT_LIST_TYPE]
            ]),
            false
        ];
    }

    /**
     * @throws InvalidDataException
     * @throws InvalidSchemaException
     */
    private static function ValidNumericList(): array
    {
        return [
            new JsonData([
                'users' => [1, 2.89, 3.14158, 4.0]
            ]),
            new JsonSchema([
                'users' => ['type' => JsonRule::NUMERIC_LIST_TYPE]
            ]),
            true
        ];
    }

    /**
     * @throws InvalidDataException
     * @throws InvalidSchemaException
     */
    private static function InvalidNumericList(): array
    {
        return [
            new JsonData([
                'users' => ['a', 2.89, 3.14158, 4.0]
            ]),
            new JsonSchema([
                'users' => ['type' => JsonRule::NUMERIC_LIST_TYPE]
            ]),
            false
        ];
    }

    /**
     * @throws InvalidDataException
     * @throws InvalidDataTypeException
     * @throws InvalidSchemaException
     */
    public function testInvalidDataList(): void
    {
        $this->expectException(InvalidDataException::class);
        $this->expectExceptionCode(InvalidDataException::INVALID_DATA_TYPE);

        $data = new JsonData([
            'users' => 'not-a-list'
        ]);
        $schema = new JsonSchema([
            'users' => ['type' => JsonRule::NUMERIC_LIST_TYPE]
        ]);

        $schema->validate($data);
    }
}