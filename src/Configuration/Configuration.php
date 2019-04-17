<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

final class Configuration implements ConfigurationInterface
{
    /**
     * The configuration unit.
     *
     * @var \Quanta\Container\Configuration\ConfigurationUnitInterface
     */
    private $unit;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Configuration\ConfigurationUnitInterface $unit
     */
    public function __construct(ConfigurationUnitInterface $unit)
    {
        $this->unit = $unit;
    }

    /**
     * @inheritdoc
     */
    public function unit(): ConfigurationUnitInterface
    {
        return $this->unit;
    }
}
