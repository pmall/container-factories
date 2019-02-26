<?php declare(strict_types=1);

namespace Quanta\Container\Values;

use Psr\Container\ContainerInterface;

use Quanta\Container\Compilation\StaticMethodStr;

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
            return sprintf('\'%s\'', $this->value);
        }

        if (is_array($this->value)) {
            if (is_string($this->value[0])) {
                return (string) new StaticMethodStr(...$this->value);
            }

            throw new \LogicException(
                $this->objectCompilationErrorMessage($this->value[0])
            );
        }

        if (is_object($this->value)) {
            throw new \LogicException(
                $this->objectCompilationErrorMessage($this->value)
            );
        }

        if (is_resource($this->value)) {
            throw new \LogicException('Unable to compile a resource, please use a factory instead');
        }

        return 'null';
    }

    /**
     * Return the message ot the exception thrown when compiling an object.
     *
     * @param object $object
     * @return string
     */
    private function objectCompilationErrorMessage($object): string
    {
        return vsprintf('Unable to compile an instance of %s, please use a factory instead', [
            get_class($object)
        ]);
    }
}
