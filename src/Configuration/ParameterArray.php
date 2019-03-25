<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\Utils;
use Quanta\Container\FactoryMap;
use Quanta\Container\Values\ValueFactory;
use Quanta\Container\Factories\Factory;

final class ParameterArray implements ConfigurationSourceInterface
{
    /**
     * The value factory used to parse parameters.
     *
     * @var \Quanta\Container\Values\ValueFactory
     */
    private $factory;

    /**
     * The array of parameters to provide.
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
    public function configuration(): ConfigurationInterface
    {
        return new Configuration(
            new FactoryMap(
                Utils::factories($this->factory, $this->parameters)
            )
        );
    }
}
