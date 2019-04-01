<?php declare(strict_types=1);

namespace Quanta\Container;

use Psr\Container\ContainerInterface;

use Quanta\Container\Compilation\ContainerEntry;
use Quanta\Container\Compilation\CompilableInterface;

final class Alias implements FactoryInterface
{
    /**
     * The container entry identifier.
     *
     * @var string
     */
    private $id;

    /**
     * Return a new Alias from the given id.
     *
     * @param string $id
     * @return \Quanta\Container\Alias
     */
    public static function instance(string $id): self
    {
        return new self($id);
    }

    /**
     * Constructor.
     *
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container)
    {
        return $container->get($this->id);
    }

    /**
     * @inheritdoc
     */
    public function compilable(string $container): CompilableInterface
    {
        return new ContainerEntry($container, $this->id);
    }
}
