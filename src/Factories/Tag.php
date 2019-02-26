<?php declare(strict_types=1);

namespace Quanta\Container\Factories;

use Psr\Container\ContainerInterface;

final class Tag implements CompilableFactoryInterface
{
    /**
     * The id of the tagged container entry.
     *
     * @var string
     */
    private $id;

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
    public function __invoke(ContainerInterface $container, array $tagged = []): array
    {
        return array_merge($tagged, [$container->get($this->id)]);
    }

    /**
     * @inheritdoc
     */
    public function compiled(Compiler $compiler): CompiledFactory
    {
        return new CompiledFactory('container', 'array $tagged', ...[
            vsprintf('return array_merge($tagged, [$container->get(\'%s\')]);', [
                addslashes($this->id),
            ])
        ]);
    }
}
