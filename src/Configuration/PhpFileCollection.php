<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\ValueParser;

final class PhpFileCollection implements ConfigurationSourceInterface
{
    /**
     * The parser used to produce factories from parameters.
     *
     * @var \Quanta\Container\ValueParser
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
     * @param \Quanta\Container\ValueParser $parser
     * @param string                        ...$patterns
     */
    public function __construct(ValueParser $parser, string ...$patterns)
    {
        $this->parser = $parser;
        $this->patterns = $patterns;
    }

    /**
     * @inheritdoc
     */
    public function configuration(): ConfigurationInterface
    {
        $configurations = [];

        foreach ($this->patterns as $pattern) {
            foreach (glob($pattern) as $path) {
                $configurations[] = new PhpFileConfiguration($this->parser, $path);
            }
        }

        return new MergedConfiguration(...$configurations);
    }
}
