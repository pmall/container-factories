<?php declare(strict_types=1);

namespace Quanta\Container\Factories;

use Psr\Container\ContainerInterface;

use Quanta\Container\Values\ValueInterface;

use Quanta\Container\Compilation\Template;

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
    public function compiled(Template $template): string
    {
        $container = $template->containerVariableName();

        $str = $this->value->str($container);

        return $template->strWithReturn($str);
    }
}
