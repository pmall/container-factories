<?php declare(strict_types=1);

namespace Quanta\Container\Factories;

use Psr\Container\ContainerInterface;

use Quanta\Container\Values\ValueInterface;

final class Factory implements CompilableFactoryInterface
{
    /**
     * The value of the parameter.
     *
     * @var \Quanta\Container\Values\ValueInterface
     */
    private $value;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Values\ValueInterface $value
     */
    public function __construct(ValueInterface $value)
    {
        $this->value = $value;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container)
    {
        return $this->value->value($container);
    }

    /**
     * @inheritdoc
     */
    public function compiled(Compiler $compiler): CompiledFactory
    {
        return new CompiledFactory('container', '', ...[
            sprintf('return %s;', $this->value->str('container')),
        ]);
    }
}