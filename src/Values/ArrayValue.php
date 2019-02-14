<?php declare(strict_types=1);

namespace Quanta\Container\Values;

use Psr\Container\ContainerInterface;

use Quanta\Container\Compilation\ArrayStr;

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
        $mapper = $this->arrval($container);

        return array_map($mapper, $this->values);
    }

    /**
     * @inheritdoc
     */
    public function str(string $container): string
    {
        return (string) new ArrayStr($this->strs($container));
    }

    /**
     * Return an array of string representations fo the values.
     *
     * @param string $container
     * @return string[]
     */
    public function strs(string $container): array
    {
        $mapper = $this->arrstr($container);

        return array_map($mapper, $this->values);
    }

    /**
     * Return a callable returning the value of a ValueInterface implementation.
     *
     * @param \Psr\Container\ContainerInterface $container
     * @return callable
     */
    private function arrval(ContainerInterface $container): callable
    {
        return function (ValueInterface $value) use ($container) {
            return $value->value($container);
        };
    }

    /**
     * Return a callable returning the string representation of a ValueInterface
     * implementation.
     *
     * @param string $container
     * @return callable
     */
    private function arrstr(string $container): callable
    {
        return function (ValueInterface $value) use ($container) {
            return $value->str($container);
        };
    }
}
