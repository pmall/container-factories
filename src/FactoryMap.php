<?php declare(strict_types=1);

namespace Quanta\Container;

use function Quanta\Exceptions\areAllTypedAs;
use Quanta\Exceptions\ArrayArgumentTypeErrorMessage;

final class FactoryMap implements FactoryMapInterface
{
    /**
     * The associative array of factories.
     *
     * @var callable[]
     */
    private $factories;

    /**
     * Constructor.
     *
     * @param callable[] $factories
     * @throws \InvalidArgumentException
     */
    public function __construct(array $factories)
    {
        if (! areAllTypedAs('callable', $factories)) {
            throw new \InvalidArgumentException(
                (string) new ArrayArgumentTypeErrorMessage(1, 'callable', $factories)
            );
        }

        $this->factories = $factories;
    }

    /**
     * @inheritdoc
     */
    public function factories(): array
    {
        return $this->factories;
    }
}
