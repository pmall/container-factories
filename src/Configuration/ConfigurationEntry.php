<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\FactoryMapInterface;
use Quanta\Container\Configuration\Passes\ProcessingPassInterface;

final class ConfigurationEntry
{
    /**
     * The factory map.
     *
     * @var \Quanta\Container\FactoryMapInterface
     */
    private $map;

    /**
     * The processing pass.
     *
     * @var \Quanta\Container\Configuration\Passes\ProcessingPassInterface
     */
    private $pass;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\FactoryMapInterface                             $map
     * @param \Quanta\Container\Configuration\Passes\ProcessingPassInterface    $pass
     */
    public function __construct(FactoryMapInterface $map, ProcessingPassInterface $pass)
    {
        $this->map = $map;
        $this->pass = $pass;
    }

    /**
     * Return the factory map.
     *
     * @return \Quanta\Container\FactoryMapInterface
     */
    public function map(): FactoryMapInterface
    {
        return $this->map;
    }

    /**
     * Return the processing pass.
     *
     * @return \Quanta\Container\Configuration\Passes\ProcessingPassInterface
     */
    public function pass(): ProcessingPassInterface
    {
        return $this->pass;
    }
}
