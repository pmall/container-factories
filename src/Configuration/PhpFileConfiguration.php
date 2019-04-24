<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\Alias;
use Quanta\Container\Invokable;
use Quanta\Container\ValueParser;

final class PhpFileConfiguration implements ConfigurationInterface
{
    /**
     * The parser used to produce factories from parameters.
     *
     * @var \Quanta\Container\ValueParser
     */
    private $parser;

    /**
     * The php file path.
     *
     * @var string
     */
    private $path;

    /**
     * The cached file contents.
     *
     * @var array[]
     */
    private $contents;

    /**
     * Constructor.
     *
     * @param string $path
     */
    public function __construct(ValueParser $parser, string $path)
    {
        $this->parser = $parser;
        $this->path = $path;
    }

    /**
     * @inheritdoc
     */
    public function factories(): array
    {
        $contents = $this->contents();

        if (! is_array($contents['parameters'])) {
            throw new \UnexpectedValueException(
                $this->keyNotArrayErrorMessage($contents, 'parameters')
            );
        }

        $factories = array_map($this->parser, $contents['parameters']);

        if (! is_array($contents['aliases'])) {
            throw new \UnexpectedValueException(
                $this->keyNotArrayErrorMessage($contents, 'aliases')
            );
        }

        foreach ($contents['aliases'] as $alias => $id) {
            try {
                $factories[$alias] = new Alias($id);
            }

            catch (\TypeError $e) {
                throw new \UnexpectedValueException(
                    $this->arrayKeyTypeErrorMessage($contents, $alias, 'strings', 'aliases')
                );
            }
        }

        if (! is_array($contents['invokables'])) {
            throw new \UnexpectedValueException(
                $this->keyNotArrayErrorMessage($contents, 'invokables')
            );
        }

        foreach ($contents['invokables'] as $id => $class) {
            try {
                $factories[$id] = new Invokable($class);
            }

            catch (\TypeError $e) {
                throw new \UnexpectedValueException(
                    $this->arrayKeyTypeErrorMessage($contents, $id, 'strings', 'invokables')
                );
            }
        }

        if (! is_array($contents['factories'])) {
            throw new \UnexpectedValueException(
                $this->keyNotArrayErrorMessage($contents, 'factories')
            );
        }

        foreach ($contents['factories'] as $id => $factory) {
            if (! is_callable($factory)) {
                throw new \UnexpectedValueException(
                    $this->arrayKeyTypeErrorMessage($contents, $id, 'callables', 'factories')
                );
            }
        }

        return array_merge($factories, $contents['factories']);
    }

    /**
     * @inheritdoc
     */
    public function mappers(): array
    {
        $contents = $this->contents();

        $mappers = [];

        if (! is_array($contents['tags'])) {
            throw new \UnexpectedValueException(
                $this->keyNotArrayErrorMessage($contents, 'tags')
            );
        }

        foreach ($contents['tags'] as $id => $tagged) {
            if (! is_array($tagged)) {
                throw new \UnexpectedValueException(
                    $this->arrayKeyTypeErrorMessage($contents, $id, 'arrays', 'tags')
                );
            }

            try {
                $mappers[$id] = new Tagging\Entries(...array_values($tagged));
            }

            catch (\TypeError $e) {
                $invalid = array_filter($tagged, function ($tag) {
                    return ! is_string($tag);
                });

                throw new \UnexpectedValueException(
                    $this->arrayKeyTypeErrorMessage($contents, (string) key($invalid), 'strings', 'tags', $id)
                );
            }
        }

        if (! is_array($contents['mappers'])) {
            throw new \UnexpectedValueException(
                $this->keyNotArrayErrorMessage($contents, 'mappers')
            );
        }

        foreach ($contents['mappers'] as $id => $class) {
            try {
                $mapper = new Tagging\Implementations($class);
            }

            catch (\TypeError $e) {
                throw new \UnexpectedValueException(
                    $this->arrayKeyTypeErrorMessage($contents, $id, 'strings', 'mappers')
                );
            }

            $mappers[$id] = key_exists($id, $mappers)
                ? new Tagging\CompositeTagging($mappers[$id], $mapper)
                : $mapper;
        }

        return $mappers;
    }

    /**
     * @inheritdoc
     */
    public function extensions(): array
    {
        $contents = $this->contents();

        $extensions = [];

        if (! is_array($contents['extensions'])) {
            throw new \UnexpectedValueException(
                $this->keyNotArrayErrorMessage($contents, 'extensions')
            );
        }

        foreach ($contents['extensions'] as $id => $extension) {
            if (! is_callable($extension)) {
                throw new \UnexpectedValueException(
                    $this->arrayKeyTypeErrorMessage($contents, $id, 'callables', 'extensions')
                );
            }

            $extensions[$id] = [$extension];
        }

        return $extensions;
    }

    /**
     * Return the content of the file and cache it.
     *
     * @return array[];
     * @throws \LogicException
     * @throws \UnexpectedValueException
     */
    private function contents(): array
    {
        if (! $this->contents) {
            if (! file_exists($this->path)) {
                throw new \LogicException(
                    vsprintf('The PHP configuration file does not exist (%s)', [
                        realpath($this->path),
                    ])
                );
            }

            $contents = require $this->path;

            if (! is_array($contents)) {
                throw new \UnexpectedValueException(
                    vsprintf('The PHP configuration file must return an array, %s returned (%s)', [
                        gettype($contents),
                        realpath($this->path),
                    ])
                );
            }

            $this->contents = [
                'parameters' => $contents['parameters'] ?? [],
                'aliases' => $contents['aliases'] ?? [],
                'invokables' => $contents['invokables'] ?? [],
                'factories' => $contents['factories'] ?? [],
                'tags' => $contents['tags'] ?? [],
                'mappers' => $contents['mappers'] ?? [],
                'extensions' => $contents['extensions'] ?? [],
            ];
        }

        return $this->contents;
    }

    /**
     * Return the error message of the exception thrown when a key of the
     * configuration array is not an array.
     *
     * @param array     $contents
     * @param string    ...$path
     * @return string
     */
    private function keyNotArrayErrorMessage(array $contents, string ...$path): string
    {
        $value = array_reduce($path, function (array $arr, string $key) {
            return $arr[$key];
        }, $contents);

        return vsprintf('The key [%s] of the configuration array must be an array, %s given (%s)', [
            implode('.', $path),
            gettype($value),
            realpath($this->path),
        ]);
    }

    /**
     * Return the error message of the exception thrown when a key of an array
     * is associated to a value with an unexpected type.
     *
     * @param array     $contents
     * @param string    $key
     * @param string    $type
     * @param string    ...$path
     * @return string
     */
    private function arrayKeyTypeErrorMessage(array $contents, string $key, string $type, string ...$path): string
    {
        $arr = array_reduce($path, function (array $arr, string $key) {
            return $arr[$key];
        }, $contents);

        return vsprintf('The key [%s] of the configuration array must be an array of %s, %s given for key [%s] (%s)', [
            implode('.', $path),
            $type,
            gettype($arr[$key]),
            $key,
            realpath($this->path),
        ]);
    }
}
