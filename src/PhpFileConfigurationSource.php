<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Values\ValueFactory;

final class PhpFileConfigurationSource implements ConfigurationSourceInterface
{
    /**
     * The value factory used to parse parameters.
     *
     * @var \Quanta\Container\Values\ValueFactory
     */
    private $factory;

    /**
     * The glob patterns used to collect php files.
     *
     * @var string[]
     */
    private $patterns;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Values\ValueFactory $factory
     * @param string                                ...$patterns
     */
    public function __construct(ValueFactory $factory, string ...$patterns)
    {
        $this->factory = $factory;
        $this->patterns = $patterns;
    }

    /**
     * @inheritdoc
     */
    public function entry(): ConfigurationEntryInterface
    {
        $entries = [];

        foreach ($this->patterns as $pattern) {
            foreach (glob($pattern) as $path) {
                $entries[] = new PhpFileConfigurationEntry($this->factory, $path);
            }
        }

        return new MergedConfigurationEntry(...$entries);
    }
}
