<?php

namespace hunomina\DataValidator\Test\Schema\Json;

use hunomina\DataValidator\Exception\Json\InvalidSchemaException;
use hunomina\DataValidator\Rule\Json\BooleanRule;
use hunomina\DataValidator\Rule\Json\JsonRule;
use hunomina\DataValidator\Rule\Json\StringRule;
use hunomina\DataValidator\Schema\Json\JsonSchema;
use PHPUnit\Framework\TestCase;
use Throwable;

class TwoLevelJsonSchemaWithListTest extends TestCase
{
    public function testChildListSchema(): void
    {
        $schema = new JsonSchema([
            'boolean' => ['type' => JsonRule::BOOLEAN_TYPE],
            'list' => ['type' => JsonRule::LIST_TYPE, 'schema' => [
                'boolean' => ['type' => JsonRule::BOOLEAN_TYPE, 'optional' => true],
                'string' => ['type' => JsonRule::STRING_TYPE, 'null' => true]
            ]]
        ]);

        self::assertCount(1, $schema->getRules());
        self::assertCount(1, $schema->getChildren());

        self::assertArrayHasKey('boolean', $schema->getRules());
        self::assertInstanceOf(BooleanRule::class, $schema->getRules()['boolean']);

        self::assertArrayHasKey('list', $schema->getChildren());
        $listChild = $schema->getChildren()['list'];
        self::assertEquals(JsonRule::LIST_TYPE, $listChild->getType());

        self::assertCount(2, $listChild->getRules());
        self::assertCount(0, $listChild->getChildren());

        self::assertArrayHasKey('boolean', $listChild->getRules());
        self::assertInstanceOf(BooleanRule::class, $listChild->getRules()['boolean']);
        self::assertTrue($listChild->getRules()['boolean']->isOptional());

        self::assertArrayHasKey('string', $listChild->getRules());
        /** @var StringRule $stringRule */
        $stringRule = $listChild->getRules()['string'];
        self::assertInstanceOf(StringRule::class, $stringRule);
        self::assertTrue($stringRule->canBeNull());
    }

    public function testThrowWithListFieldWithoutSchema(): void
    {
        try {
            new JsonSchema([
                'boolean' => ['type' => JsonRule::BOOLEAN_TYPE],
                'list' => ['type' => JsonRule::LIST_TYPE]
            ]);
        } catch (Throwable $t){
            self::assertInstanceOf(InvalidSchemaException::class, $t);
            self::assertEquals(InvalidSchemaException::INVALID_CHILD_SCHEMA, $t->getCode());

            $t = $t->getPrevious();
            self::assertInstanceOf(InvalidSchemaException::class, $t);
            self::assertEquals(InvalidSchemaException::MISSING_CHILD_SCHEMA, $t->getCode());
        }
    }
}