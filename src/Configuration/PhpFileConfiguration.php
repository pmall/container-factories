<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\Values\ValueFactory;

final class PhpFileConfiguration implements ConfigurationInterface
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
    public function unit(): ConfigurationUnitInterface
    {
        $parsed = [];

        foreach ($this->patterns as $pattern) {
            foreach (glob($pattern) as $path) {
                $configuration = require $path;

                if (! is_array($configuration)) {
                    throw new \UnexpectedValueException(
                        vsprintf('PHP configuration file must return an array, %s returned (%s)', [
                            gettype($configuration),
                            realpath($path),
                        ])
                    );
                }

                $parsed[] = new ArrayConfigurationUnit(
                    $this->factory,
                    $configuration,
                    (string) realpath($path)
                );
            }
        }

        return new MergedConfigurationUnit(...$parsed);
    }
}
