<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Maps\FactoryMapInterface;
use Quanta\Container\Passes\ExtensionPassInterface;
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
     * The processing pass to provide.
     *
     * @var \Quanta\Container\Passes\ProcessingPassInterface
     */
    private $processing;

    /**
     * The extension pass to provide.
     *
     * @var \Quanta\Container\Passes\ExtensionPassInterface
     */
    private $extension;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Maps\FactoryMapInterface        $map
     * @param \Quanta\Container\Passes\ProcessingPassInterface  $processing
     * @param \Quanta\Container\Passes\ExtensionPassInterface   $extension
     */
    public function __construct(
        FactoryMapInterface $map,
        ProcessingPassInterface $processing,
        ExtensionPassInterface $extension
    ) {
        $this->map = $map;
        $this->processing = $processing;
        $this->extension = $extension;
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
    public function processing(): ProcessingPassInterface
    {
        return $this->processing;
    }

    /**
     * Return the extension pass.
     *
     * @return \Quanta\Container\Passes\ExtensionPassInterface
     */
    public function extension(): ExtensionPassInterface
    {
        return $this->extension;
    }
}
