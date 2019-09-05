<?php

namespace hunomina\Validator\Json\Test;

use hunomina\Validator\Json\Data\JsonData;
use hunomina\Validator\Json\Exception\InvalidDataException;
use hunomina\Validator\Json\Exception\InvalidDataTypeException;
use hunomina\Validator\Json\Exception\InvalidSchemaException;
use hunomina\Validator\Json\Rule\JsonRule;
use hunomina\Validator\Json\Schema\JsonSchema;
use PHPUnit\Framework\TestCase;

class ListTypeTest extends TestCase
{
    /**
     * @throws InvalidDataException
     * @throws InvalidDataTypeException
     * @throws InvalidSchemaException
     */
    public function testValidData(): void
    {
        $data = new JsonData();
        $data->setDataFromArray([
            'users' => [
                ['id' => 0, 'name' => 'test0'],
                ['id' => 1, 'name' => 'test1']
            ]
        ]);

        $this->assertTrue($this->getSchema()->validate($data));
    }

    /**
     * @throws InvalidDataException
     * @throws InvalidDataTypeException
     * @throws InvalidSchemaException
     */
    public function testExceptionThrownOnObjectPassedAsAList(): void
    {
        $data = new JsonData();
        $data->setDataFromArray([
            'users' => [
                'id' => 0, 'name' => 'test0'
            ]
        ]);

        $this->assertFalse($this->getSchema()->validate($data));
    }

    /**
     * @return JsonSchema
     * @throws InvalidSchemaException
     */
    private function getSchema(): JsonSchema
    {
        $schema = new JsonSchema();
        $schema->setSchema([
            'users' => ['type' => JsonRule::LIST_TYPE, 'schema' => [
                'id' => ['type' => JsonRule::INTEGER_TYPE],
                'name' => ['type' => JsonRule::STRING_TYPE]
            ]]
        ]);

        return $schema;
    }
}