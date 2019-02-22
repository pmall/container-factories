<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\FactoryMap;
use Quanta\Container\FactoryMapInterface;

final class ConfiguredFactoryMap implements FactoryMapInterface
{
    /**
     * The configuration.
     *
     * @var \Quanta\Container\Configuration\ConfigurationInterface
     */
    private $configuration;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Configuration\ConfigurationInterface $configuration
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
        return $this->configuration
            ->step(new IdentityStep)
            ->map(new FactoryMap([]))
            ->factories();
    }
}
