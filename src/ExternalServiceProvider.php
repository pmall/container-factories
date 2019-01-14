<?php declare(strict_types=1);

namespace Quanta\Container;

use Interop\Container\ServiceProviderInterface;

use Quanta\Exceptions\ReturnTypeErrorMessage;
use Quanta\Exceptions\ArrayReturnTypeErrorMessage;

final class ExternalServiceProvider implements ConfigurationEntryInterface
{
    /**
     * The original service provider providing the factories and the extensions.
     *
     * @var \Interop\Container\ServiceProviderInterface
     */
    private $provider;

    /**
     * Constructor.
     *
     * @param \Interop\Container\ServiceProviderInterface $provider
     */
    public function __construct(ServiceProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @inheritdoc
     */
    public function factories(): FactoryMapInterface
    {
        $factories = $this->provider->getFactories();

        try {
            return new FactoryMap($factories);
        }

        catch (\TypeError $e) {
            throw new \UnexpectedValueException(
                (string) new ReturnTypeErrorMessage(
                    sprintf('%s::getFactories()', get_class($this->provider)), 'array', $factories
                )
            );
        }

        catch (\InvalidArgumentException $e) {
            throw new \UnexpectedValueException(
                (string) new ArrayReturnTypeErrorMessage(
                    sprintf('%s::getFactories()', get_class($this->provider)), 'callable', $factories
                )
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function extensions(): FactoryMapInterface
    {
        $extensions = $this->provider->getExtensions();

        try {
            return new FactoryMap($extensions);
        }

        catch (\TypeError $e) {
            throw new \UnexpectedValueException(
                (string) new ReturnTypeErrorMessage(
                    sprintf('%s::getExtensions()', get_class($this->provider)), 'array', $extensions
                )
            );
        }

        catch (\InvalidArgumentException $e) {
            throw new \UnexpectedValueException(
                (string) new ArrayReturnTypeErrorMessage(
                    sprintf('%s::getExtensions()', get_class($this->provider)), 'callable', $extensions
                )
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function tags(): array
    {
        return [];
    }
}
