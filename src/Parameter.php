<?php declare(strict_types=1);

namespace Quanta\Container;

use Psr\Container\ContainerInterface;

use Quanta\Container\Compilation\Compilable;
use Quanta\Container\Compilation\CompilableInterface;

final class Parameter implements FactoryInterface
{
    /**
     * The parameter value.
     *
     * @var mixed
     */
    private $value;

    /**
     * Return a new Parameter from the given value.
     *
     * @param mixed $value
     * @return \Quanta\Container\Parameter
     */
    public static function instance($value): self
    {
        return new self($value);
    }

    /**
     * Constructor.
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
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
    public function compilable(string $container): CompilableInterface
    {
        return new Compilable($this->value);
    }
}
