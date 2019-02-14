<?php declare(strict_types=1);

namespace Quanta\Container\Maps;

final class MergedFactoryMap extends AbstractFactoryMapCollection
{
    /**
     * Constructor.
     *
     *
     * @param \Quanta\Container\Maps\FactoryMapInterface ...$maps
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
