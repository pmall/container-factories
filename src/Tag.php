<?php declare(strict_types=1);

namespace Quanta\Container;

use Psr\Container\ContainerInterface;

use Quanta\Container\Compilation\Compilable;
use Quanta\Container\Compilation\ContainerEntry;
use Quanta\Container\Compilation\CompilableInterface;

final class Tag implements FactoryInterface
{
    /**
     * The tagged container entry ids.
     *
     * @var string[]
     */
    private $ids;

    /**
     * Return a new Tag from the given ids.
     *
     * @param string ...$ids
     * @return \Quanta\Container\Tag
     */
    public static function instance(string ...$ids): self
    {
        return new self(...$ids);
    }

    /**
     * Constructor.
     *
     * @param string ...$ids
     */
    public function __construct(string ...$ids)
    {
        $this->ids = $ids;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container): array
    {
        return array_map([$container, 'get'], $this->ids);
    }

    /**
     * @inheritdoc
     */
    public function compilable(string $container): CompilableInterface
    {
        return new Compilable(array_map(function ($id) use ($container) {
            return new ContainerEntry($container, $id);
        }, $this->ids));
    }
}
