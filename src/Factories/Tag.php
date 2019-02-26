<?php declare(strict_types=1);

namespace Quanta\Container\Factories;

use Psr\Container\ContainerInterface;

use Quanta\Container\Compilation\ArrayStr;

final class Tag implements CompilableFactoryInterface
{
    /**
     * The ids of the tagged container entries.
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
    public function __invoke(ContainerInterface $container, array $tagged = []): array
    {
        return array_merge($tagged, array_map([$container, 'get'], $this->ids));
    }

    /**
     * @inheritdoc
     */
    public function compiled(Compiler $compiler): CompiledFactory
    {
        return new CompiledFactory('container', 'array $tagged = []', ...[
            vsprintf('return array_merge($tagged, array_map([$container, \'get\'], %s));', [
                new ArrayStr(array_map([$this, 'quoted'], $this->ids)),
            ])
        ]);
    }

    /**
     * Return the given string with quotes.
     *
     * @param string $str
     * @return string
     */
    private function quoted(string $str): string
    {
        return sprintf('\'%s\'', $str);
    }
}
