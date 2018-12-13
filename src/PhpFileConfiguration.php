<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Exceptions\ArrayTypeCheckTrait;
use Quanta\Exceptions\ReturnTypeErrorMessage;
use Quanta\Exceptions\ArrayReturnTypeErrorMessage;

final class PhpFileConfiguration implements ServiceProviderCollectionInterface
{
    use ArrayTypeCheckTrait;

    /**
     * Glob patterns matching files returning array of factories.
     *
     * @var string[]
     */
    private $patterns;

    /**
     * Constructor.
     *
     * @param string ...$patterns
     */
    public function __construct(string ...$patterns)
    {
        $this->patterns = $patterns;
    }

    /**
     * @inheritdoc
     */
    public function providers(): array
    {
        $providers = [];

        foreach ($this->patterns as $patterns) {
            foreach (glob($pattern) as $path) {
                $configuration = require $path;

                if (! is_array($configuration)) {
                    throw new \UnexpectedValueException(
                        (string) new ReturnTypeErrorMessage(
                            sprintf('the file located at %s', $path), 'array', $configuration
                        )
                    );
                }

                if (! $this->areAllTypedAs('array', $configuration)) {
                    throw new \UnexpectedValueException(
                        (string) new ArrayReturnTypeErrorMessage(
                            sprintf('the file located at %s', $path), 'array', $configuration
                        )
                    );
                }

                $providers[] = new ServiceProvider(
                    $parameters = is_array($configuration['parameters'] ?? [])
                        ? $configuration['parameters'] ?? []
                        : [],
                    $aliases = is_array($configuration['aliases'] ?? [])
                        ? $configuration['aliases'] ?? []
                        : [],
                    $factories = is_array($configuration['factories'] ?? [])
                        ? $configuration['factories'] ?? []
                        : [],
                    $extensions = is_array($configuration['extensions'] ?? [])
                        ? $configuration['extensions'] ?? []
                        : []
                );
            }
        }

        return $providers;
    }
}
