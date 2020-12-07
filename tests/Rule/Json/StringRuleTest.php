<?php

namespace hunomina\DataValidator\Test\Rule\Json;

use hunomina\DataValidator\Data\Json\JsonData;
use hunomina\DataValidator\Exception\Json\InvalidDataException;
use hunomina\DataValidator\Rule\Json\JsonRule;
use hunomina\DataValidator\Schema\Json\JsonSchema;
use PHPUnit\Framework\TestCase;

class StringRuleTest extends TestCase
{
    /**
     * @dataProvider getTestableData
     * @param JsonData $data
     * @param JsonSchema $schema
     * @param bool $success
     * @throws InvalidDataException
     */
    public function testStringType(JsonData $data, JsonSchema $schema, bool $success): void
    {
        if (!$success) {
            $this->expectException(InvalidDataException::class);
            $this->expectExceptionCode(InvalidDataException::INVALID_DATA_TYPE);

            $schema->validate($data);
        } else {
            self::assertTrue($schema->validate($data));
        }
    }

    /**
     * @return array
     * @throws InvalidDataException
     */
    public function getTestableData(): array
    {
        return [
            self::ValidStringData(),
            self::InvalidStringData()
        ];
    }

    /**
     * @return array
     * @throws InvalidDataException
     */
    private static function ValidStringData(): array
    {
        return [
            new JsonData([
                'string' => 'hello'
            ]),
            new JsonSchema([
                'string' => ['type' => JsonRule::STRING_TYPE]
            ]),
            true
        ];
    }

    /**
     * @return array
     * @throws InvalidDataException
     */
    private static function InvalidStringData(): array
    {
        return [
            new JsonData([
                'string' => false
            ]),
            new JsonSchema([
                'string' => ['type' => JsonRule::STRING_TYPE]
            ]),
            false
        ];
    }
}