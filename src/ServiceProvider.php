<?php declare(strict_types=1);

namespace Quanta\Container;

use Interop\Container\ServiceProviderInterface;

final class ServiceProvider implements ServiceProviderInterface
{
    /**
     * The parameters.
     *
     * @param array
     */
    private $parameters;

    /**
     * The aliases.
     *
     * @param string[]
     */
    private $aliases;

    /**
     * The factories.
     *
     * @param callable[]
     */
    private $factories;

    /**
     * The extensions.
     *
     * @param callable[]
     */
    private $extensions;

    /**
     * Constructor.
     *
     * @param array         $parameters
     * @param string[]      $aliases
     * @param callable[]    $factories
     * @param callable[]    $extensions
     */
    public function __construct(array $parameters, array $aliases, array $factories, array $extensions)
    {
        $this->parameters = $parameters;
        $this->aliases = $aliases;
        $this->factories = $factories;
        $this->extensions = $extensions;
    }

    /**
     * @inheritdoc
     */
    public function getFactories()
    {
        $map = new MergedFactoryMap(...[
            new ParametersFactoryMap($this->parameters),
            new AliasesFactoryMap(array_filter($this->aliases, 'is_string')),
            new FactoryMap(array_filter($this->factories, 'is_callable')),
        ]);

        return $map->factories();
    }

    /**
     * @inheritdoc
     */
    public function getExtensions()
    {
        return array_filter($this->extensions, 'is_callable');
    }
}
