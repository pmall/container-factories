<?php declare(strict_types=1);

namespace Quanta\Container;

use Psr\Container\ContainerInterface;

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
        return (string) new Formatting\ContainerEntry($container, $this->id, $this->nullable);
    }
}
