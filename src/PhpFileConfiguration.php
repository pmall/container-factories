<?php declare(strict_types=1);

namespace Quanta\Container;

use Interop\Container\ServiceProviderInterface;

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
                $configuration = $this->configuration($path);

                $factories = array_merge(...[
                    $this->parameters($configuration[self::KEYS['parameters']]),
                    $this->aliases($path, $configuration[self::KEYS['aliases']]),
                    $this->factories($path, $configuration[self::KEYS['factories']]),
                ]);

                $extensions = $this->extensions($path, $configuration[self::KEYS['extensions']]);

                $providers[] = $this->provider($factories, $extensions);
            }
        }

        return $providers ?? [];
    }

    /**
     * Return the configuration provided by the file located as the gien path.
     *
     * @param string $path
     * @return array[]
     * @throws \UnexpectedValueException
     */
    private function configuration(string $path): array
    {
        $contents = require $path;

        if (! is_array($contents)) {
            throw new \UnexpectedValueException(
                (string) new ReturnTypeErrorMessage(
                    sprintf('the file located at %s', $path), 'array', $contents
                )
            );
        }

        if (! areAllTypedAs('array', $contents)) {
            throw new \UnexpectedValueException(
                (string) new ArrayReturnTypeErrorMessage(
                    sprintf('the file located at %s', $path), 'array', $contents
                )
            );
        }

        return [
            'parameters' => $contents[self::KEYS['parameters']] ?? [],
            'aliases' => $contents[self::KEYS['aliases']] ?? [],
            'factories' => $contents[self::KEYS['factories']] ?? [],
            'extensions' => $contents[self::KEYS['extensions']] ?? [],
        ];
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
     * @return array
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
     * @return array
     * @throws \UnexpectedValueException
     */
    private function aliases(string $path, array $aliases): array
    {
        try {
            return array_map([$this, 'alias'], $aliases);
        }

        catch (\TypeError $e) {
            throw new \UnexpectedValueException(
                $this->invalidTypeErrorMessage(
                    self::KEYS['aliases'], 'string', $path, $aliases
                )
            );
        }
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
                $this->invalidTypeErrorMessage(
                    self::KEYS['factories'], 'callable', $path, $factories
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
                $this->invalidTypeErrorMessage(
                    self::KEYS['extensions'], 'callable', $path, $extensions
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
     * @param string $key
     * @param string $type
     * @param string $path
     * @param array $values
     * @return string
     */
    private function invalidTypeErrorMessage(string $key, string $type, string $path, array $values): string
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
}
