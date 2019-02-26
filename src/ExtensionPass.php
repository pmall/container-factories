<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Factories\Extension;

use function Quanta\Exceptions\areAllTypedAs;
use Quanta\Exceptions\ArrayArgumentTypeErrorMessage;

final class ExtensionPass implements ProcessingPassInterface
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
            if (key_exists($id, $factories)) {
                $factories[$id] = new Extension($factories[$id], $extension);
            }
        }

        return $factories;
    }
}
