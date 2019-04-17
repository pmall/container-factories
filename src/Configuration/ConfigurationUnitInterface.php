<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\ProcessingPassInterface;

interface ConfigurationUnitInterface
{
    /**
     * Return an associative array of factories.
     *
     * @return callable[]
     */
    public function factories(): array;

    /**
     * Return a processing pass.
     *
     * @return \Quanta\Container\ProcessingPassInterface
     */
    public function pass(): ProcessingPassInterface;
}
