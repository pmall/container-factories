<?php declare(strict_types=1);

namespace Quanta\Container\Compilation;

final class ContainerEntry implements CompilableInterface
{
    /**
     * The container variable name.
     *
     * @var string
     */
    private $container;

    /**
     * The container entry id.
     *
     * @var string $id
     */
    private $id;

    /**
     * Constructor.
     *
     * @param string $container
     * @param string $id
     */
    public function __construct(string $container, string $id)
    {
        $this->container = $container;
        $this->id = $id;
    }

    /**
     * @inheritdoc
     */
    public function compiled(Compiler $compiler): string
    {
        return sprintf('$%s->get(%s)', $this->container, $compiler($this->id));
    }
}
