<?php declare(strict_types=1);

namespace Quanta\Container\Values;

use Psr\Container\ContainerInterface;

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
        if (is_array($value)) {
            throw new \InvalidArgumentException(
                vsprintf('Can\'t use array with %s, please use %s instead', [
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
            return sprintf('\'%s\'', $this->value);
        }

        if (is_object($this->value)) {
            $tpl = $this->value instanceof \Closure
                ? 'Unable to compile an instance of %s, please use a factory instead'
                : 'Unable to compile an instance of %s, please use an instance of %s instead';

            throw new \LogicException(
                sprintf($tpl, get_class($this->value), Instance::class)
            );
        }

        if (is_resource($this->value)) {
            throw new \LogicException('Unable to compile a resource');
        }

        return 'null';
    }
}
