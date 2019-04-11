<?php declare(strict_types=1);

namespace Quanta\Container;

use Psr\Container\ContainerInterface;

use Quanta\Container\Compilation\IndentedString;
use Quanta\Container\Compilation\ContainerEntryCollection;

final class Tag implements FactoryInterface
{
    /**
     * The tagged container entry ids.
     *
     * @var string[]
     */
    private $ids;

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
    public function compiled(string $container, callable $compiler): string
    {
        return (string) new ContainerEntryCollection($container, ...$this->ids);
    }
}
