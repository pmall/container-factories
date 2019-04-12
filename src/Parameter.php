<?php declare(strict_types=1);

namespace Quanta\Container;

use Psr\Container\ContainerInterface;

final class Parameter implements FactoryInterface
{
    /**
     * The parameter value.
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
                vsprintf('Parameter value can\'t be an array, please use %s instead', [
                    FactoryArray::class,
                ])
            );
        }

        $this->value = $value;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container)
    {
        return $this->value;
    }

    /**
     * @inheritdoc
     */
    public function compiled(string $container, callable $compiler): string
    {
        if (is_callable($this->value)) {
            return $compiler($this->value);
        }

        if (is_bool($this->value)) {
            return $this->value ? 'true' : 'false';
        }

        if (is_int($this->value)) {
            return (string) $this->value;
        }

        if (is_float($this->value)) {
            return (string) $this->value;
        }

        if (is_string($this->value)) {
            return '\'' . $this->value . '\'';
        }

        if (is_object($this->value)) {
            throw new \LogicException(
                (string) new Formatting\ObjectCompilationErrorMessage($this->value)
            );
        }

        if (is_resource($this->value)) {
            throw new \LogicException(
                'Unable to compile resources, please use a factory instead'
            );
        }

        if (is_null($this->value)) {
            return 'null';
        }

        throw new \LogicException('Unknown type');
    }
}
