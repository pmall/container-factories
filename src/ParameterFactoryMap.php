<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Values\ValueFactory;
use Quanta\Container\Factories\Factory;

final class ParameterFactoryMap implements FactoryMapInterface
{
    /**
     * The value factory used to parse parameters.
     *
     * @var \Quanta\Container\Values\ValueFactory
     */
    private $factory;

    /**
     * The array of parameters.
     *
     * @var array
     */
    private $parameters;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Values\ValueFactory $factory
     * @param array                                 $parameters
     */
    public function __construct(ValueFactory $factory, array $parameters)
    {
        $this->factory = $factory;
        $this->parameters = $parameters;
    }

    /**
     * @inheritdoc
     */
    public function factories(): array
    {
        return array_map([$this, 'factory'], $this->parameters);
    }

    /**
     * Return a factory from the given parameter parsed with the value factory.
     *
     * @param mixed $parameter
     * @return \Quanta\Container\Factories\Factory;
     */
    private function factory($parameter): Factory
    {
        return new Factory(($this->factory)($parameter));
    }
}
