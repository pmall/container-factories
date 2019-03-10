<?php declare(strict_types=1);

namespace Quanta\Container\Maps;

final class EmptyFactoryMap implements FactoryMapInterface
{
    /**
     * @inheritdoc
     */
    public function factories(): array
    {
        return [];
    }
}
