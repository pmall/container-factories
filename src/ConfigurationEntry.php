<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Maps\FactoryMapInterface;
use Quanta\Container\Passes\ProcessingPassInterface;

final class ConfigurationEntry
{
    /**
     * The factory map.
     *
     * @var \Quanta\Container\Maps\FactoryMapInterface
     */
    private $map;

    /**
     * The processing pass.
     *
     * @var \Quanta\Container\Passes\ProcessingPassInterface
     */
    private $pass;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Maps\FactoryMapInterface        $map
     * @param \Quanta\Container\Passes\ProcessingPassInterface  $pass
     */
    public function __construct(FactoryMapInterface $map, ProcessingPassInterface $pass)
    {
        $this->map = $map;
        $this->pass = $pass;
    }

    /**
     * Return the factory map.
     *
     * @return \Quanta\Container\Maps\FactoryMapInterface
     */
    public function map(): FactoryMapInterface
    {
        return $this->map;
    }

    /**
     * Return the processing pass.
     *
     * @return \Quanta\Container\Passes\ProcessingPassInterface
     */
    public function pass(): ProcessingPassInterface
    {
        return $this->pass;
    }
}
