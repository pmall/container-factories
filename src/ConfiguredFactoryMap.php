<?php declare(strict_types=1);

namespace Quanta\Container;

final class ConfiguredFactoryMap implements FactoryMapInterface
{
    /**
     * The configuration source.
     *
     * @var \Quanta\Container\ConfigurationSourceInterface
     */
    private $source;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\ConfigurationSourceInterface $source
     */
    public function __construct(ConfigurationSourceInterface $source)
    {
        $this->source = $source;
    }

    /**
     * @inheritdoc
     */
    public function factories(): array
    {
        $configuration = $this->source->configuration();

        return array_reduce(
            $configuration->passes(),
            [$this, 'reduced'],
            $configuration->map()->factories()
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
