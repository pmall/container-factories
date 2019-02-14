<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

final class Configuration implements ConfigurationInterface
{
    /**
     * The configuration entries to return.
     *
     * @var \Quanta\Container\Configuration\ConfigurationEntryInterface[]
     */
    private $entries;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Configuration\ConfigurationEntryInterface ...$entries
     */
    public function __construct(ConfigurationEntryInterface ...$entries)
    {
        $this->entries = $entries;
    }

    /**
     * @inheritdoc
     */
    public function entries(): array
    {
        return $this->entries;
    }
}
