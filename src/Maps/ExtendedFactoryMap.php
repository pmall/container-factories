<?php declare(strict_types=1);

namespace Quanta\Container\Maps;

use Quanta\Container\Factories\Extension;

final class ExtendedFactoryMap extends AbstractFactoryMapCollection
{
    /**
     * Constructor.
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
        $factory = array_shift($factories);

        return array_reduce($factories, [$this, 'extension'], $factory);
    }

    /**
     * Return an extension from the given callables.
     *
     * @param callable $factory
     * @param callable $extension
     * @return \Quanta\Container\Factories\Extension
     */
    private function extension(callable $factory, callable $extension): Extension
    {
        return new Extension($factory, $extension);
    }
}
