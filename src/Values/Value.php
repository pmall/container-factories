<?php declare(strict_types=1);

namespace Quanta\Container\Values;

use Psr\Container\ContainerInterface;

use Quanta\Container\Utils;

final class Value implements ValueInterface
{
    /**
     * The value.
     *
     * @var mixed
     */
    private $value;

    /**
     * Constructor.
     *
     * @param mixed $value
     * @throws \InvalidArgumentException
     */
    public function __construct($value)
    {
        if (is_array($value) && ! is_callable($value)) {
            throw new \InvalidArgumentException(
                vsprintf('Arrays can\'t be used with %s, please use %s instead', [
                    Value::class,
                    ArrayValue::class,
                ])
            );
        }

        $this->value = $value;
    }

    /**
     * @inheritdoc
     */
    public function value(ContainerInterface $container)
    {
        return $this->value;
    }

    /**
     * @inheritdoc
     */
    public function str(string $container): string
    {
        if (is_bool($this->value)) {
            return $this->value ? 'true' : 'false';
        }

        if (is_numeric($this->value)) {
            return (string) $this->value;
        }

        if (is_string($this->value)) {
            return sprintf('\'%s\'', addslashes($this->value));
        }

        if (is_array($this->value)) {
            if (is_string($this->value[0])) {
                return Utils::staticMethodStr(...$this->value);
            }

            throw new \LogicException(
                vsprintf('Can\'t compile [object(%s), \'%s\'], please use a factory instead', [
                    get_class($this->value[0]), $this->value[1],
                ])
            );
        }

        if (is_object($this->value)) {
            throw new \LogicException(
                vsprintf('Object(%s) can\'t be compiled, please use a factory instead', [
                    get_class($this->value),
                ])
            );
        }

        if (is_resource($this->value)) {
            throw new \LogicException(
                vsprintf('Resource(%s) can\'t be compiled, please use a factory instead', [
                    $this->value,
                ])
            );
        }

        return 'null';
    }
}
