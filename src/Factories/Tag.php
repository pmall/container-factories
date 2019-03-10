<?php declare(strict_types=1);

namespace Quanta\Container\Factories;

use Psr\Container\ContainerInterface;

use Quanta\Container\Utils;

final class Tag implements CompilableFactoryInterface
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
     * @return \Quanta\Container\Factories\Tag
     */
    public static function instance(string ...$ids): Tag
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
    public function compiled(Compiler $compiler): CompiledFactory
    {
        return new CompiledFactory('container', count($this->ids) == 0
            ? 'return [];'
            : vsprintf('return array_map([$container, \'get\'], %s);', [
                Utils::arrayStr(array_map([Utils::class, 'quoted'], $this->ids)),
            ])
        );
    }
}
