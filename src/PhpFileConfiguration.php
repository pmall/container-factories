<?php declare(strict_types=1);

namespace Quanta\Container;

use Interop\Container\ServiceProviderInterface;

use Quanta\Container\Factories\Tag;
use Quanta\Container\Factories\Alias;
use Quanta\Container\Factories\Parameter;

use Quanta\Container\Values\ValueFactoryInterface;

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
    public function providers(): array
    {
        foreach ($this->patterns as $pattern) {
            foreach (glob($pattern) as $path) {
                $config = require $path;

                if (! is_array($config)) {
                    throw new \UnexpectedValueException(
                        (string) new ReturnTypeErrorMessage(
                            sprintf('the file located at %s', $path), 'array', $config
                        )
                    );
                }

                if (! areAllTypedAs('array', $config)) {
                    throw new \UnexpectedValueException(
                        (string) new ArrayReturnTypeErrorMessage(
                            sprintf('the file located at %s', $path), 'array', $config
                        )
                    );
                }

                $factories = array_merge(...[
                    $this->parameters($config[self::KEYS['parameters']] ?? []),
                    $this->aliases($path, $config[self::KEYS['aliases']] ?? []),
                    $this->factories($path, $config[self::KEYS['factories']] ?? []),
                ]);

                $extensions = array_merge(...[
                    $this->extensions($path, $config[self::KEYS['extensions']] ?? []),
                    $this->tags($path, $config[self::KEYS['tags']] ?? []),
                ]);

                $providers[] = $this->provider($factories, $extensions);
            }
        }

        return $providers ?? [];
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
     * @return \Quanta\Container\Factories\Parameter[]
     */
    private function parameters(array $values): array
    {
        return array_map([$this, 'parameter'], $values);
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
     * The file path is given in order to throw a descriptive exception.
     *
     * @param string $path
     * @param array $aliases
     * @return \Quanta\Container\Factories\Alias[]
     * @throws \UnexpectedValueException
     */
    private function aliases(string $path, array $aliases): array
    {
        try {
            return array_map([$this, 'alias'], $aliases);
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
     * Return an array of tags from the given array of container entry
     * identifier arrays.
     *
     * The file path is given in order to throw a descriptive exception.
     *
     * @param string $path
     * @param array $tags
     * @return \Quanta\Container\Factories\Tag[]
     * @throws \UnexpectedValueException
     */
    private function tags(string $path, array $tags): array
    {
        if (! areAllTypedAs('array', $tags)) {
            throw new \UnexpectedValueException(
                $this->invalidKeyTypeErrorMessage(
                    $path, self::KEYS['tags'], 'array', $tags
                )
            );
        }

        foreach ($tags as $id => $aliases) {
            try {
                $extensions[$id] = new Tag(...array_values($aliases));
            }

            catch (\TypeError $e) {
                throw new \UnexpectedValueException(
                    $this->invalidTagTypeErrorMessage($path, $id, $aliases)
                );
            }
        }

        return $extensions ?? [];
    }

    /**
     * Ensure all values of the given array of factories are callable.
     *
     * The file path is given in order to throw a descriptive exception.
     *
     * @param string $path
     * @param array $factories
     * @return array
     * @throws \UnexpectedValueException
     */
    private function factories(string $path, array $factories): array
    {
        if (! areAllTypedAs('callable', $factories)) {
            throw new \UnexpectedValueException(
                $this->invalidKeyTypeErrorMessage(
                    $path, self::KEYS['factories'], 'callable', $factories
                )
            );
        }

        return $factories;
    }

    /**
     * Ensure all values of the given array of extensions are callable.
     *
     * The file path is given in order to throw a descriptive exception.
     *
     * @param string $path
     * @param array $extensions
     * @return array
     * @throws \UnexpectedValueException
     */
    private function extensions(string $path, array $extensions): array
    {
        if (! areAllTypedAs('callable', $extensions)) {
            throw new \UnexpectedValueException(
                $this->invalidKeyTypeErrorMessage(
                    $path, self::KEYS['extensions'], 'callable', $extensions
                )
            );
        }

        return $extensions;
    }

    /**
     * Return a service provider with the given factories and extensions.
     *
     * @param array $factories
     * @param array $extensions
     * @return \Interop\Container\ServiceProviderInterface
     */
    private function provider(array $factories, array $extensions): ServiceProviderInterface
    {
        return new class ($factories, $extensions) implements ServiceProviderInterface
        {
            private $factories;
            private $extensions;

            public function __construct(array $factories, array $extensions)
            {
                $this->factories = $factories;
                $this->extensions = $extensions;
            }

            public function getFactories()
            {
                return $this->factories;
            }

            public function getExtensions()
            {
                return $this->extensions;
            }
        };
    }

    /**
     * Return the message of exceptions thrown when an array contains at least
     * one value with a wrong type.
     *
     * @param string $path
     * @param string $key
     * @param string $type
     * @param array $values
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
