<?php declare(strict_types=1);

namespace Quanta\Container\Values;

interface ParsedValueInterface
{
    /**
     * Return whether the parsing succeeded or not.
     *
     * @return bool
     */
    public function success(): bool;

    /**
     * Return the result of the parsing.
     *
     * Should throw a LogicException when $this->success() returns false.
     *
     * @return \Quanta\Container\Values\ValueInterface
     * @throws \LogicException
     */
    public function value(): ValueInterface;
}
