<?php declare(strict_types=1);

namespace Quanta\Container;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use Quanta\Container\Compilation\IndentedString;
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

        try {
            return $container->get($this->id);
        }

        catch (NotFoundExceptionInterface $e) {
            if ($this->nullable) {
                return null;
            }
            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public function compiled(string $container, callable $compiler): string
    {
        if (! $this->nullable) {
            return (string) new ContainerEntry($container, $this->id);
        }

        return implode(PHP_EOL, [
            '(function ($container) {',
            new IndentedString(implode(PHP_EOL, [
                sprintf('if ($container->has(\'%s\')) {', $this->id),
                new IndentedString(implode(PHP_EOL, [
                    sprintf('try { return %s; }', new ContainerEntry($container, $this->id)),
                    sprintf('catch (%s $e) { return null; }', NotFoundExceptionInterface::class),
                ])),
                '}',
                'return null;',
            ])),
            sprintf('})($%s)', $container),
        ]);
    }
}
