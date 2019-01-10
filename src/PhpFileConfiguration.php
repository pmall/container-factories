<?php declare(strict_types=1);

namespace Quanta\Container;

use Interop\Container\ServiceProviderInterface;

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

                $factories = new MergedFactoryMap(...[
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
     * Return a ParametersFactoryMap with the value factory and the given array
     * of values.
     *
     * @param array $values
     * @return \Quanta\Container\ParametersFactoryMap
     */
    private function parameters(array $values): ParametersFactoryMap
    {
        return new ParametersFactoryMap($this->factory, $values);
    }

    /**
     * Return an AliasesFactoryMap with the given array of aliases.
     *
     * The file path is given in order to throw a descriptive exception.
     *
     * @param string $path
     * @param array $aliases
     * @return \Quanta\Container\AliasesFactoryMap
     * @throws \UnexpectedValueException
     */
    private function aliases(string $path, array $aliases): AliasesFactoryMap
    {
        try {
            return new AliasesFactoryMap($aliases);
        }

        catch (\InvalidArgumentException $e) {
            throw new \UnexpectedValueException(
                $this->invalidTypeErrorMessage(
                    self::KEYS['aliases'], 'string', $path, $aliases
                )
            );
        }
    }

    /**
     * Return a FactoryMap with the given array of factories.
     *
     * The file path is given in order to throw a descriptive exception.
     *
     * @param string $path
     * @param array $factories
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
                $this->invalidTypeErrorMessage(
                    self::KEYS['factories'], 'callable', $path, $factories
                )
            );
        }
    }

    /**
     * Return a FactoryMap with the given array of extensions.
     *
     * The file path is given in order to throw a descriptive exception.
     *
     * @param string $path
     * @param array $extensions
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
                $this->invalidTypeErrorMessage(
                    self::KEYS['extensions'], 'callable', $path, $extensions
                )
            );
        }
    }

    /**
     * Return a service provider with the given factories and extensions.
     *
     * @param \Quanta\Container\FactoryMapInterface $factories
     * @param \Quanta\Container\FactoryMapInterface $extensions
     * @return \Interop\Container\ServiceProviderInterface
     */
    private function provider(FactoryMapInterface $factories, FactoryMapInterface $extensions): ServiceProviderInterface
    {
        return new class ($factories, $extensions) implements ServiceProviderInterface
        {
            private $factories;
            private $extensions;

            public function __construct(FactoryMapInterface $factories, FactoryMapInterface $extensions)
            {
                $this->factories = $factories;
                $this->extensions = $extensions;
            }

            public function getFactories()
            {
                return $this->factories->factories();
            }

            public function getExtensions()
            {
                return $this->extensions->factories();
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
