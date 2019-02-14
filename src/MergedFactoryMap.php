<?php declare(strict_types=1);

namespace Quanta\Container;

final class MergedFactoryMap extends AbstractFactoryMapCollection
{
    /**
     * Constructor.
     *
     *
     * @param \Quanta\Container\FactoryMapInterface ...$maps
     */
    public function __construct(FactoryMapInterface ...$maps)
    {
        parent::__construct(...$maps);
    }

    /**
     * @inheritdoc
     */
    protected function factory(array $factories): callable
    {
        return array_pop($factories);
    }
}
