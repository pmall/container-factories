<?php declare(strict_types=1);

namespace Quanta\Container\Parsing;

use Quanta\Container\FactoryInterface;

interface ParsedFactoryInterface
{
    /**
     * Return whether the parsing succeeded or not.
     *
     * @return bool
     */
    public function success(): bool;

    /**
     * Return the parsed factory.
     *
     * @return \Quanta\Container\FactoryInterface
     */
    public function factory(): FactoryInterface;
}
