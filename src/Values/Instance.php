<?php declare(strict_types=1);

namespace Quanta\Container\Values;

use Psr\Container\ContainerInterface;

use Quanta\Container\Compilation\InstanceStr;

final class Instance implements ValueInterface
{
    /**
     * The name of the class to instantiate.
     *
     * @var string
     */
    private $class;

    /**
     * The arguments the class constructor is invoked with.
     *
     * @var \Quanta\Container\Values\ArrayValue
     */
    private $xs;

    /**
     * Constructor.
     *
     * @param string                                    $class
     * @param \Quanta\Container\Values\ValueInterface   ...$xs
     */
    public function __construct(string $class, ValueInterface ...$xs)
    {
        $this->class = $class;
        $this->xs = new ArrayValue($xs);
    }

    /**
     * @inheritdoc
     */
    public function value(ContainerInterface $container)
    {
        $xs = $this->xs->value($container);

        return new $this->class(...$xs);
    }

    /**
     * @inheritdoc
     */
    public function str(string $container): string
    {
        $xs = $this->xs->strs($container);

        return (string) new InstanceStr($this->class, ...$xs);
    }
}
