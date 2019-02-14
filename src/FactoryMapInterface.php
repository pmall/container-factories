<?php declare(strict_types=1);

namespace Quanta\Container;

interface FactoryMapInterface
{
    /**
     * Return an array of container factories.
     *
     * @return callable[]
     */
    public function factories(): array;
}
