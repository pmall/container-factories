<?php declare(strict_types=1);

namespace Quanta\Container;

final class Configuration implements ConfigurationInterface
{
    /**
     * The processed factory map to provide.
     *
     * @var \Quanta\Container\ConfigurationEntry
     */
    private $entry;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\ConfigurationEntry $entry
     */
    public function __construct(ConfigurationEntry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * @inheritdoc
     */
    public function entry(): ConfigurationEntry
    {
        return $this->entry;
    }
}
