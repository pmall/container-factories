<?php declare(strict_types=1);

namespace Quanta\Container\Values;

use Psr\Container\ContainerInterface;

use Quanta\Container\Helpers\StaticMethodStr;

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

        if (is_array($value) && ! is_string($value[0])) {
            throw new \InvalidArgumentException(
                vsprintf('Callable instance methods can\'t be used with %s, please use a factory instead ([object(%s), \'%s\'] given)', [
                    Value::class,
                    get_class($value[0]),
                    $value[1],
                ])
            );
        }

        if (is_object($value)) {
            throw new \InvalidArgumentException(
                vsprintf('Objects can\'t be used with %s, please use a factory instead (%s given)', [
                    Value::class,
                    get_class($value),
                ])
            );
        }

        if (is_resource($value)) {
            throw new \InvalidArgumentException(
                vsprintf('Resources can\'t be used with %s, please use a factory instead', [
                    Value::class,
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
            return (string) new StaticMethodStr(...$this->value);
        }

        return 'null';
    }
}
