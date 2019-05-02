<?php

namespace hunomina\Validator\Json\Rule;

interface Rule
{
    /**
     * @param $data
     * @return bool
     * Validate a data based on his type and length (if possible)
     */
    public function validate($data): bool;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param string $type
     * @return Rule
     */
    public function setType(string $type): self;

    /**
     * @return bool
     */
    public function canBeNull(): bool;

    /**
     * @param bool $null
     * @return Rule
     */
    public function setNull(bool $null): self;

    /**
     * @return bool
     */
    public function isOptional(): bool;

    /**
     * @param bool $isOptional
     * @return Rule
     */
    public function setOptional(bool $isOptional): self;

    /**
     * @return null|int
     * `null` if if length does have to be checked
     */
    public function getLength(): ?int;

    /**
     * @param int $length
     * @return Rule
     * `null` if if length does have to be checked
     */
    public function setLength(?int $length): self;
}