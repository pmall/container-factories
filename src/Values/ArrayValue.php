<?php declare(strict_types=1);

namespace Quanta\Container\Values;

use Psr\Container\ContainerInterface;

use Quanta\Container\Utils;

final class ArrayValue implements ValueInterface
{
    /**
     * The array of ValueInterface implementations.
     *
     * @var \Quanta\Container\Values\ValueInterface[]
     */
    private $values;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Values\ValueInterface[] $values
     * @throws \InvalidArgumentException
     */
    public function __construct(array $values)
    {
        $result = \Quanta\ArrayTypeCheck::result($values, ValueInterface::class);

        if (! $result->isValid()) {
            throw new \InvalidArgumentException(
                $result->message()->constructor($this, 1)
            );
        }

        $this->values = $values;
    }

    /**
     * @inheritdoc
     */
    public function value(ContainerInterface $container)
    {
        return Utils::plucked($this->values, 'value', $container);
    }

    /**
     * @inheritdoc
     */
    public function str(string $container): string
    {
        return Utils::arrayStr(
            Utils::plucked($this->values, 'str', $container)
        );
    }
}
