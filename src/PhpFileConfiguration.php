<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Factories\Tag;
use Quanta\Container\Factories\Alias;
use Quanta\Container\Factories\Parameter;

use Quanta\Container\Values\ValueFactory;
use Quanta\Container\Values\EnvVarParser;
use Quanta\Container\Values\InstanceParser;
use Quanta\Container\Values\ValueFactoryInterface;
use Quanta\Container\Values\InterpolatedStringParser;

use Quanta\Container\Passes\ReverseTagging;
use Quanta\Container\Passes\ConfigurationPassInterface;

use function Quanta\Exceptions\areAllTypedAs;
use Quanta\Exceptions\InvalidKey;
use Quanta\Exceptions\InvalidType;
use Quanta\Exceptions\ReturnTypeErrorMessage;
use Quanta\Exceptions\ArrayReturnTypeErrorMessage;

final class PhpFileConfiguration implements ConfigurationInterface
{
    /**
     * The expected key names of the arrays returned by the files.
     *
     * @var string[]
     */
    const KEYS = [
        'parameters' => 'parameters',
        'aliases' => 'aliases',
        'factories' => 'factories',
        'extensions' => 'extensions',
        'tags' => 'tags',
        'metadata' => 'metadata',
        'passes' => 'passes',
        'mappers' => 'mappers',
    ];

    /**
     * The value factory used to parse parameter values as ValueInterface
     * implementations.
     *
     * @var \Quanta\Container\Values\ValueFactoryInterface
     */
    private $factory;

    /**
     * Glob patterns matching files returning array of factories.
     *
     * @var string[]
     */
    private $patterns;

    /**
     * Return a new php file configuration with a default value factory.
     *
     * @param string ...$patterns
     * @return \Quanta\Container\PhpFileConfiguration
     */
    public static function withDefaultValueParsers(string ...$patterns): PhpFileConfiguration
    {
        $factory = new ValueFactory(...[
            new EnvVarParser,
            new InstanceParser,
            new InterpolatedStringParser,
        ]);

        return new PhpFileConfiguration($factory, ...$patterns);
    }

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Values\ValueFactoryInterface    $factory
     * @param string                                            ...$patterns
     */
    public function __construct(ValueFactoryInterface $factory, string ...$patterns)
    {
        $this->factory = $factory;
        $this->patterns = $patterns;
    }

    /**
     * @inheritdoc
     */
    public function entries(): array
    {
        foreach (array_unique($this->patterns) as $pattern) {
            foreach (glob($pattern) as $path) {
                // get the file content and ensure it is an array.
                $config = require $path;

                if (! is_array($config)) {
                    throw new \UnexpectedValueException(
                        (string) new ReturnTypeErrorMessage(
                            sprintf('the file located at %s', $path), 'array', $config
                        )
                    );
                }

                // get all the keys and ensure their values are arrays.
                $config = [
                    'parameters' => $config[self::KEYS['parameters']] ?? [],
                    'aliases' => $config[self::KEYS['aliases']] ?? [],
                    'factories' => $config[self::KEYS['factories']] ?? [],
                    'extensions' => $config[self::KEYS['extensions']] ?? [],
                    'tags' => $config[self::KEYS['tags']] ?? [],
                    'metadata' => $config[self::KEYS['metadata']] ?? [],
                    'passes' => $config[self::KEYS['passes']] ?? [],
                    'mappers' => $config[self::KEYS['mappers']] ?? [],
                ];

                if (! areAllTypedAs('array', $config)) {
                    throw new \UnexpectedValueException(
                        (string) new ArrayReturnTypeErrorMessage(
                            sprintf('the file located at %s', $path), 'array', $config
                        )
                    );
                }

                // build factory maps from the definitions.
                $parameters = $this->parameters($config['parameters']);
                $aliases = $this->aliases($path, $config['aliases']);
                $factories = $this->factories($path, $config['factories']);
                $extensions = $this->extensions($path, $config['extensions']);
                $tags = $this->tags($path, $config['tags']);
                $metadata = $this->metadata($path, $config['metadata']);
                $passes = $this->passes($path, $config['passes']);
                $mappers = $this->mappers($path, $config['mappers']);

                // add an anonymous tagged service provider.
                $entries[] = new ConfigurationEntry(
                    new MergedFactoryMap($parameters, $aliases, $factories),
                    new MergedFactoryMap($extensions, $tags),
                    $metadata,
                    ...$passes,
                    ...$mappers
                );
            }
        }

        return $entries ?? [];
    }

    /**
     * Return a parameter from the given value using the factory to parse it as
     * a ValueInterface implementation.
     *
     * @param mixed $value
     * @return \Quanta\Container\Factories\Parameter
     */
    private function parameter($value): Parameter
    {
        return new Parameter(($this->factory)($value));
    }

    /**
     * Return an array of parameters from the given array.
     *
     * @param array $values
     * @return \Quanta\Container\FactoryMap
     */
    private function parameters(array $values): FactoryMap
    {
        return new FactoryMap(array_map([$this, 'parameter'], $values));
    }

    /**
     * Return an alias from the given container entry id.
     *
     * @param string $id
     * @return \Quanta\Container\Factories\Alias
     */
    private function alias(string $id): Alias
    {
        return new Alias($id);
    }

    /**
     * Return an array of aliases from the given array of container entry ids.
     *
     * @param string    $path
     * @param array     $aliases
     * @return \Quanta\Container\FactoryMap
     * @throws \UnexpectedValueException
     */
    private function aliases(string $path, array $aliases): FactoryMap
    {
        try {
            return new FactoryMap(array_map([$this, 'alias'], $aliases));
        }

        catch (\TypeError $e) {
            throw new \UnexpectedValueException(
                $this->invalidKeyTypeErrorMessage(
                    $path, self::KEYS['aliases'], 'string', $aliases
                )
            );
        }
    }

    /**
     * Ensure all values of the given array of factories are callable.
     *
     * @param string    $path
     * @param array     $factories
     * @return \Quanta\Container\FactoryMap
     * @throws \UnexpectedValueException
     */
    private function factories(string $path, array $factories): FactoryMap
    {
        try {
            return new FactoryMap($factories);
        }

        catch (\InvalidArgumentException $e) {
            throw new \UnexpectedValueException(
                $this->invalidKeyTypeErrorMessage(
                    $path, self::KEYS['factories'], 'callable', $factories
                )
            );
        }
    }

    /**
     * Ensure all values of the given array of extensions are callable.
     *
     * @param string    $path
     * @param array     $extensions
     * @return \Quanta\Container\FactoryMap
     * @throws \UnexpectedValueException
     */
    private function extensions(string $path, array $extensions): FactoryMap
    {
        try {
            return new FactoryMap($extensions);
        }

        catch (\InvalidArgumentException $e) {
            throw new \UnexpectedValueException(
                $this->invalidKeyTypeErrorMessage(
                    $path, self::KEYS['extensions'], 'callable', $extensions
                )
            );
        }
    }

    /**
     * Return an array of tags from the given array of container entry
     * identifier arrays.
     *
     * @param string    $path
     * @param array     $tags
     * @return \Quanta\Container\FactoryMap
     * @throws \UnexpectedValueException
     */
    private function tags(string $path, array $tags): FactoryMap
    {
        foreach ($tags as $id => $tag) {
            try {
                $extensions[$id] = new Tag($tag);
            }

            catch (\TypeError $e) {
                throw new \UnexpectedValueException(
                    $this->invalidKeyTypeErrorMessage(
                        $path, self::KEYS['tags'], 'array', $tags
                    )
                );
            }

            catch (\InvalidArgumentException $e) {
                throw new \UnexpectedValueException(
                    $this->invalidTagTypeErrorMessage($path, $id, $tag)
                );
            }
        }

        return new FactoryMap($extensions ?? []);
    }

    /**
     * Return an array of metadata from the given array of metadata.
     *
     * @param string    $path
     * @param array     $metadata
     * @return array[]
     * @throws \UnexpectedValueException
     */
    private function metadata(string $path, array $metadata): array
    {
        if (! areAllTypedAs('array', $metadata)) {
            throw new \UnexpectedValueException(
                $this->invalidKeyTypeErrorMessage(
                    $path, self::KEYS['metadata'], 'array', $metadata
                )
            );
        }

        return $metadata;
    }

    /**
     * Return an array of configuration passes from the given array of passes.
     *
     * @param string    $path
     * @param array     $passes
     * @return \Quanta\Container\Passes\ConfigurationPassInterface[]
     * @throws \UnexpectedValueException
     */
    private function passes(string $path, array $passes): array
    {
        if (! areAllTypedAs(ConfigurationPassInterface::class, $passes)) {
            throw new \UnexpectedValueException(
                $this->invalidKeyTypeErrorMessage(
                    $path, self::KEYS['passes'], ConfigurationPassInterface::class, $passes
                )
            );
        }

        return array_values($passes);
    }

    /**
     * Return a reverse tagging instance from the given container entry id and
     * class name.
     *
     * @param string $id
     * @param string $class
     * @return \Quanta\Container\Passes\ReverseTagging
     */
    private function mapper(string $id, string $class): ReverseTagging
    {
        return new ReverseTagging($id, $class);
    }

    /**
     * Return an array of reverse tagging instances from the given array of
     * mappers.
     *
     * @param string    $path
     * @param array     $mappers
     * @return \Quanta\Container\Passes\ReverseTagging[]
     * @throws \UnexpectedValueException
     */
    private function mappers(string $path, array $mappers): array
    {
        $ids = array_values($mappers);
        $classes = array_map('strval', array_keys($mappers));

        try {
            return array_map([$this, 'mapper'], $ids, $classes);
        }

        catch (\TypeError $e) {
            throw new \UnexpectedValueException(
                $this->invalidKeyTypeErrorMessage(
                    $path, self::KEYS['mappers'], 'string', $ids
                )
            );
        }
    }

    /**
     * Return the message of exceptions thrown when an array contains at least
     * one value with a wrong type.
     *
     * @param string    $path
     * @param string    $key
     * @param string    $type
     * @param array     $values
     * @return string
     */
    private function invalidKeyTypeErrorMessage(string $path, string $key, string $type, array $values): string
    {
        $tpl = implode(' ', [
            'The \'%s\' key of the array returned by the file located at %s',
            'must be an array of %s values,',
            '%s associated to key %s',
        ]);

        return sprintf($tpl, $key, $path, $type, ...[
            new InvalidType($type, $values),
            new InvalidKey($type, $values),
        ]);
    }

    /**
     * Return the message of exceptions thrown when a value of a tag definition
     * array is not a string.
     *
     * @param string        $path
     * @param int|string    $id
     * @param array         $values
     * @return string
     */
    private function invalidTagTypeErrorMessage(string $path, $id, array $values): string
    {
        $tpl = implode(' ', [
            'The tag \'%s\' defined by the file located at %s',
            'must be an array of string values,',
            '%s associated to key %s',
        ]);

        return sprintf($tpl, $id, $path, ...[
            new InvalidType('string', $values),
            new InvalidKey('string', $values),
        ]);
    }
}
