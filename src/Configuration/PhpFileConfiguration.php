<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\Parsing\ParserInterface;

final class PhpFileConfiguration implements ConfigurationInterface
{
    /**
     * The parser used to produce factories from parameters.
     *
     * @var \Quanta\Container\Parsing\ParserInterface
     */
    private $parser;

    /**
     * The glob patterns used to collect php files.
     *
     * @var string[]
     */
    private $patterns;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Parsing\ParserInterface $parser
     * @param string                                    ...$patterns
     */
    public function __construct(ParserInterface $parser, string ...$patterns)
    {
        $this->parser = $parser;
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
                    $this->parser,
                    $configuration,
                    (string) realpath($path)
                );
            }
        }

        return new MergedConfigurationUnit(...$parsed);
    }
}
