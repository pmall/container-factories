<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\ProcessedFactoryMap;
use Quanta\Container\Values\ValueFactory;

final class PhpFileConfiguration implements ConfigurationInterface
{
    /**
     * The configuration keys to look for in the file.
     *
     * @var string[]
     */
    const KEYS = [
        'parameters',
        'aliases',
        'invokables',
        'factories',
        'extensions',
        'tags',
        'mappers',
    ];

    /**
     * The value factory used to parse parameters.
     *
     * @var \Quanta\Container\Values\ValueFactory
     */
    private $factory;

    /**
     * The php file path.
     *
     * @var string
     */
    private $path;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Values\ValueFactory $factory
     * @param string                                $path
     */
    public function __construct(ValueFactory $factory, string $path)
    {
        $this->factory = $factory;
        $this->path = $path;
    }

    /**
     * @inheritdoc
     */
    public function map(): ProcessedFactoryMap
    {
        // ensure the file exists.
        if (! file_exists($this->path)) {
            throw new \RuntimeException(
                sprintf('No file located at %s', $this->path)
            );
        }

        // get the content of the file, hide non php contents.
        ob_start();
        $contents = require $this->path;
        ob_end_clean();

        // ensure the file returns an array.
        if (! is_array($contents)) {
            throw new \UnexpectedValueException(
                vsprintf('The file located at %s must return an array, %s returned', [
                    $this->path, gettype($contents)
                ])
            );
        }

        // ensure all the configuration keys are arrays.
        foreach (self::KEYS as $key) {
            $configuration[$key] = $contents[$key] ?? [];

            if (! is_array($configuration[$key])) {
                throw new \UnexpectedValueException(
                    vsprintf('The key \'%s\' of the array returned by the file located at %s must be an array, %s returned', [
                        $key, $this->path, gettype($configuration[$key])
                    ])
                );
            }
        }
    }
}
