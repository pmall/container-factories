<?php declare(strict_types=1);

namespace Quanta\Container\Values;

use Psr\Container\ContainerInterface;

use Quanta\Container\Helpers\Pluck;
use Quanta\Container\Helpers\ArrayStr;

use function \Quanta\Exceptions\areAllTypedAs;
use \Quanta\Exceptions\ArrayArgumentTypeErrorMessage;

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
        if (! areAllTypedAs(ValueInterface::class, $values)) {
            throw new \InvalidArgumentException(
                (string) new ArrayArgumentTypeErrorMessage(
                    1, ValueInterface::class, $values
                )
            );
        }

        $this->values = $values;
    }

    /**
     * @inheritdoc
     */
    public function value(ContainerInterface $container)
    {
        return array_map(new Pluck('value', $container), $this->values);
    }

    /**
     * @inheritdoc
     */
    public function str(string $container): string
    {
        return (string) new ArrayStr(array_map(
            new Pluck('str', $container),
            $this->values
        ));
    }
}
