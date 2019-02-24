<?php declare(strict_types=1);

namespace Quanta\Container\Factories;

use Psr\Container\ContainerInterface;

use Quanta\Container\Compilation\Template;

final class Invokable implements CompilableFactoryInterface
{
    /**
     * The invokable class name.
     *
     * @var string
     */
    private $class;

    /**
     * Constructor.
     *
     * @param string $class
     */
    public function __construct(string $class)
    {
        $this->class = $class;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container)
    {
        return (new $this->class)($container);
    }

    /**
     * @inheritdoc
     */
     public function compiled(Template $template): string
     {
        return $template->strWithReturnf(vsprintf('(new \%s)($%s)', [
            $this->class,
            $template->containerVariableName(),
        ]));
    }
}
