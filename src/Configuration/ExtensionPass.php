<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\Factories\Extension;

use function Quanta\Exceptions\areAllTypedAs;
use Quanta\Exceptions\ArrayArgumentTypeErrorMessage;

final class ExtensionPass implements ConfigurationPassInterface
{
    /**
     * The associative array of extensions used to extend the factories.
     *
     * @var callable[]
     */
    private $extensions;

    /**
     * Constructor.
     *
     * @param callable[] $extensions
     * @throws \InvalidArgumentException
     */
    public function __construct(array $extensions)
    {
        if (! areAllTypedAs('callable', $extensions)) {
            throw new \InvalidArgumentException(
                (string) new ArrayArgumentTypeErrorMessage(1, 'callable', $extensions)
            );
        }

        $this->extensions = $extensions;
    }

    /**
     * @inheritdoc
     */
    public function processed(array $factories): array
    {
        foreach ($this->extensions as $id => $extension) {
            $factories[$id] = key_exists($id, $factories)
                 ? new Extension($factories[$id], $extension)
                 : $extension;
        }

        return $factories;
    }
}
