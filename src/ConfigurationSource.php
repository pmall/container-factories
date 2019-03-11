<?php declare(strict_types=1);

namespace Quanta\Container;

final class ConfigurationSource implements ConfigurationSourceInterface
{
    /**
     * The configuration entry to provide.
     *
     * @var \Quanta\Container\ConfigurationEntryInterface
     */
    private $entry;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\ConfigurationEntryInterface $entry
     */
    public function __construct(ConfigurationEntryInterface $entry)
    {
        $this->entry = $entry;
    }

    /**
     * @inheritdoc
     */
    public function entry(): ConfigurationEntryInterface
    {
        return $this->entry;
    }
}
