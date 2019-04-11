<?php declare(strict_types=1);

namespace Quanta\Container;

use Psr\Container\ContainerInterface;

use Quanta\Container\Compilation\ContainerEntry;

final class Alias implements FactoryInterface
{
    /**
     * The container entry identifier.
     *
     * @var string
     */
    private $id;

    /**
     * Whether to return null when the container does not contains id.
     *
     * @var bool
     */
    private $nullable;

    /**
     * Constructor.
     *
     * @param string    $id
     * @param bool      $nullable
     */
    public function __construct(string $id, bool $nullable = false)
    {
        $this->id = $id;
        $this->nullable = $nullable;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container)
    {
        if ($this->nullable && ! $container->has($this->id)) {
            return null;
        }

        return $container->get($this->id);
    }

    /**
     * @inheritdoc
     */
    public function compiled(string $container, callable $compiler): string
    {
        if (! $this->nullable) {
            return (string) new ContainerEntry($container, $this->id);
        }

        return vsprintf('$%s->has(\'%s\') ? %s : null', [
            $container,
            $this->id,
            new ContainerEntry($container, $this->id),
        ]);
    }
}
