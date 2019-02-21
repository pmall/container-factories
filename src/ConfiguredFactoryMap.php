<?php declare(strict_types=1);

namespace Quanta\Container;

final class ConfiguredFactoryMap implements FactoryMapInterface
{
    /**
     * The configuration.
     *
     * @var \Quanta\Container\ConfigurationInterface
     */
    private $configuration;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @inheritdoc
     */
    public function factories(): array
    {
        return array_reduce(
            $this->configuration->passes(),
            [$this, 'reduced'],
            $this->configuration->map()->factories()
        );
    }

    /**
     * Return the given associative array of factories processed with the given
     * configuration pass.
     *
     * @param callable[]                                    $factories
     * @param \Quanta\Container\ConfigurationPassInterface  $pass
     * @return callable[]
     */
    private function reduced(array $factories, ConfigurationPassInterface $pass): array
    {
        return $pass->processed($factories);
    }
}
