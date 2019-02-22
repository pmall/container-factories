<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\FactoryMapInterface;

final class IdentityStep implements ConfigurationStepInterface
{
    /**
     * @inheritdoc
     */
    public function map(FactoryMapInterface $map): FactoryMapInterface
    {
        return $map;
    }
}
