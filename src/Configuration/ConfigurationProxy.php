<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

final class ConfigurationProxy implements ConfigurationInterface
{
    /**
     * The configuration source to proxy.
     *
     * @var \Quanta\Container\Configuration\ConfigurationSourceInterface
     */
    private $source;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Configuration\ConfigurationSourceInterface $source
     */
    public function __construct(ConfigurationSourceInterface $source)
    {
        $this->source = $source;
    }

    /**
     * @inheritdoc
     */
    public function step(ConfigurationStepInterface $step): ConfigurationStepInterface
    {
        return $this->source->configuration()->step($step);
    }
}
